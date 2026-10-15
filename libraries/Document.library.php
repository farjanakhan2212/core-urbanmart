<?php
/**
 * Document rendering service for Invoice / Money Receipt / POS Sale documents.
 *
 * Centralises the data acquisition (customer / line items / totals) and the
 * rendering of the printable + PDF representation of a document so the
 * controller, the on-screen view and the PDF generator all agree on one
 * source of truth.
 *
 * The company logo is resolved through a single {@see Document::logo()} helper
 * so every representation (browser preview, printout and PDF) shows the same
 * configured logo. For PDFs the image is embedded as a base64 data URI which
 * Dompdf/XAMPP render reliably (Dompdf ignores file:// image URLs and needs the
 * GD extension to embed raster images at all).
 *
 * Print / PDF layout intentionally uses plain tables (instead of flex/grid)
 * so it renders correctly in Dompdf as well as in a browser print dialog.
 */
class Document
{
    /** Shop identity pulled from the existing grocery settings. */
    public static function shop()
    {
        return [
            'name'    => GrocerySetting::get('shop_name', 'UrbanMart Grocery'),
            'address' => GrocerySetting::get('shop_address', ''),
            'phone'   => GrocerySetting::get('shop_phone', ''),
            'email'   => GrocerySetting::get('shop_email', ''),
        ];
    }

    /* ------------------------------------------------------------------ */
    /* Company logo resolution                                             */
    /* ------------------------------------------------------------------ */

    /**
     * Resolve the configured company logo to a real, readable image file.
     *
     * Priority (first existing, valid image wins):
     *   1. Grocery settings key  shop_logo / company_logo / logo
     *   2. core_company.logo (the DB company record for this shop)
     *   3. configs/app.config.php  $logo  (app level configuration)
     *   4. urbanmart.png  (asset shipped with the app)
     *
     * @return array|null ['file'=>abs fs path, 'url'=>web url, 'mime'=>..., 'name'=>...]
     */
    public static function logo()
    {
        global $base_url, $logo, $db, $tx;

        $root = str_replace('\\', '/', dirname(__DIR__));
        $candidates = [];

        // 1. Explicit grocery setting (if the shop ever stores one).
        foreach (['shop_logo', 'company_logo', 'logo'] as $key) {
            $v = trim((string)GrocerySetting::get($key, ''));
            if ($v !== '') $candidates[] = $v;
        }

        // 2. Company table logo, preferring the row matching the shop name.
        $shopName = trim((string)GrocerySetting::get('shop_name', ''));
        if ($db && !empty($tx)) {
            $rows = [];
            $res = @$db->query("SELECT name, logo FROM {$tx}company WHERE logo IS NOT NULL AND logo <> ''");
            if ($res) {
                while ($r = $res->fetch_assoc()) $rows[] = $r;
            }
            if ($rows && $shopName !== '') {
                usort($rows, function ($a, $b) use ($shopName) {
                    $am = stripos($shopName, (string)$a['name']) !== false ? 0 : 1;
                    $bm = stripos($shopName, (string)$b['name']) !== false ? 0 : 1;
                    return $am <=> $bm;
                });
            }
            foreach ($rows as $r) $candidates[] = $r['logo'];
        }

        // 3. Application level configured logo (configs/app.config.php).
        if (!empty($logo)) {
            $candidates[] = ltrim(str_replace('\\', '/', (string)$logo), '/');
            $candidates[] = basename((string)$logo);
        }

        // 4. Known default asset shipped with the application.
        $candidates[] = 'urbanmart.png';

        foreach ($candidates as $cand) {
            $file = self::resolveImagePath((string)$cand, $root, (string)$base_url);
            if ($file !== null) {
                $mime = @getimagesize($file);
                return [
                    'file' => $file,
                    'url'  => $base_url . '/img/' . basename($file),
                    'mime' => $mime ? (string)$mime['mime'] : 'image/png',
                    'name' => basename($file),
                ];
            }
        }
        return null;
    }

    /**
     * Normalise a configured logo reference to an absolute filesystem path.
     * Returns null when the reference is not a readable, supported image.
     */
    protected static function resolveImagePath($cand, $root, $baseUrl)
    {
        $cand = trim($cand);
        if ($cand === '') return null;
        $cand = str_replace('\\', '/', $cand);

        // Drop the web base url if present so /Farjana/.../img/x.png works.
        if ($baseUrl !== '' && strpos($cand, $baseUrl) === 0) {
            $cand = ltrim(substr($cand, strlen($baseUrl)), '/');
        }
        $cand = preg_replace('#^https?://[^/]+#i', '', $cand);
        $cand = ltrim($cand, '/');

        $tries = [];
        $tries[] = $root . '/' . $cand;
        $tries[] = $root . '/img/' . basename($cand);
        if (strpos($cand, 'img/') !== 0) {
            $tries[] = $root . '/img/' . $cand;
        }

        $allowed = ['image/png', 'image/jpeg', 'image/gif', 'image/webp', 'image/bmp', 'image/svg+xml'];
        foreach ($tries as $path) {
            if (!is_file($path) || !is_readable($path)) continue;
            $info = @getimagesize($path);
            if (!$info || empty($info['mime'])) continue;
            if (!in_array($info['mime'], $allowed, true)) continue;
            return str_replace('\\', '/', $path);
        }
        return null;
    }

    /** Render a clear diagnostic block when no logo could be resolved. */
    protected static function logoDiagnostic()
    {
        return '<span style="color:#b91c1c;font-size:10px">&#9888; Company logo not found. '
            . 'Set core_grocery_settings.logo or core_company.logo.</span>';
    }

    /* ------------------------------------------------------------------ */
    /* Data acquisition                                                    */
    /* ------------------------------------------------------------------ */

    public static function invoice($id)
    {
        $id = (int)$id;
        if ($id <= 0) return null;
        $inv = Invoice::find($id);
        if (!$inv) return null;

        $customer = !empty($inv->customer_id) ? Customer::find((int)$inv->customer_id) : null;

        $lines = [];
        $subtotal = 0.0;
        $discount = 0.0;
        $vat = 0.0;
        foreach (InvoiceDetail::filter($id) as $d) {
            $prod = !empty($d->product_id) ? Product::find((int)$d->product_id) : null;
            $price = (float)$d->price;
            $qty = (float)$d->qty;
            $lines[] = [
                'name'     => $prod ? trim((string)$prod->name) : ('Product #' . (int)$d->product_id),
                'sku'      => ($prod && trim((string)$prod->barcode)) ? trim((string)$prod->barcode) : '',
                'qty'      => $qty,
                'price'    => $price,
                'discount' => (float)$d->discount,
                'vat'      => (float)$d->vat,
                'total'    => $price * $qty,
            ];
            $subtotal += $price * $qty;
            $discount += (float)$d->discount;
            $vat      += (float)$d->vat;
        }

        $grand = (float)$inv->invoice_total;
        $paid  = (float)$inv->paid_total;
        $due   = max(0, $grand - $paid);
        if ($grand > 0 && $paid >= $grand) {
            $status = 'paid';
        } elseif ($paid > 0) {
            $status = 'partial';
        } else {
            $status = 'unpaid';
        }

        return [
            'type'         => 'invoice',
            'route'        => 'invoice',
            'view'         => 'invoice/show',
            'id'           => $id,
            'doc_no'       => 'INV-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT),
            'title'        => 'Invoice',
            'date'         => date('d M Y', strtotime((string)$inv->created_at)),
            'terms'        => trim((string)$inv->payment_term),
            'status'       => $status,
            'status_label' => ucfirst($status),
            'remark'       => trim((string)$inv->remark),
            'customer'     => self::customerArray($customer),
            'lines'        => $lines,
            'subtotal'     => $subtotal,
            'discount'     => $discount,
            'vat'          => $vat,
            'grand_total'  => $grand,
            'paid'         => $paid,
            'due'          => $due,
            'previous'     => (float)$inv->previous_due,
        ];
    }

    public static function receipt($id)
    {
        $id = (int)$id;
        if ($id <= 0) return null;
        $mr = MoneyReceipt::find($id);
        if (!$mr) return null;

        $customer = !empty($mr->customer_id) ? Customer::find((int)$mr->customer_id) : null;

        $lines = [];
        $breakup = 0.0;
        foreach (MoneyReceiptDetail::Filter($id) as $d) {
            $prod = !empty($d->product_id) ? Product::find((int)$d->product_id) : null;
            $price = (float)$d->price;
            $qty = (float)$d->qty;
            $lines[] = [
                'name'  => $prod ? trim((string)$prod->name) : ('Product #' . (int)$d->product_id),
                'sku'   => ($prod && trim((string)$prod->barcode)) ? trim((string)$prod->barcode) : '',
                'qty'   => $qty,
                'price' => $price,
                'total' => $price * $qty,
            ];
            $breakup += $price * $qty;
        }

        $total = (float)$mr->receipt_total;

        return [
            'type'         => 'receipt',
            'route'        => 'moneyreceipt',
            'view'         => 'moneyreceipt/show',
            'id'           => $id,
            'doc_no'       => 'MR-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT),
            'title'        => 'Money Receipt',
            'date'         => date('d M Y', strtotime((string)$mr->created_at)),
            'remark'       => trim((string)$mr->remark),
            'customer'     => self::customerArray($customer),
            'lines'        => $lines,
            'amount'       => $total,
            'amount_words' => function_exists('grocery_amount_in_words') ? grocery_amount_in_words($total) : '',
            'breakup'      => $breakup,
        ];
    }

    /** POS / grocery sale document (source: core_grocery_sales). */
    public static function sale($id)
    {
        $id = (int)$id;
        if ($id <= 0) return null;
        $sale = GrocerySale::withDetails($id);
        if (!$sale) return null;

        $lines = [];
        $subtotal = 0.0;
        $discount = 0.0;
        $vat = 0.0;
        foreach (($sale['lines'] ?? []) as $l) {
            $l = is_array($l) ? $l : (array)$l;
            $qty    = (float)($l['quantity'] ?? 0);
            $price  = (float)($l['selling_price'] ?? 0);
            $dis    = (float)($l['discount'] ?? 0);
            $lineVat = (float)($l['vat'] ?? 0);
            $lineTotal = $qty * $price - $dis + $lineVat;
            $lines[] = [
                'name'     => trim((string)($l['name'] ?? ('Product #' . (int)($l['product_id'] ?? 0)))),
                'sku'      => trim((string)($l['sku'] ?? '')),
                'qty'      => $qty,
                'price'    => $price,
                'discount' => $dis,
                'vat'      => $lineVat,
                'total'    => $lineTotal,
            ];
            $subtotal += $qty * $price;
            $discount += $dis;
            $vat      += $lineVat;
        }

        $grand = (float)($sale['grand_total'] ?? 0);
        $paid  = (float)($sale['paid_amount'] ?? 0);
        $due   = (float)($sale['due_amount'] ?? 0);
        $status = (string)($sale['payment_status'] ?? 'unpaid');

        $customer = null;
        if (!empty($sale['customer_id']) || !empty($sale['customer_name'])) {
            $customer = [
                'name'    => trim((string)($sale['customer_name'] ?? 'Walk-in Customer')),
                'address' => trim((string)($sale['customer_address'] ?? '')),
                'phone'   => trim((string)($sale['customer_phone'] ?? '')),
                'email'   => '',
            ];
        }

        return [
            'type'         => 'sale',
            'route'        => 'GrocerySale',
            'view'         => 'GrocerySale/invoice',
            'id'           => $id,
            'doc_no'       => (string)($sale['invoice_no'] ?? ('SALE-' . $id)),
            'title'        => 'Sale Invoice',
            'date'         => date('d M Y', strtotime((string)($sale['sale_date'] ?? $sale['created_at'] ?? 'now'))),
            'terms'        => trim((string)($sale['payment_method_name'] ?? '')),
            'status'       => $status,
            'status_label' => ucfirst($status),
            'remark'       => trim((string)($sale['remark'] ?? '')),
            'customer'     => $customer,
            'lines'        => $lines,
            'subtotal'     => $subtotal,
            'discount'     => $discount,
            'vat'          => $vat,
            'grand_total'  => $grand,
            'paid'         => $paid,
            'due'          => $due,
            'previous'     => 0.0,
        ];
    }

    protected static function customerArray($c)
    {
        if (!$c) return null;
        return [
            'name'    => trim((string)$c->name),
            'address' => isset($c->address) ? trim((string)$c->address) : '',
            'phone'   => isset($c->mobile) ? trim((string)$c->mobile) : '',
            'email'   => isset($c->email) ? trim((string)$c->email) : '',
        ];
    }

    /* ------------------------------------------------------------------ */
    /* Standalone pages (print + pdf)                                      */
    /* ------------------------------------------------------------------ */

    public static function printPage($type, array $d)
    {
        global $base_url;
        $shop = self::shop();
        $info = self::logo();
        $logo = $info ? $info['url'] : '';
        $toolbar = self::screenToolbar($d, $base_url);
        $body = self::body($d, $logo, $shop);
        return self::wrap($d['doc_no'], self::css(false), $toolbar . $body, true);
    }

    public static function pdfHtml($type, array $d)
    {
        $shop = self::shop();
        $info = self::logo();

        // Embed the logo as a data URI: Dompdf ignores file:// image URLs and
        // data URIs sidestep chroot / permission issues entirely.
        $logo = '';
        if ($info) {
            $data = @file_get_contents($info['file']);
            if ($data !== false) {
                $logo = 'data:' . $info['mime'] . ';base64,' . base64_encode($data);
            }
        }
        $body = self::body($d, $logo, $shop);
        return self::wrap($d['doc_no'], self::css(true), $body, false);
    }

    public static function pdf($html, $filename, $download = true)
    {
        $root = dirname(__DIR__);
        require_once $root . '/vendor/autoload.php';

        // Dompdf needs the GD extension to embed raster images (logos). Fail
        // loudly instead of silently generating a logo-less document.
        if (!extension_loaded('gd') && !extension_loaded('imagick')) {
            throw new \RuntimeException(
                'The PHP GD extension is required to embed the company logo in PDFs. '
                . 'Enable "extension=gd" in php.ini and restart Apache.'
            );
        }

        $options = new \Dompdf\Options();
        $options->setChroot($root);
        $options->setFontDir($root . '/storage/dompdf/fonts');
        $options->setFontCache($root . '/storage/dompdf/cache');
        $options->setIsRemoteEnabled(false);
        $options->setIsHtml5ParserEnabled(true);
        $options->setDefaultFont('DejaVu Sans');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setBasePath($root . '/');

        // Register the bundled Bangla-capable font programmatically. Dompdf
        // resolves @font-face TTFs unreliably, whereas registerFont installs
        // the font into the writable font cache dir and works consistently.
        $fontFile = $root . '/assets/fonts/kalpurush.ttf';
        if (file_exists($fontFile)) {
            try {
                $dompdf->getFontMetrics()->registerFont(
                    ['family' => 'kalpurush', 'style' => 'normal', 'weight' => 'normal'],
                    $fontFile
                );
            } catch (\Throwable $e) {
                // Fall back to the default font if registration fails.
            }
        }

        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        $dompdf->stream($filename, ['Attachment' => (bool)$download]);
        exit;
    }

    /* ------------------------------------------------------------------ */
    /* Markup builders                                                     */
    /* ------------------------------------------------------------------ */

    protected static function wrap($title, $css, $inner, $autoPrint)
    {
        $h = '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">'
            . '<meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>' . self::e($title) . '</title>'
            . '<style>' . $css . '</style>'
            . '</head><body>' . $inner;
        if ($autoPrint) {
            $h .= '<script>window.addEventListener("load",function(){setTimeout(function(){window.print();},250);});</script>';
        }
        $h .= '</body></html>';
        return $h;
    }

    protected static function screenToolbar(array $d, $base_url)
    {
        $base = $d['route'] ?? ($d['type'] === 'receipt' ? 'moneyreceipt' : 'invoice');
        return '<div class="pdoc-toolbar">'
            . '<a class="pdoc-btn" href="' . self::e($base_url . '/' . $base) . '">&larr; Back</a>'
            . '<button type="button" class="pdoc-btn" onclick="window.print()">&#128424; Print</button>'
            . '<a class="pdoc-btn" href="' . self::e($base_url . '/' . $base . '/pdf/' . $d['id']) . '" target="_blank" rel="noopener">PDF</a>'
            . '<a class="pdoc-btn" href="' . self::e($base_url . '/' . $base . '/download/' . $d['id']) . '">Download</a>'
            . '</div>';
    }

    protected static function body(array $d, $logo, array $shop)
    {
        $logoInfo = self::logo();
        $isInvoice = $d['type'] !== 'receipt';
        $rows = '';
        if ($isInvoice) {
            $i = 1;
            if (!$d['lines']) {
                $rows .= '<tr><td colspan="7" class="pdoc-muted">No line items recorded.</td></tr>';
            }
            foreach ($d['lines'] as $l) {
                $rows .= '<tr>'
                    . '<td class="num">' . $i++ . '</td>'
                    . '<td>' . self::e($l['name'])
                    . ($l['sku'] !== '' ? '<span class="pdoc-sku">SKU: ' . self::e($l['sku']) . '</span>' : '')
                    . '</td>'
                    . '<td class="num">' . number_format($l['qty'], 2) . '</td>'
                    . '<td class="num">' . self::e(grocery_money($l['price'])) . '</td>'
                    . '<td class="num">' . self::e(grocery_money($l['discount'])) . '</td>'
                    . '<td class="num">' . self::e(grocery_money($l['vat'])) . '</td>'
                    . '<td class="num">' . self::e(grocery_money($l['total'])) . '</td>'
                    . '</tr>';
            }
        }

        $html = '<div class="pdoc">';

        // ---- header ----
        $html .= '<table class="pdoc-top"><tr>'
            . '<td class="pdoc-brand">'
            . ($logo !== ''
                ? '<img src="' . self::e($logo) . '" class="pdoc-logo" alt="' . self::e($shop['name']) . '">'
                : self::logoDiagnostic())
            . '<span class="pdoc-brandname">' . self::e($shop['name']) . '</span>'
            . ($shop['address'] !== '' ? '<span class="pdoc-brandsub">' . self::e($shop['address']) . '</span>' : '')
            . '</td>'
            . '<td class="pdoc-headlines">'
            . '<div class="pdoc-title">' . self::e(strtoupper($d['title'])) . '</div>'
            . '<div class="pdoc-meta">'
            . '<span><b>No:</b> ' . self::e($d['doc_no']) . '</span>'
            . '<span><b>Date:</b> ' . self::e($d['date']) . '</span>'
            . ($isInvoice ? '<span><b>Status:</b> ' . self::e($d['status_label']) . '</span>' : '')
            . '</div>'
            . '</td></tr></table>';

        // ---- parties ----
        $cust = $d['customer'];
        $html .= '<table class="pdoc-parties"><tr>'
            . '<td class="pdoc-parties-gap"></td>'
            . '<td>'
            . '<div class="pdoc-label">From</div>'
            . '<div class="pdoc-name">' . self::e($shop['name']) . '</div>'
            . '<div class="pdoc-detail">'
            . ($shop['address'] !== '' ? self::e($shop['address']) . '<br>' : '')
            . ($shop['phone'] !== '' ? 'Phone: ' . self::e($shop['phone']) . '<br>' : '')
            . ($shop['email'] !== '' ? self::e($shop['email']) : '')
            . '</div>'
            . '</td>'
            . '<td>'
            . '<div class="pdoc-label">' . ($isInvoice ? 'Bill To' : 'Received From') . '</div>'
            . ($cust
                ? '<div class="pdoc-name">' . self::e($cust['name']) . '</div>'
                    . '<div class="pdoc-detail">'
                    . ($cust['address'] !== '' ? self::e($cust['address']) . '<br>' : '')
                    . ($cust['phone'] !== '' ? 'Phone: ' . self::e($cust['phone']) . '<br>' : '')
                    . ($cust['email'] !== '' ? self::e($cust['email']) : '')
                    . '</div>'
                : '<div class="pdoc-detail">Walk-in Customer</div>')
            . '</td>'
            . '</tr></table>';

        if ($isInvoice) {
            // ---- items ----
            $html .= '<table class="pdoc-items"><thead><tr>'
                . '<th style="width:26px">#</th><th>Item / Description</th>'
                . '<th class="num">Qty</th><th class="num">Unit Price</th>'
                . '<th class="num">Discount</th><th class="num">VAT</th><th class="num">Total</th>'
                . '</tr></thead><tbody>' . $rows . '</tbody></table>';

            // ---- totals + payment/notes ----
            $html .= '<table class="pdoc-totwrap"><tr>'
                . '<td class="pdoc-paycell">'
                . '<div class="pdoc-label">Payment Information</div>'
                . '<table class="pdoc-kv">'
                . '<tr><td>Payment Status</td><td>' . self::e($d['status_label']) . '</td></tr>'
                . (!empty($d['terms']) ? '<tr><td>Payment Term</td><td>' . self::e($d['terms']) . '</td></tr>' : '')
                . '<tr><td>Paid Amount</td><td>' . self::e(grocery_money($d['paid'])) . '</td></tr>'
                . '<tr><td>Amount Due</td><td>' . self::e(grocery_money($d['due'])) . '</td></tr>'
                . '</table>'
                . ($d['remark'] !== ''
                    ? '<div class="pdoc-label" style="margin-top:8px">Notes</div><div class="pdoc-note">' . nl2br(self::e($d['remark'])) . '</div>'
                    : '')
                . '</td>'
                . '<td class="pdoc-totcell">'
                . '<table class="pdoc-totals">'
                . '<tr><td>Subtotal</td><td class="num">' . self::e(grocery_money($d['subtotal'])) . '</td></tr>'
                . ($d['discount'] != 0 ? '<tr><td>Discount</td><td class="num">- ' . self::e(grocery_money($d['discount'])) . '</td></tr>' : '')
                . ($d['vat'] != 0 ? '<tr><td>VAT / Tax</td><td class="num">' . self::e(grocery_money($d['vat'])) . '</td></tr>' : '')
                . ($d['previous'] > 0 ? '<tr><td>Previous Due</td><td class="num">' . self::e(grocery_money($d['previous'])) . '</td></tr>' : '')
                . '<tr class="pdoc-grand"><td>Grand Total</td><td class="num">' . self::e(grocery_money($d['grand_total'])) . '</td></tr>'
                . '</table>'
                . '</td></tr></table>';
        } else {
            // ---- money receipt amount hero ----
            $html .= '<table class="pdoc-hero"><tr>'
                . '<td class="pdoc-hero-left">'
                . '<div class="pdoc-label">Amount Received</div>'
                . '<div class="pdoc-hero-amount">' . self::e(grocery_money($d['amount'])) . '</div>'
                . '<div class="pdoc-hero-words">In words: ' . self::e($d['amount_words']) . '</div>'
                . '</td>'
                . '<td class="pdoc-hero-right">'
                . '<div class="pdoc-label">Receipt</div>'
                . '<div class="pdoc-detail">No: ' . self::e($d['doc_no']) . '<br>Date: ' . self::e($d['date']) . '</div>'
                . '</td>'
                . '</tr></table>';

            if ($d['lines']) {
                $i = 1;
                $rows = '';
                foreach ($d['lines'] as $l) {
                    $rows .= '<tr>'
                        . '<td class="num">' . $i++ . '</td>'
                        . '<td>' . self::e($l['name'])
                        . ($l['sku'] !== '' ? '<span class="pdoc-sku">SKU: ' . self::e($l['sku']) . '</span>' : '')
                        . '</td>'
                        . '<td class="num">' . number_format($l['qty'], 2) . '</td>'
                        . '<td class="num">' . self::e(grocery_money($l['price'])) . '</td>'
                        . '<td class="num">' . self::e(grocery_money($l['total'])) . '</td>'
                        . '</tr>';
                }
                $html .= '<table class="pdoc-items"><thead><tr>'
                    . '<th style="width:26px">#</th><th>Item / Description</th>'
                    . '<th class="num">Qty</th><th class="num">Unit Price</th><th class="num">Amount</th>'
                    . '</tr></thead><tbody>' . $rows . '</tbody></table>';
            }

            if ($d['remark'] !== '') {
                $html .= '<div class="pdoc-label" style="margin-top:10px">Payment Purpose / Note</div>'
                    . '<div class="pdoc-note">' . nl2br(self::e($d['remark'])) . '</div>';
            }
        }

        // ---- signatures ----
        $custName = $d['customer'] ? $d['customer']['name'] : 'Customer';
        $html .= '<table class="pdoc-sign"><tr>'
            . '<td>Customer Signature<span class="pdoc-sign-cap">' . self::e($custName) . '</span></td>'
            . '<td>Authorized Signature<span class="pdoc-sign-cap">' . self::e($shop['name']) . '</span></td>'
            . '</tr></table>';

        // ---- footer ----
        $contact = trim(implode('  |  ', array_filter([$shop['phone'], $shop['email']])));
        $html .= '<div class="pdoc-footer">'
            . ($contact !== '' ? '<div>' . self::e($contact) . '</div>' : '')
            . '<div class="pdoc-thanks">Thank you for your business!</div>'
            . '</div>';

        $html .= '</div>';
        return $html;
    }

    /* ------------------------------------------------------------------ */
    /* Stylesheet                                                          */
    /* ------------------------------------------------------------------ */

    protected static function css($forPdf)
    {
        // The PDF renderer registers kalpurush via FontMetrics, so it does not
        // need an @font-face rule; the browser print page does.
        $face = '';
        if (!$forPdf) {
            global $base_url;
            if (file_exists(dirname(__DIR__) . '/assets/fonts/kalpurush.ttf')) {
                $webFont = $base_url . '/assets/fonts/kalpurush.ttf';
                $face = "@font-face{font-family:'Kalpurush';src:url('" . $webFont . "') format('truetype');font-weight:normal;font-style:normal;}";
            }
        }

        return $face . '
        *{box-sizing:border-box;}
        body{margin:0;background:#eef2f7;}
        .pdoc{font-family:"Kalpurush","DejaVu Sans",Arial,sans-serif;color:#1f2a37;font-size:12px;background:#fff;max-width:800px;margin:0 auto;padding:26px 30px;}
        .pdoc table{border-collapse:collapse;width:100%;}
        .pdoc .num{text-align:right;white-space:nowrap;}
        .pdoc-muted{color:#94a3b8;}
        .pdoc-top{border-bottom:2px solid #e5e9ef;margin-bottom:14px;}
        .pdoc-top td{vertical-align:top;padding-bottom:10px;}
        .pdoc-brand{width:58%;}
        .pdoc-logo{width:52px;height:52px;object-fit:contain;vertical-align:middle;margin-right:10px;}
        .pdoc-brandname{font-size:17px;font-weight:bold;vertical-align:middle;}
        .pdoc-brandsub{font-size:11px;color:#64748b;display:block;margin-top:2px;}
        .pdoc-headlines{text-align:right;}
        .pdoc-title{font-size:22px;font-weight:bold;color:#0b5c3a;letter-spacing:1px;}
        .pdoc-meta{font-size:11px;color:#334155;margin-top:4px;}
        .pdoc-meta span{display:inline-block;margin-left:12px;}
        .pdoc-parties td{width:50%;vertical-align:top;padding:9px 11px;border:1px solid #e5e9ef;background:#f7f9fb;}
        .pdoc-parties .pdoc-parties-gap{width:12px;border:0;background:transparent;padding:0;}
        .pdoc-label{font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:1px;color:#94a3b8;margin-bottom:4px;}
        .pdoc-name{font-weight:bold;font-size:13px;margin-bottom:2px;}
        .pdoc-detail{font-size:11px;color:#475569;line-height:1.5;}
        .pdoc-items{margin-top:12px;}
        .pdoc-items th{background:#e9f5ef;color:#0b5c3a;font-size:10px;text-transform:uppercase;letter-spacing:.5px;padding:7px 8px;text-align:left;border-bottom:2px solid #cfebdd;}
        .pdoc-items td{padding:7px 8px;border-bottom:1px solid #e5e9ef;vertical-align:top;}
        .pdoc-sku{display:block;font-size:10px;color:#94a3b8;}
        .pdoc-totwrap{margin-top:12px;}
        .pdoc-paycell{vertical-align:top;width:55%;padding-right:14px;}
        .pdoc-totcell{vertical-align:top;width:45%;}
        .pdoc-kv td{padding:3px 0;font-size:11px;}
        .pdoc-kv td:first-child{color:#64748b;}
        .pdoc-kv td:last-child{text-align:right;font-weight:bold;}
        .pdoc-totals{border:1px solid #e5e9ef;}
        .pdoc-totals td{padding:6px 10px;border-bottom:1px solid #e5e9ef;font-size:11px;}
        .pdoc-totals td:first-child{color:#64748b;}
        .pdoc-totals td:last-child{text-align:right;font-weight:bold;}
        .pdoc-totals .pdoc-grand td{background:#e9f5ef;color:#0b5c3a;font-size:13px;font-weight:bold;border-bottom:0;border-top:2px solid #0f7a4d;}
        .pdoc-note{background:#fffbeb;border:1px solid #fde68a;padding:7px 9px;font-size:11px;color:#78350f;line-height:1.5;}
        .pdoc-hero{margin-top:14px;border:1px solid #cfebdd;border-left:5px solid #0f7a4d;background:#e9f5ef;}
        .pdoc-hero td{vertical-align:top;padding:12px 14px;}
        .pdoc-hero-right{text-align:right;width:35%;}
        .pdoc-hero-amount{font-size:24px;font-weight:bold;color:#0b5c3a;}
        .pdoc-hero-words{font-size:11px;color:#475569;font-style:italic;margin-top:4px;}
        .pdoc-sign{margin-top:34px;}
        .pdoc-sign td{width:50%;text-align:center;font-size:11px;color:#475569;border-top:1px dashed #94a3b8;padding-top:6px;font-weight:bold;}
        .pdoc-sign-cap{display:block;font-size:10px;text-transform:uppercase;letter-spacing:1px;color:#94a3b8;font-weight:normal;margin-top:2px;}
        .pdoc-footer{margin-top:18px;border-top:1px solid #e5e9ef;padding-top:9px;text-align:center;font-size:10px;color:#64748b;}
        .pdoc-thanks{font-weight:bold;color:#0b5c3a;margin-top:3px;}
        .pdoc-toolbar{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:8px;max-width:800px;margin:0 auto;padding:14px 0 6px;}
        .pdoc-btn{display:inline-block;padding:8px 14px;border:1px solid #cbd5e1;border-radius:6px;background:#fff;color:#1f2a37;font-size:13px;font-weight:600;text-decoration:none;cursor:pointer;}
        .pdoc-btn:hover{background:#f1f5f9;}
        @page{size:A4 portrait;margin:12mm;}
        @media print{body{background:#fff;} .pdoc{box-shadow:none;max-width:100%;padding:0;} .pdoc-toolbar{display:none;}}
        ';
    }

    protected static function e($s)
    {
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    }
}
