<?php
$products = GroceryProduct::posItems('', 100);
?>
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Select Products</h6></div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($products as $p) { ?>
                        <div class="col-md-4 mb-2">
                            <div class="card" style="cursor:pointer"
                                data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>" data-stock="<?= $p['available'] ?>" onclick="adjAdd(this)">
                                <div class="card-body p-2 text-center">
                                    <div class="small font-weight-bold text-truncate mb-0"><?= htmlspecialchars($p['name']) ?></div>
                                    <div class="text-muted small">Stock: <?= number_format((float)$p['available'], 2) ?></div>
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
            <div class="card-header"><h6 class="mb-0">New Adjustment</h6></div>
            <div class="card-body">
                <form method="post" action="<?= $base_url ?>/GroceryStock/adjustment_save">
                    <div class="form-group">
                        <label class="small">Adjustment Type</label>
                        <select name="adjustment_type" class="form-control">
                            <option value="in">Stock In (+) &mdash; e.g. found surplus</option>
                            <option value="out">Stock Out (&minus;) &mdash; e.g. damage/loss</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="small">Reason</label>
                        <input type="text" name="reason" class="form-control" placeholder="e.g. damaged goods, count error">
                    </div>
                    <table class="table table-sm">
                        <thead><tr><th>Item</th><th class="text-center">Qty</th><th></th></tr></thead>
                        <tbody id="adj-cart"></tbody>
                    </table>
                    <input type="hidden" name="cart" id="adj-cart-json">
                    <button class="btn btn-success btn-block">Save Adjustment</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    window._adjCart = {};
    function adjAdd(el) {
        var id = $(el).data('id');
        if (!window._adjCart[id]) window._adjCart[id] = { product_id: id, name: $(el).data('name'), quantity: 1 };
        else window._adjCart[id].quantity++;
        adjRender();
    }
    function adjRender() {
        var $tb = $('#adj-cart').empty();
        $.each(window._adjCart, function (id, it) {
            $tb.append('<tr>' +
                '<td>' + it.name + '</td>' +
                '<td class="text-center"><input type="number" min="1" class="form-control form-control-sm text-center adj-qty" data-id="' + id + '" value="' + it.quantity + '"></td>' +
                '<td><button type="button" class="btn btn-sm btn-danger adj-rm" data-id="' + id + '">&times;</button></td></tr>');
        });
        $('#adj-cart-json').val(JSON.stringify(Object.values(window._adjCart)));
    }
    $(document).on('change', '.adj-qty', function () {
        var id = $(this).data('id'), v = parseInt(this.value, 10);
        if (v < 1) v = 1;
        window._adjCart[id].quantity = v;
        adjRender();
    });
    $(document).on('click', '.adj-rm', function () {
        delete window._adjCart[$(this).data('id')];
        adjRender();
    });
</script>