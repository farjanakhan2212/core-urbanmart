<?php
/** @var Invoice $invoice passed by InvoiceController::show() */
$inv = $invoice ?? null;
if (!$inv) {
    echo '<div class="alert alert-warning">Invoice not found.</div>';
    return;
}

$shop_name    = GrocerySetting::get('shop_name', 'UrbanMart Grocery');
$shop_address = GrocerySetting::get('shop_address', '');
$shop_phone   = GrocerySetting::get('shop_phone', '');
$shop_email   = GrocerySetting::get('shop_email', '');
$logo_info    = Document::logo();
$logo_path    = $logo_info ? $logo_info['url'] : ($base_url . '/img/logo.jpg');

$customer    = !empty($inv->customer_id) ? Customer::find((int)$inv->customer_id) : null;
$details     = InvoiceDetail::filter((int)$inv->id);
$grand_total = (float)$inv->invoice_total;
$paid        = (float)$inv->paid_total;
$due         = max(0, $grand_total - $paid);
$previous    = (float)$inv->previous_due;

if ($grand_total > 0 && $paid >= $grand_total) {
    $status = 'paid';
} elseif ($paid > 0) {
    $status = 'partial';
} else {
    $status = 'unpaid';
}
$status_label = ($status === 'paid') ? 'Paid' : (($status === 'partial') ? 'Partial' : 'Unpaid');

$invoice_no = 'INV-' . str_pad((string)$inv->id, 4, '0', STR_PAD_LEFT);
$inv_date   = date('d M Y', strtotime((string)$inv->created_at));
$terms      = trim((string)$inv->payment_term);

$lines    = [];
$subtotal = 0.0;
$discount = 0.0;
$vat      = 0.0;
foreach ($details as $d) {
    $prod = !empty($d->product_id) ? Product::find((int)$d->product_id) : null;
    $price = (float)$d->price;
    $qty   = (float)$d->qty;
    $lines[] = [
        'name'     => $prod ? trim(htmlspecialchars($prod->name)) : ('Product #' . (int)$d->product_id),
        'sku'      => ($prod && trim((string)$prod->barcode)) ? htmlspecialchars($prod->barcode) : '',
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
?>

<div class="gdoc-screen">
    <div class="gdoc-toolbar">
        <a href="<?= $base_url ?>/invoice" class="btn btn-light btn-sm"><span aria-hidden="true">&larr;</span> Back</a>
        <a href="<?= $base_url ?>/invoice/print/<?= (int)$inv->id ?>" target="_blank" rel="noopener" class="btn btn-secondary btn-sm"><span aria-hidden="true">&#128424;</span> Print</a>
        <a href="<?= $base_url ?>/invoice/pdf/<?= (int)$inv->id ?>" target="_blank" rel="noopener" class="btn btn-dark btn-sm">PDF</a>
        <a href="<?= $base_url ?>/invoice/download/<?= (int)$inv->id ?>" class="btn btn-success btn-sm">Download</a>
    </div>

    <div class="gdoc-sheet">
        <div class="gdoc-head">
            <div class="gdoc-brand">
                <img class="gdoc-brand-logo" src="<?= htmlspecialchars($logo_path) ?>"
                     alt="<?= htmlspecialchars($shop_name) ?>" />
                <div>
                    <div class="gdoc-brand-name"><?= htmlspecialchars($shop_name) ?></div>
                    <?php if (!empty($shop_address)): ?>
                        <div class="gdoc-brand-tagline"><?= htmlspecialchars($shop_address) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="gdoc-headlines">
                <div class="gdoc-title">Invoice</div>
                <div class="gdoc-title-rule"></div>
                <div class="gdoc-meta">
                    <span><span class="gdoc-mlabel">Invoice No.</span><span class="gdoc-mvalue"><?= htmlspecialchars($invoice_no) ?></span></span>
                    <span><span class="gdoc-mlabel">Date</span><span class="gdoc-mvalue"><?= htmlspecialchars($inv_date) ?></span></span>
                    <?php if (!empty($terms)): ?>
                        <span><span class="gdoc-mlabel">Terms</span><span class="gdoc-mvalue"><?= htmlspecialchars($terms) ?></span></span>
                    <?php endif; ?>
                    <span><span class="gdoc-mlabel">Status</span><span class="gdoc-badge bad-<?= htmlspecialchars($status) ?>"><?= htmlspecialchars($status_label) ?></span></span>
                </div>
            </div>
        </div>

        <div class="gdoc-parties">
            <div class="gdoc-box">
                <div class="gdoc-box-label">From</div>
                <div class="gdoc-box-name"><?= htmlspecialchars($shop_name) ?></div>
                <div class="gdoc-box-detail">
                    <?php if (!empty($shop_address)): ?><span class="gdoc-dline"><?= htmlspecialchars($shop_address) ?></span><?php endif; ?>
                    <?php if (!empty($shop_phone)): ?><span class="gdoc-dline">Phone: <?= htmlspecialchars($shop_phone) ?></span><?php endif; ?>
                    <?php if (!empty($shop_email)): ?><span class="gdoc-dline"><?= htmlspecialchars($shop_email) ?></span><?php endif; ?>
                </div>
            </div>
            <div class="gdoc-box">
                <div class="gdoc-box-label">Bill To</div>
                <?php if ($customer): ?>
                    <div class="gdoc-box-name"><?= htmlspecialchars($customer->name) ?></div>
                    <div class="gdoc-box-detail">
                        <?php if (!empty($customer->address)): ?><span class="gdoc-dline"><?= htmlspecialchars($customer->address) ?></span><?php endif; ?>
                        <?php if (!empty($customer->mobile)): ?><span class="gdoc-dline">Phone: <?= htmlspecialchars($customer->mobile) ?></span><?php endif; ?>
                        <?php if (!empty($customer->email)): ?><span class="gdoc-dline"><?= htmlspecialchars($customer->email) ?></span><?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="gdoc-box-detail"><span class="gdoc-dline">Walk-in Customer</span></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="gdoc-table-wrap">
            <table class="gdoc-table">
                <thead>
                    <tr>
                        <th style="width:34px">#</th>
                        <th>Item / Description</th>
                        <th class="gdoc-num">Qty</th>
                        <th class="gdoc-num">Unit Price</th>
                        <th class="gdoc-num">Discount</th>
                        <th class="gdoc-num">VAT</th>
                        <th class="gdoc-num">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$lines): ?>
                        <tr><td colspan="7">No line items found for this invoice.</td></tr>
                    <?php endif; ?>
                    <?php $i = 1; foreach ($lines as $line): ?>
                        <tr>
                            <td class="gdoc-num" style="color:#94a3b8"><?= $i++ ?></td>
                            <td>
                                <span class="gdoc-item-name"><?= $line['name'] ?></span>
                                <?php if ($line['sku'] !== ''): ?>
                                    <span class="gdoc-item-sku">SKU: <?= $line['sku'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="gdoc-num"><?= number_format($line['qty'], 2) ?></td>
                            <td class="gdoc-num"><?= grocery_money($line['price']) ?></td>
                            <td class="gdoc-num"><?= grocery_money($line['discount']) ?></td>
                            <td class="gdoc-num"><?= grocery_money($line['vat']) ?></td>
                            <td class="gdoc-num"><?= grocery_money($line['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="gdoc-totals-row">
            <table class="gdoc-totals">
                <tbody>
                    <tr><td class="gdoc-t-label">Subtotal</td><td class="gdoc-t-value"><?= grocery_money($subtotal) ?></td></tr>
                    <?php if ($discount != 0): ?>
                        <tr><td class="gdoc-t-label">Discount</td><td class="gdoc-t-value">- <?= grocery_money($discount) ?></td></tr>
                    <?php endif; ?>
                    <?php if ($vat != 0): ?>
                        <tr><td class="gdoc-t-label">VAT / Tax</td><td class="gdoc-t-value"><?= grocery_money($vat) ?></td></tr>
                    <?php endif; ?>
                    <?php if ($previous > 0): ?>
                        <tr><td class="gdoc-t-label">Previous Due</td><td class="gdoc-t-value"><?= grocery_money($previous) ?></td></tr>
                    <?php endif; ?>
                    <tr class="gdoc-t-total"><td class="gdoc-t-label">Grand Total</td><td class="gdoc-t-value"><?= grocery_money($grand_total) ?></td></tr>
                </tbody>
            </table>
        </div>

        <div class="gdoc-grid-2">
            <div class="gdoc-block">
                <div class="gdoc-block-title">Payment Information</div>
                <div class="gdoc-kv"><span class="gdoc-k">Payment Status</span><span class="gdoc-v"><?= htmlspecialchars($status_label) ?></span></div>
                <?php if (!empty($terms)): ?>
                    <div class="gdoc-kv"><span class="gdoc-k">Payment Term</span><span class="gdoc-v"><?= htmlspecialchars($terms) ?></span></div>
                <?php endif; ?>
                <div class="gdoc-kv"><span class="gdoc-k">Paid Amount</span><span class="gdoc-v"><?= grocery_money($paid) ?></span></div>
                <div class="gdoc-kv"><span class="gdoc-k">Amount Due</span><span class="gdoc-v"><?= grocery_money($due) ?></span></div>
            </div>
            <?php $remark = trim((string)$inv->remark); if ($remark !== ''): ?>
                <div class="gdoc-block">
                    <div class="gdoc-block-title">Notes</div>
                    <div class="gdoc-note"><?= nl2br(htmlspecialchars($remark)) ?></div>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($shop_name)): ?>
            <div class="gdoc-sign">
                <div class="gdoc-sign-item">
                    <div class="gdoc-sign-line">Customer Signature</div>
                    <div class="gdoc-sign-cap"><?= $customer ? htmlspecialchars($customer->name) : 'Customer' ?></div>
                </div>
                <div class="gdoc-sign-item">
                    <div class="gdoc-sign-line">Authorized Signature</div>
                    <div class="gdoc-sign-cap"><?= htmlspecialchars($shop_name) ?></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="gdoc-footer">
            <?php if ($shop_phone !== '' || $shop_email !== ''): ?>
                <div class="gdoc-footer-contact">
                    <?php if ($shop_phone !== ''): ?><span><?= htmlspecialchars($shop_phone) ?></span><?php endif; ?>
                    <?php if ($shop_phone !== '' && $shop_email !== ''): ?><span class="gdoc-fsep">|</span><?php endif; ?>
                    <?php if ($shop_email !== ''): ?><span><?= htmlspecialchars($shop_email) ?></span><?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="gdoc-thanks">Thank you for your business!</div>
        </div>
    </div>
</div>