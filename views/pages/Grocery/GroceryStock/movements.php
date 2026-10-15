<?php
$data = $grocerystock ?? null;
$page = $data ? (int)$data->page : 1;
$q = $data ? (string)$data->q : '';
$type = $data ? (string)$data->type : '';
$rows = grocery_rows($data);
$total = grocery_total($data);
Page::open();
Row::open();
Col::open(["col" => 12]);
Card::open(['title' => 'Stock Movements']);
Html::link(['class' => 'btn btn-info', 'route' => 'GroceryStock', 'text' => 'Stock Report']);
echo '<form method="get" class="form-inline mb-3 float-right">
        <input type="hidden" name="class" value="GroceryStock">
        <input type="hidden" name="method" value="movements">
        <input type="text" name="q" value="' . htmlspecialchars($q) . '" class="form-control form-control-sm mr-1" placeholder="Search...">
        <select name="type" class="form-control form-control-sm mr-1">
            <option value="">All types</option>
            <option value="in"' . ($type === 'in' ? ' selected' : '') . '>Stock In (Purchase / Opening / Adjustment)</option>
            <option value="out"' . ($type === 'out' ? ' selected' : '') . '>Stock Out (Sale / Damage / Adjustment)</option>
            <option value="adjustment"' . ($type === 'adjustment' ? ' selected' : '') . '>Adjustment</option>
        </select>
        <button class="btn btn-sm btn-primary">Filter</button>
      </form>';
echo '<div class="table-responsive"><table class="table table-sm table-bordered table-hover">';
echo '<thead class="thead-light"><tr><th>#</th><th>Date</th><th>Product</th><th class="text-center">Type</th><th class="text-right">Qty</th><th class="text-right">Balance</th><th>Reference</th><th>Remark</th><th>By</th></tr></thead><tbody>';
foreach ($rows as $r) {
    echo '<tr>';
    echo '<td>' . $r['id'] . '</td>';
    echo '<td>' . htmlspecialchars($r['created_at'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['name'] ?? '') . '</td>';
    echo '<td class="text-center">'; grocery_status_badge($r['movement_type'] ?? ''); echo '</td>';
    echo '<td class="text-right">' . number_format((float)$r['quantity'], 2) . '</td>';
    echo '<td class="text-right">' . number_format((float)$r['balance_after'], 2) . '</td>';
    echo '<td class="small">' . htmlspecialchars($r['reference_type'] ?? '') . ' #' . ($r['reference_id'] ?? '') . '</td>';
    echo '<td class="small">' . htmlspecialchars($r['remark'] ?? '') . '</td>';
    echo '<td class="small">' . htmlspecialchars($r['created_by'] ?? '') . '</td>';
    echo '</tr>';
}
if (!$rows) echo '<tr><td colspan="9" class="text-center">No movements found</td></tr>';
echo '</tbody></table></div>';
grocery_paginate_links($page, $total, 25);
Card::close();
Col::close();
Row::close();
Page::close();