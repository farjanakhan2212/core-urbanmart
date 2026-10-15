<?php
$mr = $moneyreceipt ?? null;
if (!$mr) { echo 'Record not found'; return; }
$details = MoneyReceiptDetail::Filter($mr->id);
$customers = Customer::all();
$products = Product::all();
?>
<style>
    .item-row td { vertical-align: middle; }
</style>
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Edit Money Receipt MR-<?= (int)$mr->id ?></h6></div>
            <div class="card-body">
                <form method="post" action="<?= $base_url ?>/moneyreceipt/update/<?= (int)$mr->id ?>">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="small">Receipt Date</label>
                            <input type="text" class="form-control" value="<?= date('d-M-Y', strtotime($mr->created_at)) ?>" disabled>
                        </div>
                        <div class="form-group col-md-8">
                            <label class="small">Received From</label>
                            <select name="customer_id" class="form-control" required>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?= (int)$c->id ?>"<?= $c->id == $mr->customer_id ? ' selected' : '' ?>><?= htmlspecialchars($c->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="small">Remark</label>
                        <input type="text" name="remark" class="form-control" value="<?= htmlspecialchars($mr->remark ?? '') ?>">
                    </div>

                    <h6 class="mt-3">Items</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" id="items-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center" style="width:110px">Qty</th>
                                    <th class="text-right" style="width:150px">Price</th>
                                    <th class="text-right" style="width:150px">Amount</th>
                                    <th style="width:50px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rowIdx = 0; foreach ($details as $d): ?>
                                <tr class="item-row">
                                    <td>
                                        <select name="items[<?= $rowIdx ?>][product_id]" class="form-control form-control-sm item-product">
                                            <?php foreach ($products as $p): ?>
                                                <option value="<?= (int)$p->id ?>"<?= $p->id == $d->product_id ? ' selected' : '' ?>><?= htmlspecialchars($p->name) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="number" step="any" min="0" name="items[<?= $rowIdx ?>][qty]" class="form-control form-control-sm item-qty" value="<?= htmlspecialchars($d->qty) ?>"></td>
                                    <td><input type="number" step="any" min="0" name="items[<?= $rowIdx ?>][price]" class="form-control form-control-sm item-price" value="<?= htmlspecialchars($d->price) ?>"></td>
                                    <td class="text-right item-amount"><?= number_format((float)$d->qty * (float)$d->price, 2) ?></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-item">&times;</button></td>
                                </tr>
                                <?php $rowIdx++; endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Total</strong></td>
                                    <td class="text-right"><strong id="receipt-total">0.00</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-info" id="btn-add-item">Add Item</button>
                    <hr>
                    <button class="btn btn-success" type="submit">Update Money Receipt</button>
                    <a class="btn btn-secondary" href="<?= $base_url ?>/moneyreceipt">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var products = <?= json_encode(array_map(function ($p) { return ['id' => $p->id, 'name' => $p->name]; }, $products)) ?>;
    var table = document.querySelector('#items-table tbody');

    function opts(selected) {
        var h = '';
        products.forEach(function (p) {
            h += '<option value="' + p.id + '"' + (p.id == selected ? ' selected' : '') + '>' + p.name.replace(/</g, '&lt;') + '</option>';
        });
        return h;
    }

    function addRow(sel, qty, price) {
        var tr = document.createElement('tr');
        tr.className = 'item-row';
        var idx = table.querySelectorAll('.item-row').length;
        tr.innerHTML =
            '<td><select name="items[' + idx + '][product_id]" class="form-control form-control-sm item-product">' + opts(sel || 0) + '</select></td>' +
            '<td><input type="number" step="any" min="0" name="items[' + idx + '][qty]" class="form-control form-control-sm item-qty" value="' + (qty || 1) + '"></td>' +
            '<td><input type="number" step="any" min="0" name="items[' + idx + '][price]" class="form-control form-control-sm item-price" value="' + (price || '') + '"></td>' +
            '<td class="text-right item-amount">0.00</td>' +
            '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-item">&times;</button></td>';
        table.appendChild(tr);
        recalc();
    }

    function recalc() {
        var total = 0;
        table.querySelectorAll('.item-row').forEach(function (tr) {
            var qty = parseFloat(tr.querySelector('.item-qty').value) || 0;
            var price = parseFloat(tr.querySelector('.item-price').value) || 0;
            var amt = qty * price;
            tr.querySelector('.item-amount').textContent = amt.toFixed(2);
            total += amt;
        });
        document.querySelector('#receipt-total').textContent = total.toFixed(2);
    }

    document.querySelector('#btn-add-item').addEventListener('click', function () { addRow(); });

    table.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-remove-item')) {
            var rows = table.querySelectorAll('.item-row');
            if (rows.length > 1) { e.target.closest('tr').remove(); recalc(); }
        }
    });
    table.addEventListener('input', recalc);

    recalc();
})();
</script>