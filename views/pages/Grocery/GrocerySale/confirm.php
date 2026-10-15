<?php
/**
 * GrocerySale delete confirmation.
 * @var obj $grocerysale passed by GrocerySaleController::confirm()
 */
$data = $grocerysale ?? null;
$s = $data ? ($data->sale ?? null) : null;
if (!$s) {
    echo '<div class="alert alert-warning">Sale not found.</div>';
    return;
}
$s = (array)$s;
$sid = (int)($s['id'] ?? 0);

Page::open();
Row::open();
Col::open(["col" => 12]);
Card::open(["title" => "Confirm Delete"]);
echo "<div class='alert alert-danger'><strong>Warning:</strong> Deleting sale <strong>"
    . htmlspecialchars($s['invoice_no'] ?? ('#' . $sid))
    . "</strong> permanently removes the invoice and its line items. Stock for the sold items "
    . "will be <strong>restored</strong> and the linked payment record removed. This action cannot be undone.</div>";

echo "<table class='table table-bordered'>";
echo "<tr><th style='width:220px'>Invoice No</th><td>" . htmlspecialchars($s['invoice_no'] ?? '') . "</td></tr>";
echo "<tr><th>Date</th><td>" . htmlspecialchars($s['sale_date'] ?? '') . "</td></tr>";
echo "<tr><th>Customer</th><td>" . htmlspecialchars($s['customer_name'] ?? 'Walk-in Customer') . "</td></tr>";
echo "<tr><th>Grand Total</th><td>" . grocery_money($s['grand_total'] ?? 0) . "</td></tr>";
echo "<tr><th>Paid / Due</th><td>" . grocery_money($s['paid_amount'] ?? 0) . " / " . grocery_money($s['due_amount'] ?? 0) . "</td></tr>";
echo "</table>";

Form::open(["route" => "GrocerySale/delete/$sid"]);
echo csrf_field();
Form::input(["name" => "id", "type" => "hidden", "value" => $sid]);
echo "<a class='btn btn-secondary' href='$base_url/GrocerySale'>Cancel</a> ";
Form::input(["name" => "delete", "class" => "btn btn-danger", "value" => "Delete Sale", "type" => "submit"]);
Form::close();

Card::close();
Col::close();
Row::close();
Page::close();
