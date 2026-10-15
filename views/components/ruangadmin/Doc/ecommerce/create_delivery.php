<style>
  #cmbCustomer {
    padding: 5px;
  }
</style>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
<!-- Content Header (Page header) -->

        <!-- Main content -->
        <div class="invoice p-3 mb-3">
          <!-- title row -->
          <div class="row">
            <div class="col-12">
              <h4>
                <i class="fas fa-globe"></i> <?=Company::find(1)->name?>.
                <small class="float-right">Date: <?php echo date("d M Y") ?></small>
              </h4>
            </div>
            <!-- /.col -->
          </div>
          <!-- info row -->
          <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
              From
              <address>
                <?php $company=Company::find(1); ?>
                <strong><?=$company->name?></strong><br>
                <?=$company->area?>,               
                <?=$company->city?><br>
                Email: <?=$company->email?><br>
                Contact: <?=$company->mobile?>
              </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
              Shipper
              <address>
                <?php
                  echo Shipper::html_select("shipper");
                ?>
                <div id="customer-info"></div>
              </address>
              <div>
                Shipping Address:<br>
                <textarea id="txtShippingAddress"></textarea>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">

              <table>
              <tr>                
                <td><b>Order ID:</b></td>
                <td><form name="f"><input type="text" style="width:60px" id="order_id" name="order_id"  /><input type="button" id="go" value="Go" onclick="printCart(document.f.order_id.value)" /></form></td>
              </tr>
                <tr>
                
                  <td><b>Delivery ID:</b></td>
                  <td><input type="text" style="width:60px" value="<?php echo Order::get_last_id() + 1; ?>" readonly /></td>
                </tr>
                <tr>
                  <td><b>Delivery Date:</b></td>
                  <td><input type="text" id="txtOrderDate" value=<?php echo date("d-m-Y"); ?> /></td>
                </tr>
              
              </table>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->

          <!-- Table row -->
          <div class="row">
            <div class="col-12 table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>SN</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Discount</th>
                    <th>LineTotal</th>
                 
                  </tr>
                 
                </thead>
                <tbody id="items">

                </tbody>
              </table>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->

          <div class="row">
            <!-- accepted payments column -->
            <div class="col-6">
              <strong>Remark</strong><br>
              <textarea id="txtRemark"></textarea>
            </div>
            <!-- /.col -->
            <div class="col-6">
              <p class="lead">Amount Due 2/22/2014</p>

              <div class="table-responsive">
                <table class="table">
                  <tbody>
                    <tr>
                      <th style="width:50%">Subtotal:</th>
                      <td id="subtotal">0</td>
                    </tr>
                    <tr>
                      <th style="width:50%">Tax:</th>
                      <td id="tax">0</td>
                    </tr>
                    <tr>
                      <th>Total:</th>
                      <td id="net-total">0</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->

          <!-- this row will not appear when printing -->
          <div class="row no-print">
            <div class="col-12">
              <a href="invoice-print.html" rel="noopener" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> Print</a>
              <input type="button" value="Process Delivery"  onclick="ProcessDelivery()"  class="btn btn-success float-right">
             
            </div>
          </div>
        </div>
        <!-- /.invoice -->

<script>

let base_url=`<?= $base_url ?>/api`;
let products =[];
const cart = new Cart("delivery");

printCart();


async function printCart(order_id=1) {
  //let products = cart.getCart();

    order_id=document.querySelector("#order_id").value;

    const response =  await fetch(`${base_url}/delivery/getOrderDetails/${order_id}`,{
      method: "GET",
      headers: {
      "Accept":"application/json",         
    }   
    });

    let json=await response.json(); 

    //console.log(json.products);

     products=json.products
    let sn = 1;
    let $bill = "";
    let subtotal = 0;

    if (products != null) {
      products.forEach(function(item, i) {
        //console.log(item.name);
        let lineTotal=item.qty*item.price-item.discount;
        subtotal += lineTotal;
        
        let $html = "<tr>";
        $html += "<td>";
        $html += sn;
        $html += "</td>";
        $html += "<td>";
        $html += item.name;
        $html += "</td>";
        $html += "<td data-field='price'>";
        $html += item.price;
        $html += "</td>";
        $html += "<td data-field='qty'>";
        $html += item.qty;
        $html += "</td>";
        $html += "<td data-field='discount'>";
        $html += item.discount;
        $html += "</td>";
        $html += "<td data-field='subtotal'>";
        $html += lineTotal;
        $html += "</td>";
        // $html += "<td>";
        // $html += "<input type='button' class='delete' data-id='" + item.item_id + "' value='-'/>";
        // $html += "</td>";
        $html += "</tr>";
        $bill += $html;
        sn++;
      });
    }

    //$("#items").html($bill);
    document.querySelector("#items").innerHTML=$bill;
    // //Order Summary
    document.querySelector("#subtotal").innerHTML=subtotal;
    //$("#subtotal").html(subtotal);
    let tax = (subtotal * 0.05).toFixed(2);
    //$("#tax").html(tax);
    document.querySelector("#tax").innerHTML=tax;
    //$("#net-total").html(parseFloat(subtotal) + parseFloat(tax));
    document.querySelector("#net-total").innerHTML=parseFloat(subtotal) + parseFloat(tax);
}



async function ProcessDelivery(){

if(confirm("Are you sure?")){

let person_id = 1;
let create_at = document.querySelector("#txtOrderDate").value; // $("#txtOrderDate").val();
let shipped_at =document.querySelector("#txtOrderDate").value; // $("#txtOrderDate").val();
let shipper_id=1;
let delivery_status_id=1;
let shipping_address =document.querySelector("#txtShippingAddress").value; // $("#txtShippingAddress").val();
let remark =document.querySelector("#txtRemark").value; //  $("#txtRemark").val();
// //let order_total = $("#net-total").text();          

let _data={
   "order_id":document.querySelector("#order_id").value, //  $("#order_id").val(), 
   "person_id": person_id,           
   "create_at": create_at,
   "shipper_id": shipper_id,
   "shipped_at": shipped_at,           
   "delivery_status_id": delivery_status_id,
   "products": products
 }

 console.log(_data);

 //   $.ajax({
 //     url: `${base_url}/delivery/save`,
 //     type: 'POST',
 //     data:_data,
 //     success: function(res) {
 //       console.log(res);
 //       cart.clearCart();
 //       $("#items").html("");
 //     }
 //   });

       try {   
           const response =  await fetch(`${base_url}/delivery/save`,{
               method: "POST",
               headers: {
                   "Content-Type":"application/json",
                   "Accept":"application/json",         
               },
               body:JSON.stringify(_data)  
           });          

          } catch (error) {
              console.error("Error:", error);
          }



}//end if

}//


</script>
