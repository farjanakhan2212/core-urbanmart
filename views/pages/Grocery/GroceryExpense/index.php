<?php
$data = $groceryexpense ?? null;
$page = $data ? (int)$data->page : 1;
$q = $data ? (string)$data->q : '';
$rows = grocery_rows($data);
$total = grocery_total($data);
Page::open();
Row::open();
Col::open(["col" => 12]);
Card::open(['title' => 'Expenses']);
Html::link(['class' => 'btn btn-success', 'route' => 'GroceryExpense/create', 'text' => 'New Expense']);
echo '<form method="get" class="form-inline mb-3 float-right">
        <input type="hidden" name="class" value="GroceryExpense">
        <input type="text" name="q" value="' . htmlspecialchars($q) . '" class="form-control form-control-sm mr-1" placeholder="Search...">
        <button class="btn btn-sm btn-primary">Search</button>
      </form>';
echo '<div class="table-responsive"><table class="table table-sm table-bordered table-hover">';
echo '<thead class="thead-light"><tr><th>No</th><th>Date</th><th>Category</th><th class="text-right">Amount</th><th>Remark</th><th class="text-right"></th></tr></thead><tbody>';
foreach ($rows as $r) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($r['expense_no'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['expense_date'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['category_name'] ?? '') . '</td>';
    echo '<td class="text-right">' . grocery_money($r['amount'] ?? 0) . '</td>';
    echo '<td>' . htmlspecialchars($r['remark'] ?? '') . '</td>';
    echo '<td class="text-center">' . grocery_options('GroceryExpense', $r['id']) . '</td>';
    echo '</tr>';
}
if (!$rows) echo '<tr><td colspan="6" class="text-center">No expenses found</td></tr>';
echo '</tbody></table></div>';
grocery_paginate_links($page, $total, 20);
Card::close();
Col::close();
Row::close();
Page::close();