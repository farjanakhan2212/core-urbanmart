<?php
$data = $groceryreport ?? null;
$salesToday = $data ? $data->salesToday : 0;
$salesMonth = $data ? $data->salesMonth : 0;
$purchMonth = $data ? $data->purchMonth : 0;
$expenseMonth = $data ? $data->expenseMonth : 0;
$dueTotal = $data ? $data->dueTotal : 0;
Page::open();
Row::open();
Col::open(["col" => 3]);
Card::open(['title' => 'Sales Today']);
echo '<h2 class="text-success">' . grocery_money($salesToday) . '</h2>';
Card::close();
Col::close();
Col::open(["col" => 3]);
Card::open(['title' => 'Sales This Month']);
echo '<h2 class="text-primary">' . grocery_money($salesMonth) . '</h2>';
Card::close();
Col::close();
Col::open(["col" => 3]);
Card::open(['title' => 'Purchases This Month']);
echo '<h2 class="text-info">' . grocery_money($purchMonth) . '</h2>';
Card::close();
Col::close();
Col::open(["col" => 3]);
Card::open(['title' => 'Expenses This Month']);
echo '<h2 class="text-warning">' . grocery_money($expenseMonth) . '</h2>';
Card::close();
Col::close();
Col::open(["col" => 12]);
Card::open(['title' => 'Customer Receivables']);
echo '<h2 class="text-danger">' . grocery_money($dueTotal) . '</h2>';
Card::close();
Col::close();
$topProducts = grocery_list_rows($data, 'top_products');
if ($topProducts) {
    Col::open(["col" => 12]);
    Card::open(['title' => 'Top Products (This Month)']);
    echo '<div class="table-responsive"><table class="table table-sm table-bordered">';
    echo '<thead class="thead-light"><tr><th>#</th><th>Product</th><th>SKU</th><th class="text-right">Qty</th><th class="text-right">Amount</th></tr></thead><tbody>';
    $i = 1;
    foreach ($topProducts as $t) {
        echo '<tr><td>' . $i++ . '</td><td>' . htmlspecialchars($t['name'] ?? '') . '</td><td>' . htmlspecialchars($t['sku'] ?? '') . '</td><td class="text-right">' . number_format((float)($t['qty'] ?? 0), 2) . '</td><td class="text-right">' . grocery_money($t['amount'] ?? 0) . '</td></tr>';
    }
    echo '</tbody></table></div>';
    Card::close();
    Col::close();
}
Row::close();
Page::close();