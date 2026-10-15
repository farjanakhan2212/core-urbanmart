<?php
$data = $grocerypurchase ?? null;
$page = $data ? (int)$data->page : 1;
$q = $data ? (string)$data->q : '';
$rows = grocery_rows($data);
$total = grocery_total($data);
Page::open();
Row::open();
Col::open(["col" => 12]);
Card::open(['title' => 'Purchases']);
Html::link(['class' => 'btn btn-success', 'route' => 'GroceryPurchase/create', 'text' => 'New Purchase']);
echo '<form method="get" class="form-inline mb-3 float-right">
        <input type="hidden" name="class" value="GroceryPurchase">
        <input type="text" name="q" value="' . htmlspecialchars($q) . '" class="form-control form-control-sm mr-1" placeholder="Search...">
        <button class="btn btn-sm btn-primary">Search</button>
      </form>';
echo '<div class="table-responsive"><table class="table table-sm table-bordered table-hover">';
echo '<thead class="thead-light"><tr><th>No</th><th>Date</th><th>Supplier</th><th class="text-right">Total</th><th class="text-right">Paid</th><th class="text-right">Due</th><th>Status</th></tr></thead><tbody>';
foreach ($rows as $r) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($r['purchase_no'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['purchase_date'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['supplier_name'] ?? '') . '</td>';
    echo '<td class="text-right">' . grocery_money($r['grand_total'] ?? 0) . '</td>';
    echo '<td class="text-right">' . grocery_money($r['paid_amount'] ?? 0) . '</td>';
    echo '<td class="text-right' . ((float)($r['due_amount'] ?? 0) > 0 ? ' text-danger' : '') . '">' . grocery_money($r['due_amount'] ?? 0) . '</td>';
    echo '<td>'; grocery_status_badge($r['status'] ?? ''); echo '</td>';
    echo '</tr>';
}
if (!$rows) echo '<tr><td colspan="7" class="text-center">No purchases found</td></tr>';
echo '</tbody></table></div>';
grocery_paginate_links($page, $total, 20);
Card::close();
Col::close();
Row::close();
Page::close();