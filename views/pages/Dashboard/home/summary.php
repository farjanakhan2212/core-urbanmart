<?php
global $tx;
$dueTotal = (float)GrocerySale::scalar("SELECT IFNULL(SUM(due_amount),0) FROM {$tx}grocery_sales WHERE status='completed'")
    + (float)GroceryPurchase::scalar("SELECT IFNULL(SUM(due_amount),0) FROM {$tx}grocery_purchases WHERE status='received'");
$salesTotal = (float)GrocerySale::scalar("SELECT IFNULL(SUM(grand_total),0) FROM {$tx}grocery_sales WHERE status='completed'");
$stockValue = (float)GroceryProduct::scalar("SELECT IFNULL(SUM(purchase_price * stock_quantity),0) FROM {$tx}grocery_products WHERE status='active'");
$products = (int)GroceryProduct::scalar("SELECT COUNT(*) FROM {$tx}grocery_products WHERE status='active'");
$lowStock = (int)GroceryProduct::scalar("SELECT COUNT(*) FROM {$tx}grocery_products WHERE status='active' AND stock_quantity <= reorder_level");

Page::open();
  Row::open(["g"=>"3"]);
   Col::open(["col"=>4]);
    Card::open(["title"=>"Orders","class-title"=>"text-light","class-head"=>"bg bg-success","class-body"=>"bg bg-light text-success fs-5 fw-bolder d-flex justify-content-center align-items-center"]);
         echo GroceryOrder::countRows();
    Card::close();
   Col::close();

   Col::open(["col"=>4]);
   Card::open(["title"=>"Purchase","class-title"=>"text-light","class-head"=>"bg bg-danger","class-body"=>"bg bg-light text-danger fs-5 fw-bolder d-flex justify-content-center align-items-center"]);
   echo GroceryPurchase::countRows();
   Card::close();
  Col::close();

   Col::open(["col"=>4]);
   Card::open(["title"=>"Customer","class-title"=>"text-light","class-head"=>"bg bg-warning","class-body"=>"d-flex justify-content-center align-items-center fs-5 fw-bolder text-warning"]);
  echo GroceryCustomer::countRows();
  Card::close();
 Col::close();

 Row::close();

 Row::open(["g"=>"3"]);

 Col::open(["col"=>4]);
 Card::open(["title"=>"Products","class-title"=>"text-light","class-head"=>"bg bg-dark","class-body"=>"bg bg-light text-danger fs-5 fw-bolder d-flex justify-content-center align-items-center"]);
 echo $products;
 Card::close();
Col::close();

Col::open(["col"=>4]);
 Card::open(["title"=>"Account (Total Due)","class-title"=>"text-light","class-head"=>"bg bg-info","class-body"=>"bg bg-light fs-5 fw-bolder d-flex justify-content-center align-items-center"]);
 echo number_format($dueTotal, 2);
 Card::close();
Col::close();

Col::open(["col"=>4]);
 Card::open(["title"=>"Suppliers","class-title"=>"text-light","class-head"=>"bg bg-primary","class-body"=>"bg bg-light fs-5 fw-bolder d-flex justify-content-center align-items-center"]);
 echo GrocerySupplier::countRows();
 Card::close();
Col::close();

  Row::close();

  Row::open(["g"=>"3"]);
   Col::open(["col"=>4]);
    Card::open(["title"=>"Total Sales","class-title"=>"text-light","class-head"=>"bg bg-success","class-body"=>"bg bg-light fs-5 fw-bolder d-flex justify-content-center align-items-center"]);
         echo number_format($salesTotal, 2);
    Card::close();
   Col::close();

   Col::open(["col"=>4]);
   Card::open(["title"=>"Stock Value","class-title"=>"text-light","class-head"=>"bg bg-danger","class-body"=>"bg bg-light fs-5 fw-bolder d-flex justify-content-center align-items-center"]);
   echo number_format($stockValue, 2);
   Card::close();
  Col::close();
  Col::open(["col"=>4]);
  Card::open(["title"=>"Low Stock","class-title"=>"text-light","class-head"=>"bg bg-dark","class-body"=>"bg bg-light fs-5 fw-bolder d-flex justify-content-center align-items-center"]);
  echo $lowStock;
  Card::close();
 Col::close();

  Row::close();

 Page::close();
?>