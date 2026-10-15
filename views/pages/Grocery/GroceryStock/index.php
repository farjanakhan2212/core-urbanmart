<?php
$data = $grocerystock ?? null;
$page = $data ? (int)$data->page : 1;
$q = $data ? (string)$data->q : '';
$low = $data ? (int)$data->low : 0;
$rows = grocery_rows($data);
$total = grocery_total($data);
Page::open();
Row::open();
Col::open(["col" => 12]);
Card::open(['title' => 'Stock Report']);
Html::link(['class' => 'btn btn-info', 'route' => 'GroceryStock/movements', 'text' => 'Movements']);
Html::link(['class' => 'btn btn-success', 'route' => 'GroceryStock/adjustments', 'text' => 'Adjustments']);
echo '<form method="get" class="form-inline mb-3 float-right">
        <input type="hidden" name="class" value="GroceryStock">
        <input type="text" name="q" value="' . htmlspecialchars($q) . '" class="form-control form-control-sm mr-1" placeholder="Name / SKU / barcode">
        <div class="form-check mr-2"><input class="form-check-input" type="checkbox" name="low" value="1" id="low-chk"' . ($low ? ' checked' : '') . '><label class="form-check-label" for="low-chk">Low stock</label></div>
        <button class="btn btn-sm btn-primary">Filter</button>
      </form>';
echo '<div class="table-responsive"><table class="table table-sm table-bordered table-hover">';
echo '<thead class="thead-light"><tr><th>Product</th><th>Category</th><th class="text-right">Qty</th><th class="text-right">Available</th><th class="text-right">Total Purchased</th><th class="text-right">Total Sold</th><th class="text-right">Stock Value</th><th>Unit</th><th class="text-center">Status</th></tr></thead><tbody>';
foreach ($rows as $r) {
    $avail = (float)($r['available_quantity'] ?? 0);
    $reorder = (float)($r['reorder_level'] ?? 0);
    echo '<tr>';
    echo '<td>' . htmlspecialchars($r['name'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['category_name'] ?? '') . '</td>';
    echo '<td class="text-right">' . number_format((float)$r['quantity'], 2) . '</td>';
    echo '<td class="text-right' . ($avail <= $reorder ? ' text-danger' : '') . '">' . number_format($avail, 2) . '</td>';
    echo '<td class="text-right">' . number_format((float)$r['total_purchased'], 2) . '</td>';
    echo '<td class="text-right">' . number_format((float)$r['total_sold'], 2) . '</td>';
    echo '<td class="text-right">' . grocery_money($r['stock_value'] ?? 0) . '</td>';
    echo '<td>' . htmlspecialchars($r['unit_short'] ?? '') . '</td>';
    echo '<td class="text-center">' . ($avail <= $reorder ? '<span class="badge badge-warning">Low</span>' : '<span class="badge badge-success">OK</span>') . '</td>';
    echo '</tr>';
}
if (!$rows) echo '<tr><td colspan="9" class="text-center">No stock found</td></tr>';
echo '</tbody></table></div>';
grocery_paginate_links($page, $total, 20);
Card::close();
Col::close();
Row::close();
Page::close();