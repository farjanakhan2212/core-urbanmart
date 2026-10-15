<?php
/**
 * GrocerySale View page (POS "Sales / Invoices" detail).
 * @var obj $grocerysale passed by GrocerySaleController::invoice()/show()
 */
$data = $grocerysale ?? null;
$s = $data ? ($data->sale ?? null) : null;
if (!$s) { echo 'Invoice not found'; return; }
$s = is_object($s) ? (array)$s : (array)$s;
$shop = GrocerySetting::get('shop_name', 'UrbanMart Grocery');
$currency = GrocerySetting::get('currency_symbol', 'Tk');
$saleLines = isset($s['lines']) ? (array)$s['lines'] : [];
$sid = (int)($s['id'] ?? 0);
$logoInfo = Document::logo();
$logo = $logoInfo ? $logoInfo['url'] : '';
function _gdoc_m($v){ return number_format((float)$v, 2); }
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <a class="btn btn-sm btn-light mr-2 mb-2" href="<?= $base_url ?>/GrocerySale"><span aria-hidden="true">&larr;</span> Back</a>
            <a class="btn btn-sm btn-secondary mb-2 mr-2" href="<?= $base_url ?>/GrocerySale/print/<?= $sid ?>" target="_blank" rel="noopener"><i class="fa fa-print" aria-hidden="true"></i> Print</a>
            <a class="btn btn-sm btn-dark mb-2 mr-2" href="<?= $base_url ?>/GrocerySale/pdf/<?= $sid ?>" target="_blank" rel="noopener"><i class="fa fa-file-pdf" aria-hidden="true"></i> PDF</a>
            <a class="btn btn-sm btn-success mb-2 mr-2" href="<?= $base_url ?>/GrocerySale/download/<?= $sid ?>"><i class="fa fa-download" aria-hidden="true"></i> Download</a>
            <a class="btn btn-sm btn-primary mb-2" href="<?= $base_url ?>/GrocerySale/edit/<?= $sid ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
        </div>
    </div>
    <div class="card" id="invoice-area">
        <div class="card-body">
            <div class="row border-bottom pb-3 mb-3">
                <div class="col-md-6">
                    <?php if ($logo): ?><img src="<?= htmlspecialchars($logo) ?>" alt="<?= htmlspecialchars($shop) ?>" style="max-height:56px;max-width:180px;object-fit:contain;margin-bottom:6px"><?php endif; ?>
                    <h3 class="mb-0"><?= htmlspecialchars($shop) ?></h3>
                    <small><?= htmlspecialchars(GrocerySetting::get('shop_address', '')) ?></small><br>
                    <small><?= htmlspecialchars(GrocerySetting::get('shop_phone', '')) ?></small>
                </div>
                <div class="col-md-6 text-right">
                    <h4>TAX INVOICE</h4>
                    <h5 class="mb-0"><?= htmlspecialchars($s['invoice_no'] ?? '') ?></h5>
                    <small><?= htmlspecialchars($s['sale_date'] ?? '') ?></small>
                </div>
            </div>
            <p><strong>Customer:</strong> <?= htmlspecialchars($s['customer_name'] ?? 'Walk-in Customer') ?>
                <?php if (!empty($s['customer_phone'])) echo '&nbsp;|&nbsp;' . htmlspecialchars($s['customer_phone']); ?></p>
            <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="thead-light">
                    <tr><th>#</th><th>Item</th><th class="text-right">Qty</th><th class="text-right">Price</th><th class="text-right">VAT</th><th class="text-right">Total</th></tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach ($saleLines as $line) { $line = is_array($line) ? $line : (array)$line; ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($line['name'] ?? '') ?><?php if (!empty($line['sku'])) echo '<br><small>' . htmlspecialchars($line['sku']) . '</small>'; ?></td>
                            <td class="text-right"><?= htmlspecialchars((string)($line['quantity'] ?? 0)) ?></td>
                            <td class="text-right"><?= _gdoc_m($line['selling_price'] ?? 0) ?></td>
                            <td class="text-right"><?= _gdoc_m($line['vat'] ?? 0) ?></td>
                            <td class="text-right"><?= _gdoc_m($line['subtotal'] ?? 0) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0"><small>Payment: <?= htmlspecialchars($s['payment_method_name'] ?? '') ?></small></p>
                    <p class="mb-0"><small>Status: <span class="text-uppercase"><?= htmlspecialchars($s['payment_status'] ?? '') ?></span></small></p>
                    <?php if (!empty($s['remark'])) echo '<p class="mb-0"><small>Note: ' . htmlspecialchars($s['remark']) . '</small></p>'; ?>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr><td class="text-right">Subtotal</td><td class="text-right" style="width:140px"><?= $currency ?> <?= _gdoc_m($s['subtotal'] ?? 0) ?></td></tr>
                        <tr><td class="text-right">Discount</td><td class="text-right"><?= $currency ?> <?= _gdoc_m($s['discount'] ?? 0) ?></td></tr>
                        <tr><td class="text-right">Tax</td><td class="text-right"><?= $currency ?> <?= _gdoc_m($s['tax'] ?? 0) ?></td></tr>
                        <tr class="font-weight-bold"><td class="text-right">Grand Total</td><td class="text-right"><?= $currency ?> <?= _gdoc_m($s['grand_total'] ?? 0) ?></td></tr>
                        <tr><td class="text-right">Paid</td><td class="text-right"><?= $currency ?> <?= _gdoc_m($s['paid_amount'] ?? 0) ?></td></tr>
                        <tr><td class="text-right">Due</td><td class="text-right"><?= $currency ?> <?= _gdoc_m($s['due_amount'] ?? 0) ?></td></tr>
                    </table>
                </div>
            </div>
            <p class="text-center border-top pt-2 mb-0"><?= htmlspecialchars(GrocerySetting::get('receipt_footer', 'Thank you for shopping with UrbanMart!')) ?></p>
        </div>
    </div>
</div>
<style>
    @media print {
        .topbar, .sidebar, .float-right { display: none !important; }
        #content-wrapper { margin-left: 0 !important; }
    }
</style>
