<?php
$id = (int)($_GET['id'] ?? 0);
$row = $id ? GroceryProduct::findRow($id) : null;
Page::open();
Row::open();
Col::open(["col" => 9]);
Card::open(['title' => $row ? 'Edit Product: ' . htmlspecialchars($row['name']) : 'Create Product']);
Form::open(['route' => $row ? 'GroceryProduct/update/' . $id : 'GroceryProduct/save']);
Form::text(['name' => 'name', 'label' => 'Product Name', 'value' => $row['name'] ?? '']);
echo '<div class="form-group row">';
echo '<label class="col-sm-2 col-form-label">Category</label><div class="col-sm-10">';
Form::select(['name' => 'category_id', 'table' => 'grocery_categories', 'value' => $row['category_id'] ?? '']);
echo '</div></div>';
echo '<div class="form-group row">';
echo '<label class="col-sm-2 col-form-label">Subcategory</label><div class="col-sm-10">';
Form::select(['name' => 'subcategory_id', 'table' => 'grocery_subcategories', 'value' => $row['subcategory_id'] ?? '']);
echo '</div></div>';
echo '<div class="form-group row">';
echo '<label class="col-sm-2 col-form-label">Brand</label><div class="col-sm-10">';
Form::select(['name' => 'brand_id', 'table' => 'grocery_brands', 'value' => $row['brand_id'] ?? '']);
echo '</div></div>';
echo '<div class="form-group row">';
echo '<label class="col-sm-2 col-form-label">Unit</label><div class="col-sm-10">';
Form::select(['name' => 'unit_id', 'table' => 'grocery_units', 'value' => $row['unit_id'] ?? '']);
echo '</div></div>';
Form::text(['name' => 'sku', 'label' => 'SKU', 'value' => $row['sku'] ?? '']);
Form::text(['name' => 'barcode', 'label' => 'Barcode', 'value' => $row['barcode'] ?? '']);
echo '<div class="form-group row"><label class="col-sm-2 col-form-label">Product Type</label><div class="col-sm-10">';
echo '<select name="product_type" class="form-select" style="width:100%">';
foreach (['goods' => 'Goods', 'service' => 'Service'] as $k => $v) {
    $sel = ($row['product_type'] ?? 'goods') == $k ? ' selected' : '';
    echo "<option value='$k'$sel>$v</option>";
}
echo '</select></div></div>';
Form::number(['name' => 'purchase_price', 'label' => 'Purchase Price', 'value' => $row['purchase_price'] ?? 0]);
Form::number(['name' => 'selling_price', 'label' => 'Selling Price', 'value' => $row['selling_price'] ?? 0]);
Form::number(['name' => 'mrp_price', 'label' => 'MRP Price', 'value' => $row['mrp_price'] ?? 0]);
Form::number(['name' => 'tax_rate', 'label' => 'Tax Rate (%)', 'value' => $row['tax_rate'] ?? 0]);
echo '<div class="form-group row"><label class="col-sm-2 col-form-label">Discount Type</label><div class="col-sm-10">';
echo '<select name="discount_type" class="form-select" style="width:100%">';
foreach (['fixed' => 'Fixed', 'percent' => 'Percent'] as $k => $v) {
    $sel = ($row['discount_type'] ?? 'fixed') == $k ? ' selected' : '';
    echo "<option value='$k'$sel>$v</option>";
}
echo '</select></div></div>';
Form::number(['name' => 'discount_value', 'label' => 'Discount Value', 'value' => $row['discount_value'] ?? 0]);
Form::number(['name' => 'stock_quantity', 'label' => 'Stock Quantity', 'value' => $row['stock_quantity'] ?? 0]);
Form::number(['name' => 'reorder_level', 'label' => 'Reorder Level', 'value' => $row['reorder_level'] ?? 0]);
Form::number(['name' => 'weight', 'label' => 'Weight', 'value' => $row['weight'] ?? '']);
Form::text(['name' => 'short_description', 'label' => 'Short Description', 'value' => $row['short_description'] ?? '']);
Form::textarea(['name' => 'full_description', 'label' => 'Full Description', 'value' => $row['full_description'] ?? '']);
$img = $row['image'] ?? '';
Form::file(['name' => 'image', 'label' => 'Image', 'value' => $img]);
if ($img) {
    echo '<div class="form-group row"><div class="offset-sm-2 col-sm-10">';
    echo '<img src="' . $base_url . '/img/' . htmlspecialchars($img) . '" class="img-thumbnail" style="max-width:120px">';
    echo '</div></div>';
}
echo '<div class="form-group row"><label class="col-sm-2 col-form-label">Status</label><div class="col-sm-10">';
echo '<select name="status" class="form-select" style="width:100%">';
foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $k => $v) {
    $sel = ($row['status'] ?? 'active') == $k ? ' selected' : '';
    echo "<option value='$k'$sel>$v</option>";
}
echo '</select></div></div>';
echo '<div class="form-group row"><div class="offset-sm-2 col-sm-10">';
echo '<div class="form-check"><input type="checkbox" class="form-check-input" name="is_featured" id="is_featured" value="1"' . (!empty($row['is_featured']) ? ' checked' : '') . '>';
echo '<label class="form-check-label" for="is_featured">Featured product</label></div></div></div>';
Form::submit(['name' => 'submit', 'value' => $row ? 'Update Product' : 'Save Product', 'class' => 'btn btn-primary']);
Form::close();
Card::close();
Col::close();
Row::close();
Page::close();