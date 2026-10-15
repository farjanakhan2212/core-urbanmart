<?php
/** @var MoneyReceipt $moneyreceipt passed by MoneyReceiptController::show() */
$mr = $moneyreceipt ?? null;
if (!$mr) {
    echo '<div class="alert alert-warning">Money receipt not found.</div>';
    return;
}

$shop_name    = GrocerySetting::get('shop_name', 'UrbanMart Grocery');
$shop_address = GrocerySetting::get('shop_address', '');
$shop_phone   = GrocerySetting::get('shop_phone', '');
$shop_email   = GrocerySetting::get('shop_email', '');
$logo_info    = Document::logo();
$logo_path    = $logo_info ? $logo_info['url'] : ($base_url . '/img/logo.jpg');

$customer = !empty($mr->customer_id) ? Customer::find((int)$mr->customer_id) : null;
$details  = MoneyReceiptDetail::Filter((int)$mr->id);

$amount_total  = (float)$mr->receipt_total;
$amount_words  = grocery_amount_in_words($amount_total);
$receipt_no    = 'MR-' . str_pad((string)$mr->id, 4, '0', STR_PAD_LEFT);
$receipt_date  = date('d M Y', strtotime((string)$mr->created_at));
$remark        = trim((string)$mr->remark);

$lines = [];
$breakup_total = 0.0;
foreach ($details as $d) {
    $prod = !empty($d->product_id) ? Product::find((int)$d->product_id) : null;
    $price = (float)$d->price;
    $qty   = (float)$d->qty;
    $lines[] = [
        'name'  => $prod ? trim(htmlspecialchars($prod->name)) : ('Product #' . (int)$d->product_id),
        'sku'   => ($prod && trim((string)$prod->barcode)) ? htmlspecialchars($prod->barcode) : '',
        'qty'   => $qty,
        'price' => $price,
        'total' => $price * $qty,
    ];
    $breakup_total += $price * $qty;
}
?>

<div class="gdoc-screen">
    <div class="gdoc-toolbar">
        <a href="<?= $base_url ?>/moneyreceipt" class="btn btn-light btn-sm"><span aria-hidden="true">&larr;</span> Back</a>
        <a href="<?= $base_url ?>/moneyreceipt/print/<?= (int)$mr->id ?>" target="_blank" rel="noopener" class="btn btn-secondary btn-sm"><span aria-hidden="true">&#128424;</span> Print</a>
        <a href="<?= $base_url ?>/moneyreceipt/pdf/<?= (int)$mr->id ?>" target="_blank" rel="noopener" class="btn btn-dark btn-sm">PDF</a>
        <a href="<?= $base_url ?>/moneyreceipt/download/<?= (int)$mr->id ?>" class="btn btn-success btn-sm">Download</a>
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
                <div class="gdoc-title">Money Receipt</div>
                <div class="gdoc-title-rule"></div>
                <div class="gdoc-meta">
                    <span><span class="gdoc-mlabel">Receipt No.</span><span class="gdoc-mvalue"><?= htmlspecialchars($receipt_no) ?></span></span>
                    <span><span class="gdoc-mlabel">Date</span><span class="gdoc-mvalue"><?= htmlspecialchars($receipt_date) ?></span></span>
                </div>
            </div>
        </div>

        <div class="gdoc-receipt-box">
            <div style="flex:1; min-width:0">
                <div class="gdoc-received-label">Payment Received From</div>
                <div class="gdoc-received-name"><?= $customer ? htmlspecialchars($customer->name) : 'Walk-in Customer' ?></div>
                <?php if ($customer && !empty($customer->mobile)): ?>
                    <div style="font-size:.82rem; color:#475569; margin-top:.15rem">Phone: <?= htmlspecialchars($customer->mobile) ?></div>
                <?php endif; ?>
            </div>
            <div class="gdoc-amount-block" style="flex:0 0 auto">
                <div class="gdoc-amount-label">Amount Received</div>
                <div class="gdoc-amount-hero"><?= grocery_money($amount_total) ?></div>
            </div>
            <div class="gdoc-amount-words" style="flex-basis:100%">
                In words: <?= htmlspecialchars($amount_words) ?>
            </div>
        </div>

        <div class="gdoc-grid-2">
            <div class="gdoc-block">
                <div class="gdoc-block-title">Payment Details</div>
                <div class="gdoc-kv"><span class="gdoc-k">Receipt No.</span><span class="gdoc-v"><?= htmlspecialchars($receipt_no) ?></span></div>
                <div class="gdoc-kv"><span class="gdoc-k">Date</span><span class="gdoc-v"><?= htmlspecialchars($receipt_date) ?></span></div>
                <div class="gdoc-kv"><span class="gdoc-k">Received From</span><span class="gdoc-v"><?= $customer ? htmlspecialchars($customer->name) : 'Walk-in Customer' ?></span></div>
            </div>
            <div class="gdoc-block">
                <div class="gdoc-block-title">Total Received</div>
                <div class="gdoc-kv"><span class="gdoc-k">Amount</span><span class="gdoc-v"><?= grocery_money($amount_total) ?></span></div>
                <?php if ($breakup_total > 0 && abs($breakup_total - $amount_total) >= 0.01): ?>
                    <div class="gdoc-kv"><span class="gdoc-k">Item Breakup</span><span class="gdoc-v"><?= grocery_money($breakup_total) ?></span></div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($lines): ?>
            <div class="gdoc-table-wrap">
                <table class="gdoc-table">
                    <thead>
                        <tr>
                            <th style="width:34px">#</th>
                            <th>Item / Description</th>
                            <th class="gdoc-num">Qty</th>
                            <th class="gdoc-num">Unit Price</th>
                            <th class="gdoc-num">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
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
                                <td class="gdoc-num"><?= grocery_money($line['total']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if ($remark !== ''): ?>
            <div class="gdoc-block" style="margin-top:1rem">
                <div class="gdoc-block-title">Payment Purpose / Note</div>
                <div class="gdoc-note" style="margin-top:0"><?= nl2br(htmlspecialchars($remark)) ?></div>
            </div>
        <?php endif; ?>

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