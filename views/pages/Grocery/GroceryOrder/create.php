<?php
$products = GroceryProduct::posItems('', 100);
$customers = GroceryCustomer::rows('status = 1', [], 'name ASC');
$methods = GroceryPaymentMethod::rows('status = 1', [], 'name ASC');
$currency = GrocerySetting::get('currency_symbol', 'Tk');
?>
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h6 class="mb-0">Select Products</h6>
                <input id="ord-search" class="form-control form-control-sm w-50" placeholder="Search product...">
            </div>
            <div class="card-body">
                <div class="row" id="ord-grid">
                    <?php foreach ($products as $p) { ?>
                        <div class="col-md-3 mb-3">
                            <div class="card" style="cursor:pointer"
                                data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>"
                                data-price="<?= $p['selling_price'] ?>" onclick="ordAdd(this)">
                                <div class="card-body p-2 text-center">
                                    <div class="small font-weight-bold text-truncate"><?= htmlspecialchars($p['name']) ?></div>
                                    <div class="text-muted small"><?= $currency ?> <?= number_format($p['selling_price'], 2) ?></div>
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
            <div class="card-header"><h6 class="mb-0">Order Details</h6></div>
            <div class="card-body">
                <form method="post" action="<?= $base_url ?>/GroceryOrder/save">
                    <div class="form-group">
                        <label class="small">Customer</label>
                        <select name="customer_id" class="form-control">
                            <option value="">Select Customer</option>
                            <?php foreach ($customers as $c) echo '<option value="' . $c['id'] . '">' . htmlspecialchars($c['name']) . '</option>'; ?>
                        </select>
                    </div>
                    <table class="table table-sm">
                        <thead><tr><th>Item</th><th class="text-center">Qty</th><th class="text-right">Price</th><th class="text-right">Total</th><th></th></tr></thead>
                        <tbody id="ord-cart"></tbody>
                    </table>
                    <table class="table table-sm mb-2">
                        <tbody>
                            <tr><td class="text-right">Subtotal</td><td class="text-right" style="width:40%"><?= $currency ?> <span id="ord-subtotal">0.00</span></td></tr>
                            <tr><td class="text-right">Discount</td><td class="text-right"><?= $currency ?> <span id="ord-discount-view">0.00</span></td></tr>
                            <tr><td class="text-right">Shipping</td><td class="text-right"><?= $currency ?> <span id="ord-shipping-view">0.00</span></td></tr>
                            <tr><th class="text-right">Grand Total</th><th class="text-right"><?= $currency ?> <span id="ord-grand">0.00</span></th></tr>
                        </tbody>
                    </table>
                    <div class="form-group row">
                        <label class="col-5 col-form-label">Discount</label>
                        <div class="col-7"><input type="number" id="ord-discount" name="discount" class="form-control form-control-sm" value="0" min="0" step="0.01"></div>
                    </div>
                    <div class="form-group row">
                        <label class="col-5 col-form-label">Shipping Fee</label>
                        <div class="col-7"><input type="number" id="ord-shipping" name="shipping_fee" class="form-control form-control-sm" value="0" min="0" step="0.01"></div>
                    </div>
                    <div class="form-group row">
                        <label class="col-5 col-form-label">Payment Method</label>
                        <div class="col-7">
                            <select name="payment_method_id" class="form-control form-control-sm">
                                <?php foreach ($methods as $m) echo '<option value="' . $m['id'] . '">' . htmlspecialchars($m['name']) . '</option>'; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-5 col-form-label">Amount Paid</label>
                        <div class="col-7"><input type="number" id="ord-paid" name="paid_amount" class="form-control form-control-sm" value="0" min="0" step="0.01"></div>
                    </div>
                    <div class="form-group">
                        <label class="small">Shipping Address</label>
                        <textarea name="shipping_address" class="form-control" rows="2" placeholder="Address"></textarea>
                    </div>
                    <div class="form-group row">
                        <label class="col-5 col-form-label">Expected Delivery</label>
                        <div class="col-7"><input type="date" name="expected_delivery" class="form-control form-control-sm"></div>
                    </div>
                    <input type="hidden" name="cart" id="ord-cart-json">
                    <button class="btn btn-success btn-block">Place Order</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    window._ordCart = {};
    var UM_ORD_PAID_EDITED = false;
    function ordNum(v) {
        var n = parseFloat(v);
        return isFinite(n) ? n : 0;
    }
    function ordAdd(el) {
        var id = $(el).data('id');
        if (!window._ordCart[id]) window._ordCart[id] = { product_id: id, name: $(el).data('name'), price: $(el).data('price'), quantity: 1 };
        else window._ordCart[id].quantity++;
        ordRender();
    }
    function ordSubtotal() {
        var sub = 0;
        $.each(window._ordCart, function (id, it) {
            sub += ordNum(it.price) * ordNum(it.quantity);
        });
        return sub;
    }
    function ordTotals() {
        var sub = ordSubtotal();
        var discount = ordNum($('#ord-discount').val());
        var shipping = ordNum($('#ord-shipping').val());
        if (discount < 0) discount = 0;
        if (shipping < 0) shipping = 0;
        var grand = sub - discount + shipping;
        if (grand < 0 || sub <= 0) grand = 0;
        $('#ord-subtotal').text(sub.toFixed(2));
        $('#ord-discount-view').text(discount.toFixed(2));
        $('#ord-shipping-view').text(shipping.toFixed(2));
        $('#ord-grand').text(grand.toFixed(2));
        if (!UM_ORD_PAID_EDITED) $('#ord-paid').val(grand.toFixed(2));
    }
    function ordRefresh() {
        $.each(window._ordCart, function (id, it) {
            var line = ordNum(it.price) * ordNum(it.quantity);
            $('.ord-line[data-id="' + id + '"]').text(line.toFixed(2));
        });
        $('#ord-cart-json').val(JSON.stringify(Object.values(window._ordCart)));
        ordTotals();
    }
    function ordRender() {
        var $tb = $('#ord-cart').empty();
        $.each(window._ordCart, function (id, it) {
            var line = ordNum(it.price) * ordNum(it.quantity);
            $tb.append('<tr>' +
                '<td>' + it.name + '</td>' +
                '<td class="text-center"><input type="number" min="1" step="1" class="form-control form-control-sm text-center ord-qty" data-id="' + id + '" value="' + it.quantity + '"></td>' +
                '<td class="text-right"><input type="number" min="0" step="0.01" class="form-control form-control-sm text-right ord-price" data-id="' + id + '" value="' + ordNum(it.price).toFixed(2) + '"></td>' +
                '<td class="text-right"><span class="ord-line" data-id="' + id + '">' + line.toFixed(2) + '</span></td>' +
                '<td><button type="button" class="btn btn-sm btn-danger ord-rm" data-id="' + id + '">&times;</button></td></tr>');
        });
        $('#ord-cart-json').val(JSON.stringify(Object.values(window._ordCart)));
        ordTotals();
    }
    $(document).on('input', '.ord-qty', function () {
        var id = $(this).data('id'), v = parseInt(this.value, 10);
        if (isNaN(v) || v < 1) v = 1;
        window._ordCart[id].quantity = v;
        ordRefresh();
    });
    $(document).on('change', '.ord-qty', function () {
        var id = $(this).data('id'), v = parseInt(this.value, 10);
        if (isNaN(v) || v < 1) v = 1;
        window._ordCart[id].quantity = v;
        $(this).val(v);
        ordRefresh();
    });
    $(document).on('input', '.ord-price', function () {
        var id = $(this).data('id'), v = ordNum(this.value);
        if (v < 0) v = 0;
        window._ordCart[id].price = v;
        ordRefresh();
    });
    $(document).on('change', '.ord-price', function () {
        var id = $(this).data('id'), v = ordNum(this.value);
        if (v < 0) v = 0;
        window._ordCart[id].price = v;
        $(this).val(v.toFixed(2));
        ordRefresh();
    });
    $(document).on('click', '.ord-rm', function () {
        delete window._ordCart[$(this).data('id')];
        ordRender();
    });
    $('#ord-discount, #ord-shipping').on('input change', ordTotals);
    $('#ord-paid').on('input change', function () {
        UM_ORD_PAID_EDITED = $(this).val() !== '';
    });
    $(function () { ordTotals(); });
    $('#ord-search').on('input', function () {
        var q = this.value.trim();
        $('#ord-grid .col-md-3').each(function () {
            $(this).toggle($(this).find('.font-weight-bold').text().toLowerCase().indexOf(q.toLowerCase()) !== -1 || q.length < 2);
        });
    });
</script>