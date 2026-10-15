<?php
$company = Company::find(1);
$receiptNo = (MoneyReceipt::get_last_id() ? MoneyReceipt::get_last_id() : 0) + 1;
$today = date('d-M-Y');
?>
<style>
    .receipt-container {
      max-width: 700px;
      margin: auto;
      border: 1px solid #ccc;
      padding: 30px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      background-color:#fff;
    }

    .header {
      text-align: center;
      margin-bottom: 20px;
    }

    .header h2 {
      margin: 0;
    }

    .receipt-details {
      margin-bottom: 30px;
    }

    .receipt-details table {
      width: 100%;
      border-collapse: collapse;
    }

    .receipt-details td {
      padding: 5px;
    }

    .items-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    .items-table th,
    .items-table td {
      border: 1px solid #ccc;
      padding: 8px;
      text-align: left;
    }

    .total {
      text-align: right;
      font-size: 18px;
      margin: 10px 0;
    }

    .footer {
      margin-top: 40px;
      display: flex;
      justify-content: space-between;
    }

    .signature {
      text-align: center;
      margin-top: 50px;
    }

    .signature-line {
      border-top: 1px solid #000;
      width: 200px;
      margin: auto;
      padding-top: 5px;
    }
</style>

<div class="receipt-container">
  <div class="header">
    <?php if ($company && !empty($company->logo)) echo "<img src='{$base_url}/img/{$company->logo}' width='100' />"; ?>
    <div>
    <h4><?= $company ? htmlspecialchars($company->name) : 'UrbanMart Grocery' ?></h4>
    <p><?= $company && $company->street_address ? htmlspecialchars($company->street_address) : '123 Main St, City, Country' ?></p>
    <p>Phone: <?= $company && $company->mobile ? htmlspecialchars($company->mobile) : '123-456-7890' ?></p>
    <h3>Money Receipt</h3>
   </div>
  </div>

  <div class="receipt-details">
    <table>
      <tr>
        <td><strong>Receipt No:</strong> MR-<?= $receiptNo ?></td>
        <td><strong>Date:</strong> <span id="date"><?= $today ?></span></td>
      </tr>
      <tr>
        <td><strong>Received From:</strong> <?= Customer::html_select("customer_id") ?></td>
        <td><strong>Payment Method:</strong> Cash</td>
      </tr>
    </table>
  </div>

  <table class="items-table">
    <thead>
      <tr>
        <th>Description</th>
        <th>Unit</th>
        <th>Price</th>
        <th>Amount</th>
        <th></th>
      </tr>
      <tr>
        <th><?= Product::html_select("product_id") ?></th>
        <th><input type="text" style="width:100%" name="unit" id="unit" value="1" /></th>
        <th><input type="text" style="width:100%" name="price" id="price" /></th>
        <th></th>
        <th><input type="button" value=" + " style="width:100%" name="btnAdd" id="btnAdd" /></th>
      </tr>
    </thead>
    <tbody id="tbody">
    </tbody>
  </table>
  <div class="row">
    <div class="col-6">
      <input type="button" class="btn btn-primary" onclick="CreateMr()" name="btnProcess" value="Create Money Receipt" />
      <span id="status" class="ml-2"></span>
    </div>
    <div class="col-6">
      <div class="total">
        Total <strong id="total">0</strong>
      </div>
    </div>
  </div>

  <div class="footer">
    <div class="signature">
      <div class="signature-line">Customer Signature</div>
    </div>
  </div>
</div>

<script>
let base_url="<?= $base_url ?>/api";
let list_url="<?= $base_url ?>/moneyreceipt";
let cart=[];

document.querySelector("#btnAdd").addEventListener("click",(e)=>{
    let unit=document.querySelector("#unit").value;
    let price=document.querySelector("#price").value;
    let product_id=document.querySelector("#product_id").value;
    let product_name=document.querySelector("#product_id").options[document.querySelector("#product_id").selectedIndex].text;

    if(!product_id || !unit || !price || parseFloat(unit)<=0 || parseFloat(price)<=0){
        alert("Select a product and enter unit and price.");
        return;
    }

    let lineTotal=parseFloat(unit)*parseFloat(price);
    let json={id:cart.length+1,desc:product_name,product_id:product_id,qty:unit,price:price,lineTotal:lineTotal};
    cart.push(json);
    printCart();
});

function printCart(){
    let html="";
    let total=0;
    cart.forEach((item)=>{
        html+="<tr>";
        html+=`<td>${item.desc}</td>`;
        html+=`<td>${item.qty}</td>`;
        html+=`<td>${item.price}</td>`;
        html+=`<td>${item.lineTotal.toFixed(2)}</td>`;
        html+=`<td><input type="button" onclick="del(${item.id})" value="del" /></td>`;
        html+="</tr>";
        total+=item.lineTotal;
    });
    document.querySelector("#tbody").innerHTML=html;
    document.querySelector("#total").innerHTML=total.toFixed(2);
}

function del(id){
    let index=cart.findIndex((item)=>item.id==id);
    cart.splice(index,1);
    printCart();
}

async function CreateMr(){
    if(!cart.length){
        alert("Add at least one item first.");
        return;
    }
    if(!confirm("Are you sure you want to create this money receipt?")) return;

    let status=document.querySelector("#status");
    status.textContent="Saving...";

    let customer_id=document.querySelector("#customer_id").value;
    let total=document.querySelector("#total").innerHTML;
    let today="<?= date('Y-m-d') ?>";

    let jsonData={
        created_at:today,
        updated_at:today,
        customer_id:customer_id,
        remark:"",
        receipt_total:total,
        items:cart
    };

    try{
        let response=await fetch(`${base_url}/moneyreceipt/save`,{
            method:"POST",
            headers:{"Content-Type":"application/json"},
            body:JSON.stringify(jsonData)
        });
        let json=await response.json();
        if(json && json.success==="yes"){
            status.innerHTML='<span class="text-success">Saved successfully. Redirecting...</span>';
            setTimeout(()=>{ window.location.href=list_url; }, 600);
        }else{
            status.innerHTML='<span class="text-danger">Save failed. Please try again.</span>';
        }
    }catch(error){
        console.log(error);
        status.innerHTML='<span class="text-danger">Request failed. Please try again.</span>';
    }
}
</script>