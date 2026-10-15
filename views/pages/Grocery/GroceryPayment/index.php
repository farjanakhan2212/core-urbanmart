<?php
$data = $grocerypayment ?? null;
$page = $data ? (int)$data->page : 1;
$q = $data ? (string)$data->q : '';
$rows = grocery_rows($data);
$total = grocery_total($data);
Page::open();
Row::open();
Col::open(["col" => 12]);
Card::open(['title' => 'Payments']);
Html::link(['class' => 'btn btn-success', 'route' => 'GroceryPayment/create', 'text' => 'New Payment']);
echo '<form method="get" class="form-inline mb-3 float-right">
        <input type="hidden" name="class" value="GroceryPayment">
        <input type="text" name="q" value="' . htmlspecialchars($q) . '" class="form-control form-control-sm mr-1" placeholder="Search...">
        <button class="btn btn-sm btn-primary">Search</button>
      </form>';
echo '<div class="table-responsive"><table class="table table-sm table-bordered table-hover">';
echo '<thead class="thead-light"><tr><th>No</th><th>Date</th><th>Type</th><th>Customer / Supplier</th><th class="text-right">Amount</th><th>Method</th><th>Ref</th></tr></thead><tbody>';
foreach ($rows as $r) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($r['payment_no'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['payment_date'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['payable_type'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['customer_name'] ?? $r['supplier_name'] ?? '') . '</td>';
    echo '<td class="text-right">' . grocery_money($r['amount'] ?? 0) . '</td>';
    echo '<td>' . htmlspecialchars($r['payment_method_name'] ?? '') . '</td>';
    echo '<td class="small">' . htmlspecialchars($r['transaction_id'] ?? '') . '</td>';
    echo '</tr>';
}
if (!$rows) echo '<tr><td colspan="7" class="text-center">No payments found</td></tr>';
echo '</tbody></table></div>';
grocery_paginate_links($page, $total, 20);
Card::close();
Col::close();
Row::close();
Page::close();