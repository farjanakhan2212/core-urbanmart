<?php
$data = $groceryproduct ?? null;
$page = $data ? (int)$data->page : 1;
$q = $data ? (string)$data->q : '';
$low = $data ? (int)$data->low : 0;
$rows = grocery_rows($data);
$total = grocery_total($data);
Page::open();
Row::open();
Col::open(["col" => 12]);
Card::open(['title' => $low ? 'Low Stock Products' : 'Manage Products']);
Html::link(['class' => 'btn btn-success', 'route' => 'GroceryProduct/create', 'text' => 'Create Product']);
echo '<form method="get" class="form-inline mb-3 float-right">
        <input type="hidden" name="class" value="GroceryProduct">
        <input type="text" name="q" value="' . htmlspecialchars($q) . '" class="form-control form-control-sm mr-1" placeholder="Search name / sku / barcode...">
        <input type="hidden" name="low" value="' . $low . '">
        <button class="btn btn-sm btn-primary mr-1">Search</button>
        <a class="btn btn-sm ' . ($low ? 'btn-secondary' : 'btn-warning') . '" href="' . $base_url . '/GroceryProduct/' . ($low ? '' : '?low=1') . '">' . ($low ? 'Show All' : 'Low Stock') . '</a>
      </form>';
echo '<div class="table-responsive"><table class="table table-sm table-bordered table-hover">';
echo '<thead class="thead-light"><tr><th></th><th>Name</th><th>SKU</th><th>Category</th><th>Unit</th><th class="text-right">Price</th><th class="text-right">Stock</th><th>Status</th><th class="text-right">Actions</th></tr></thead><tbody>';
foreach ($rows as $r) {
    echo '<tr><td>';
    grocery_product_image($r['image'] ?? '');
    echo '</td><td>' . htmlspecialchars($r['name'] ?? '');
    if (!empty($r['barcode'])) echo '<br><small class="text-muted">' . htmlspecialchars($r['barcode']) . '</small>';
    echo '</td><td>' . htmlspecialchars($r['sku'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['category_name'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['unit_short'] ?? ($r['unit_name'] ?? '')) . '</td>';
    echo '<td class="text-right">' . grocery_money($r['selling_price'] ?? 0) . '</td>';
    $avail = (float)($r['available'] ?? $r['stock_quantity'] ?? 0);
    $reorder = (float)($r['reorder_level'] ?? 0);
    echo '<td class="text-right' . ($avail <= $reorder ? ' text-danger font-weight-bold' : '') . '">' . number_format($avail, 2) . '</td>';
    echo '<td>'; grocery_status_badge($r['status'] ?? 'inactive'); echo '</td>';
    echo '<td class="text-right">'; grocery_options('GroceryProduct', $r['id']); echo '</td></tr>';
}
if (!$rows) echo '<tr><td colspan="9" class="text-center">No products found</td></tr>';
echo '</tbody></table></div>';
grocery_paginate_links($page, $total, 20);
Card::close();
Col::close();
Row::close();
Page::close();