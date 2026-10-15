<?php
$data = $grocerysale ?? null;
$page = $data ? (int)$data->page : 1;
$q = $data ? (string)$data->q : '';
$rows = grocery_rows($data);
$total = grocery_total($data);
Page::open();
Row::open();
Col::open(["col" => 12]);
Card::open(['title' => 'Sales / Invoices']);
Html::link(['class' => 'btn btn-success', 'route' => 'GroceryPos', 'text' => 'Open POS & Sale']);
echo '<form method="get" class="form-inline mb-3 float-right">
        <input type="hidden" name="class" value="GrocerySale">
        <input type="text" name="q" value="' . htmlspecialchars($q) . '" class="form-control form-control-sm mr-1" placeholder="Search invoice / customer...">
        <button class="btn btn-sm btn-primary">Search</button>
      </form>';
echo '<div class="table-responsive"><table class="table table-sm table-bordered table-hover">';
echo '<thead class="thead-light"><tr><th>Invoice</th><th>Date</th><th>Customer</th><th class="text-right">Total</th><th class="text-right">Paid</th><th class="text-right">Due</th><th>Status</th><th class="text-right" style="min-width:230px">Action</th></tr></thead><tbody>';
foreach ($rows as $r) {
    $id = (int)($r['id'] ?? 0);
    echo '<tr>';
    echo '<td>' . htmlspecialchars($r['invoice_no'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['sale_date'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($r['customer_name'] ?? 'Walk-in') . '</td>';
    echo '<td class="text-right">' . grocery_money($r['grand_total'] ?? 0) . '</td>';
    echo '<td class="text-right">' . grocery_money($r['paid_amount'] ?? 0) . '</td>';
    echo '<td class="text-right' . ((float)($r['due_amount'] ?? 0) > 0 ? ' text-danger' : '') . '">' . grocery_money($r['due_amount'] ?? 0) . '</td>';
    echo '<td>'; grocery_status_badge($r['payment_status'] ?? ''); echo '</td>';
    echo '<td class="text-right"><div class="btn-group" style="display:flex;flex-wrap:wrap;gap:3px;justify-content:flex-end;">'
        . '<a class="btn btn-info btn-sm" href="' . $base_url . '/GrocerySale/invoice/' . $id . '" title="View sale details"><i class="fa fa-eye" aria-hidden="true"></i> View</a>'
        . '<a class="btn btn-secondary btn-sm" href="' . $base_url . '/GrocerySale/print/' . $id . '" target="_blank" rel="noopener" title="Printable copy"><i class="fa fa-print" aria-hidden="true"></i> Print</a>'
        . '<a class="btn btn-dark btn-sm" href="' . $base_url . '/GrocerySale/pdf/' . $id . '" target="_blank" rel="noopener" title="Open PDF"><i class="fa fa-file-pdf" aria-hidden="true"></i> PDF</a>'
        . '<a class="btn btn-success btn-sm" href="' . $base_url . '/GrocerySale/download/' . $id . '" title="Download PDF"><i class="fa fa-download" aria-hidden="true"></i> Download</a>'
        . '<a class="btn btn-primary btn-sm" href="' . $base_url . '/GrocerySale/edit/' . $id . '" title="Edit sale"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>'
        . '<a class="btn btn-danger btn-sm" href="' . $base_url . '/GrocerySale/confirm/' . $id . '" title="Delete sale"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>'
        . '</div></td>';
    echo '</tr>';
}
if (!$rows) echo '<tr><td colspan="9" class="text-center">No sales found</td></tr>';
echo '</tbody></table></div>';
grocery_paginate_links($page, $total, 20);
Card::close();
Col::close();
Row::close();
Page::close();
