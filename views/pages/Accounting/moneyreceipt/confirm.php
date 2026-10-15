<?php
Page::open();
Row::open();
Col::open();
Card::open(["title"=>"Confirm Delete"]);
global $base_url;
echo "<div class='alert alert-warning'><strong>Warning:</strong> This permanently deletes the money receipt and all of its line items. This action cannot be undone.</div>";
Form::open(["route"=>"moneyreceipt/delete/$id"]);
echo csrf_field();

echo MoneyReceipt::html_row_details($id);
Form::input(["name"=>"id", "type"=>"hidden", "value"=>$id]);
echo "<a class='btn btn-secondary' href='$base_url/moneyreceipt'>Cancel</a> ";
Form::input(["name"=>"delete", "class"=>"btn btn-danger", "value"=>"Delete", "type"=>"submit"]);
Form::close();
Card::close();
Col::close();
Row::close();
Page::close();
