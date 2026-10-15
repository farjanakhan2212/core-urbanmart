<?php
$products = GroceryProduct::posItems('', 100);
$suppliers = GrocerySupplier::rows('status = 1', [], 'name ASC');
$methods = GroceryPaymentMethod::rows('status = 1', [], 'name ASC');
$currency = GrocerySetting::get('currency_symbol', 'Tk');
?>
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h6 class="mb-0">Select Products</h6>
                <input id="pur-search" class="form-control form-control-sm w-50" placeholder="Search product...">
            </div>
            <div class="card-body">
                <div class="row" id="pur-grid">
                    <?php foreach ($products as $p) { ?>
                        <div class="col-md-3 mb-3">
                            <div class="card" style="cursor:pointer"
                                data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>"
                                data-cost="<?= $p['purchase_price'] ?>" data-sku="<?= htmlspecialchars($p['sku'] ?? '') ?>" onclick="purAdd(this)">
                                <div class="card-body p-2 text-center">
                                    <div class="small font-weight-bold text-truncate"><?= htmlspecialchars($p['name']) ?></div>
                                    <div class="text-muted small"><?= $currency ?> <?= number_format($p['purchase_price'], 2) ?></div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Purchase Details</h6></div>
            <div class="card-body">
                <form method="post" action="<?= $base_url ?>/GroceryPurchase/save">
                    <div class="form-group">
                        <label class="small">Supplier</label>
                        <select name="supplier_id" class="form-control">
                            <option value="">Select Supplier</option>
                            <?php foreach ($suppliers as $s) echo '<option value="' . $s['id'] . '">' . htmlspecialchars($s['name']) . '</option>'; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="small">Purchase Date</label>
                        <input type="date" name="purchase_date" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <table class="table table-sm">
                        <thead><tr><th>Item</th><th class="text-center">Qty</th><th class="text-right">Cost</th><th class="text-right">Total</th><th></th></tr></thead>
                        <tbody id="pur-cart"></tbody>
                    </table>
                    <div class="form-group row">
                        <label class="col-5 col-form-label">Payment Method</label>
                        <div class="col-7">
                            <select name="payment_method_id" class="form-control form-control-sm">
                                <?php foreach ($methods as $m) echo '<option value="' . $m['id'] . '">' . htmlspecialchars($m['name']) . '</option>'; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-5 col-form-label">Discount</label>
                        <div class="col-7"><input type="number" name="discount" id="pur-discount" class="form-control form-control-sm" value="0" min="0" step="0.01"></div>
                    </div>
                    <div class="form-group row">
                        <label class="col-5 col-form-label">Tax</label>
                        <div class="col-7"><input type="number" name="tax" class="form-control form-control-sm" value="0" min="0" step="0.01"></div>
                    </div>
                    <div class="form-group row">
                        <label class="col-5 col-form-label">Amount Paid</label>
                        <div class="col-7"><input type="number" name="paid_amount" class="form-control form-control-sm" value="0" min="0" step="0.01"></div>
                    </div>
                    <div class="form-group">
                        <label class="small">Remark</label>
                        <textarea name="remark" class="form-control" rows="2"></textarea>
                    </div>
                    <input type="hidden" name="cart" id="pur-cart-json">
                    <button class="btn btn-success btn-block" type="submit">Save Purchase</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    window._purCart = {};
    function purAdd(el) {
        var id = $(el).data('id');
        if (!window._purCart[id]) window._purCart[id] = { product_id: id, name: $(el).data('name'), purchase_price: $(el).data('cost'), quantity: 1 };
        else window._purCart[id].quantity++;
        purRender();
    }
    function purRender() {
        var $tb = $('#pur-cart').empty();
        $.each(window._purCart, function (id, it) {
            var line = Number(it.purchase_price) * Number(it.quantity);
            $tb.append('<tr>' +
                '<td>' + it.name + '</td>' +
                '<td class="text-center"><input type="number" min="1" class="form-control form-control-sm text-center pur-qty" data-id="' + id + '" value="' + it.quantity + '"></td>' +
                '<td class="text-right">' + Number(it.purchase_price).toFixed(2) + '</td>' +
                '<td class="text-right">' + line.toFixed(2) + '</td>' +
                '<td><button type="button" class="btn btn-sm btn-danger pur-rm" data-id="' + id + '">&times;</button></td></tr>');
        });
        $('#pur-cart-json').val(JSON.stringify(Object.values(window._purCart)));
    }
    $(document).on('change', '.pur-qty', function () {
        var id = $(this).data('id'), v = parseInt(this.value, 10);
        if (v < 1) v = 1;
        window._purCart[id].quantity = v;
        purRender();
    });
    $(document).on('click', '.pur-rm', function () {
        delete window._purCart[$(this).data('id')];
        purRender();
    });
    $('#pur-search').on('input', function () {
        var q = this.value.trim();
        $('#pur-grid .col-md-3').each(function () {
            $(this).toggle($(this).find('.font-weight-bold').text().toLowerCase().indexOf(q.toLowerCase()) !== -1 || q.length < 2);
        });
    });
</script>