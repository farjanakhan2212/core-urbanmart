<?php
$data = $grocerysettings ?? null;
$row = is_object($data->data) ? (array)$data->data : ($data->data ?? []);
$methods = grocery_list_rows($data, 'payment_methods');
?>
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Shop Settings</h6></div>
            <div class="card-body">
                <form method="post" action="<?= $base_url ?>/GrocerySettings/save">
                    <div class="form-group">
                        <label class="small">Shop Name</label>
                        <input type="text" name="shop_name" class="form-control" value="<?= htmlspecialchars($row['shop_name'] ?? 'UrbanMart Grocery') ?>">
                    </div>
                    <div class="form-group">
                        <label class="small">Shop Phone</label>
                        <input type="text" name="shop_phone" class="form-control" value="<?= htmlspecialchars($row['shop_phone'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="small">Shop Email</label>
                        <input type="email" name="shop_email" class="form-control" value="<?= htmlspecialchars($row['shop_email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="small">Shop Address</label>
                        <input type="text" name="shop_address" class="form-control" value="<?= htmlspecialchars($row['shop_address'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="small">Currency Symbol</label>
                        <input type="text" name="currency_symbol" class="form-control" maxlength="6" value="<?= htmlspecialchars($row['currency_symbol'] ?? 'Tk') ?>">
                    </div>
                    <div class="form-group">
                        <label class="small">Default Payment Method</label>
                        <select name="default_payment_method" class="form-control">
                            <?php foreach ($methods as $m) echo '<option value="' . $m['id'] . '"' . (($row['default_payment_method'] ?? '') == $m['id'] ? ' selected' : '') . '>' . htmlspecialchars($m['name']) . '</option>'; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="small">Allow Negative Stock</label>
                        <select name="allow_negative_stock" class="form-control">
                            <option value="1"<?= !empty($row['allow_negative_stock']) ? ' selected' : '' ?>>Yes</option>
                            <option value="0"<?= empty($row['allow_negative_stock']) ? ' selected' : '' ?>>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="small">Receipt Footer</label>
                        <input type="text" name="receipt_footer" class="form-control" value="<?= htmlspecialchars($row['receipt_footer'] ?? 'Thank you for shopping with UrbanMart!') ?>">
                    </div>
                    <button class="btn btn-primary btn-block">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>