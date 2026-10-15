<?php
$products = GroceryProduct::posItems('', 50);
$customers = GroceryCustomer::rows('status = 1', [], 'name ASC');
$methods = GroceryPaymentMethod::rows('status = 1', [], 'name ASC');
$currency = GrocerySetting::get('currency_symbol', 'Tk');
?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h6 class="mb-0 fw-bolder">Products</h6>
                <input id="pos-search" class="form-control form-control-sm w-50" placeholder="Search product / barcode...">
            </div>
            <div class="card-body">
                <div class="row" id="pos-grid">
                    <?php foreach ($products as $p) {
                        $avail = (float)($p['available'] ?? 0);
                        $img = $p['image'] ? htmlspecialchars($p['image']) : 'grocery-1232944-1280-jpg.jpg';
                    ?>
                        <div class="col-md-3 mb-3">
                            <div class="card product-card" style="cursor:pointer"
                                data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>"
                                data-price="<?= $p['selling_price'] ?>" data-sku="<?= htmlspecialchars($p['sku'] ?? '') ?>">
                                <img src="<?= $base_url ?>/img/<?= $img ?>" class="card-img-top" style="height:100px;object-fit:cover">
                                <div class="card-body p-2 text-center">
                                    <div class="small font-weight-bold text-truncate"><?= htmlspecialchars($p['name']) ?></div>
                                    <div class="text-muted small"><?= $currency ?> <?= number_format($p['selling_price'], 2) ?></div>
                                    <div class="text-muted small">Stock: <?= number_format($avail, 0) ?></div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h6 class="mb-0 fw-bolder">Cart</h6></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="small">Customer</label>
                    <select id="pos-customer" class="form-control">
                        <option value="">Walk-in Customer</option>
                        <?php foreach ($customers as $c) echo '<option value="' . $c['id'] . '">' . htmlspecialchars($c['name']) . '</option>'; ?>
                    </select>
                </div>
                <table class="table table-sm">
                    <thead><tr><th>Item</th><th class="text-center">Qty</th><th class="text-right">Total</th><th></th></tr></thead>
                    <tbody id="pos-cart"></tbody>
                </table>
                <div class="form-group row">
                    <label class="col-5 col-form-label">Discount</label>
                    <div class="col-7"><input type="number" id="pos-discount" class="form-control form-control-sm" value="0" min="0" step="0.01"></div>
                </div>
                <div class="form-group row">
                    <label class="col-5 col-form-label">Grand Total</label>
                    <div class="col-7"><h4 class="mb-0 text-right" id="pos-grand"><?= $currency ?> 0.00</h4></div>
                </div>
                <div class="form-group row">
                    <label class="col-5 col-form-label">Payment Method</label>
                    <div class="col-7">
                        <select id="pos-method" class="form-control form-control-sm">
                            <?php foreach ($methods as $m) echo '<option value="' . $m['id'] . '">' . htmlspecialchars($m['name']) . '</option>'; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-5 col-form-label">Amount Received</label>
                    <div class="col-7"><input type="number" id="pos-paid" class="form-control form-control-sm" min="0" step="0.01"></div>
                </div>
                <button class="btn btn-success btn-block" id="pos-checkout">Complete Sale</button>
            </div>
        </div>
    </div>
</div>
<script>
    var UM_BASE = '<?= $base_url ?>';
    var cart = {};
    var currency = '<?= $currency ?>';

    $(document).ready(function () {
        $('#pos-search').on('input', function () {
            var q = this.value.trim();
            var $grid = $('#pos-grid');
            if (q.length < 2) { showAll(); return; }
            $.getJSON(UM_BASE + '/GroceryProduct/search?q=' + encodeURIComponent(q), function (res) {
                $grid.empty();
                if (res.success && res.items.length) {
                    $.each(res.items, function (i, p) { $grid.append(cardHtml(p)); });
                } else {
                    $grid.html('<div class="col-12 text-center text-muted">No products found</div>');
                }
            });
        });
        function showAll() {
            // reloading the grid from server is heavy; hide nothing - simply leave as is
        }
        function cardHtml(p) {
            var img = p.image || 'grocery-1232944-1280-jpg.jpg';
            return '<div class="col-md-3 mb-3">' +
                '<div class="card product-card" style="cursor:pointer" data-id="' + p.id + '" data-name="' + $('<div>').text(p.name).html() + '" data-price="' + p.selling_price + '">' +
                '<img src="' + UM_BASE + '/img/' + img + '" class="card-img-top" style="height:100px;object-fit:cover">' +
                '<div class="card-body p-2 text-center"><div class="small font-weight-bold text-truncate">' + $('<div>').text(p.name).html() + '</div>' +
                '<div class="text-muted small">' + currency + ' ' + Number(p.selling_price).toFixed(2) + '</div></div></div></div>';
        }
        $(document).on('click', '.product-card', function () {
            var id = $(this).data('id');
            if (!cart[id]) {
                cart[id] = { product_id: id, name: $(this).data('name'), price: $(this).data('price'), quantity: 1 };
            } else {
                cart[id].quantity++;
            }
            renderCart();
        });
        function renderCart() {
            var $tb = $('#pos-cart').empty();
            var grand = 0;
            $.each(cart, function (id, it) {
                var line = Number(it.price) * Number(it.quantity);
                grand += line;
                $tb.append('<tr>' +
                    '<td>' + it.name + '</td>' +
                    '<td class="text-center"><input type="number" min="1" class="form-control form-control-sm text-center pos-qty" data-id="' + id + '" value="' + it.quantity + '"></td>' +
                    '<td class="text-right">' + currency + ' ' + line.toFixed(2) + '</td>' +
                    '<td><button class="btn btn-sm btn-danger pos-remove" data-id="' + id + '">&times;</button></td></tr>');
            });
            $('#pos-grand').text(currency + ' ' + grand.toFixed(2));
        }
        $(document).on('change', '.pos-qty', function () {
            var id = $(this).data('id');
            var v = parseInt(this.value, 10);
            if (v < 1) v = 1;
            cart[id].quantity = v;
            renderCart();
        });
        $(document).on('click', '.pos-remove', function () {
            delete cart[$(this).data('id')];
            renderCart();
        });
        $('#pos-checkout').on('click', function () {
            var items = Object.keys(cart).map(function (k) { return cart[k]; });
            if (!items.length) { alert('Cart is empty'); return; }
            var payload = {
                customer_id: $('#pos-customer').val(),
                discount: $('#pos-discount').val(),
                payment_method_id: $('#pos-method').val(),
                paid_amount: $('#pos-paid').val(),
                cart: JSON.stringify(items)
            };
            $.post(UM_BASE + '/GroceryPos/checkout', payload, function (res) {
                if (res.success) {
                    window.location.href = res.redirect;
                } else {
                    alert(res.message || 'Checkout failed');
                }
            }, 'json');
        });
    });
</script>