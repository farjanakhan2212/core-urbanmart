<?php
/**
 * GrocerySale edit form (only reachable for non-paid sales).
 * @var obj $grocerysale passed by GrocerySaleController::edit()
 */
$data = $grocerysale ?? null;
$s = $data ? ($data->sale ?? null) : null;
if (!$s) {
    echo '<div class="alert alert-warning">Sale not found.</div>';
    return;
}
$s = (array)$s;
$customers = $data->customers ?? [];
$methods   = $data->methods ?? [];
$products  = $data->products ?? [];
$sid       = (int)($s['id'] ?? 0);
$currency  = GrocerySetting::get('currency_symbol', 'Tk');

$lines = [];
foreach ((array)($s['lines'] ?? []) as $l) {
    $lines[] = is_object($l) ? (array)$l : (array)$l;
}

function _gedit_h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <a class="btn btn-sm btn-light mb-3" href="<?= $base_url ?>/GrocerySale"><span aria-hidden="true">&larr;</span> Back to Sales</a>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h6 class="mb-0 fw-bolder">Edit Sale — <?= _gedit_h($s['invoice_no'] ?? '') ?></h6></div>
        <div class="card-body">
            <form method="post" action="<?= $base_url ?>/GrocerySale/update/<?= $sid ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $sid ?>">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="small">Customer</label>
                            <select name="customer_id" class="form-control form-control-sm">
                                <option value="">Walk-in Customer</option>
                                <?php foreach ($customers as $c) { $c = is_object($c) ? (array)$c : $c; ?>
                                    <option value="<?= (int)$c['id'] ?>" <?= ((int)($s['customer_id'] ?? 0) === (int)$c['id']) ? 'selected' : '' ?>><?= _gedit_h($c['name']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="small">Sale Date</label>
                            <input type="date" name="sale_date" class="form-control form-control-sm" value="<?= _gedit_h($s['sale_date'] ?? date('Y-m-d')) ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="small">Payment Method</label>
                            <select name="payment_method_id" class="form-control form-control-sm">
                                <?php foreach ($methods as $m) { $m = is_object($m) ? (array)$m : $m; ?>
                                    <option value="<?= (int)$m['id'] ?>" <?= ((int)($s['payment_method_id'] ?? 0) === (int)$m['id']) ? 'selected' : '' ?>><?= _gedit_h($m['name']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="small">Amount Received (Paid)</label>
                            <input type="number" step="0.01" min="0" name="paid_amount" id="paid_amount" class="form-control form-control-sm" value="<?= _gedit_h($s['paid_amount'] ?? 0) ?>">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="lines-table">
                        <thead class="thead-light">
                            <tr>
                                <th style="min-width:240px">Product</th>
                                <th style="width:110px" class="text-right">Quantity</th>
                                <th style="width:130px" class="text-right">Unit Price</th>
                                <th style="width:120px" class="text-right">Discount</th>
                                <th style="width:50px"></th>
                            </tr>
                        </thead>
                        <tbody id="lines-body">
                        <?php foreach ($lines as $l) { ?>
                            <tr>
                                <td>
                                    <select name="product_id[]" class="form-control form-control-sm">
                                        <?php foreach ($products as $p) { $p = is_object($p) ? (array)$p : $p; ?>
                                            <option value="<?= (int)$p['id'] ?>" <?= ((int)($l['product_id'] ?? 0) === (int)$p['id']) ? 'selected' : '' ?>><?= _gedit_h($p['name']) ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td><input type="number" step="0.001" min="0" name="quantity[]" class="form-control form-control-sm text-right" value="<?= _gedit_h($l['quantity'] ?? 1) ?>"></td>
                                <td><input type="number" step="0.01" min="0" name="selling_price[]" class="form-control form-control-sm text-right" value="<?= _gedit_h($l['selling_price'] ?? 0) ?>"></td>
                                <td><input type="number" step="0.01" min="0" name="line_discount[]" class="form-control form-control-sm text-right" value="<?= _gedit_h($l['discount'] ?? 0) ?>"></td>
                                <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-line"><i class="fa fa-times" aria-hidden="true"></i></button></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <button type="button" id="add-line" class="btn btn-sm btn-outline-primary mb-3"><i class="fa fa-plus" aria-hidden="true"></i> Add Line</button>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="small">Note / Remark</label>
                            <textarea name="remark" class="form-control form-control-sm" rows="2"><?= _gedit_h($s['remark'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="small">Discount</label>
                            <input type="number" step="0.01" min="0" name="discount" class="form-control form-control-sm" value="<?= _gedit_h($s['discount'] ?? 0) ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="small">Tax</label>
                            <input type="number" step="0.01" min="0" name="tax" class="form-control form-control-sm" value="<?= _gedit_h($s['tax'] ?? 0) ?>">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-primary btn-block">Update Sale</button>
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning small mb-0">Saving recalculates totals, reverses the previous stock/payment impact and re-applies the new lines inside one transaction. Fully paid invoices cannot be edited.</div>
            </form>
        </div>
    </div>
</div>

<template id="line-template">
    <tr>
        <td>
            <select name="product_id[]" class="form-control form-control-sm">
                <?php foreach ($products as $p) { $p = is_object($p) ? (array)$p : $p; ?>
                    <option value="<?= (int)$p['id'] ?>"><?= _gedit_h($p['name']) ?></option>
                <?php } ?>
            </select>
        </td>
        <td><input type="number" step="0.001" min="0" name="quantity[]" class="form-control form-control-sm text-right" value="1"></td>
        <td><input type="number" step="0.01" min="0" name="selling_price[]" class="form-control form-control-sm text-right" value="0"></td>
        <td><input type="number" step="0.01" min="0" name="line_discount[]" class="form-control form-control-sm text-right" value="0"></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-line"><i class="fa fa-times" aria-hidden="true"></i></button></td>
    </tr>
</template>
<script>
(function () {
    var tpl = document.getElementById('line-template');
    var body = document.getElementById('lines-body');
    document.getElementById('add-line').addEventListener('click', function () {
        body.appendChild(tpl.content.cloneNode(true));
    });
    body.addEventListener('click', function (e) {
        var btn = e.target.closest('.remove-line');
        if (btn) {
            var rows = body.querySelectorAll('tr');
            if (rows.length > 1) { btn.closest('tr').remove(); }
        }
    });
})();
</script>
