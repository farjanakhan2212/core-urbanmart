<?php $company=Company::find(1); ?>
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
      margin-top: 10px;
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
    <img src='<?=$base_url?>/img/<?=$company->logo?>' width='100' />
    <div>
    <h4><?=$company->name?></h4>
    <p>123 Main St, City, Country</p>
    <p>Phone: 123-456-7890 | Email: info@abc.com</p>
    <h3>Money Receipt</h3>
   </div>
  </div>

  <div class="receipt-details">
    <table>
      <tr>
        <td><strong>Receipt No:</strong> MR-<?=MoneyReceipt::get_last_id()+1?></td>
        <td><strong>Date:</strong> <span id="date"><?=date("d-M-Y")?></span></td>
      </tr>
      <tr>
        <td><strong>Received From:</strong> <?=Customer::html_select("customer_id")?></td>
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
        <th><?=Product::html_select("product_id")?></th>
        <th><input type="text" style="width:100%" name="unit" id="unit" value="1" /></th>
        <th><input type="text" style="width:100%" name="price" id="price" /></th>
        <th></th>
        <th><input type="button" value=" + " style="width:100%" name="btnAdd" id="btnAdd" /></th>
      </tr>
    </thead>
    <tbody id="tbody">     
    </tbody>  
  </table>
  <input type="button" class="btn btn-primary" onclick="CreateMr()"  name="btnProcess" value="Create Money Receipt" />
  <div class="total">
    Total <strong id="total">0</strong>
    
  </div>
  
  <div class="footer">  
    <div class="signature">
      <div class="signature-line">Customer Signature</div>
    </div>
  </div>

  
</div>

<script>
    let base_url="<?= $base_url ?>/api";
    
    let cart=[];
    document.querySelector("#btnAdd").addEventListener("click",(e)=>{
       
       // let desc=document.querySelector("#description").value;
        let unit=document.querySelector("#unit").value;
        let price=document.querySelector("#price").value;
        let product_id=document.querySelector("#product_id").value;
        let product_name= document.querySelector("#product_id").options[document.querySelector("#product_id").selectedIndex].text;
       
        let lineTotal=unit*price;      

        let json={id:cart.length+1,desc:product_name,product_id:product_id,qty:unit,price:price,lineTotal:lineTotal};

        cart.push(json);
        
       console.log(cart);

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
            html+=`<td>${item.lineTotal}</td>`;
            html+=`<td><input type="button" onclick="del(${item.id})" value="del" /></td>`;
            html+="</tr>";
            total+=item.lineTotal;
        });

      document.querySelector("#tbody").innerHTML=html;
      document.querySelector("#total").innerHTML=total;

    }

    function del(id){
      let index=cart.findIndex((item)=>{
        return item.id==id;
      });
       cart.splice(index,1);
       printCart();
    }

    async function CreateMr(){

        if(confirm("Are you sure?")){
          let date=document.querySelector("#date").innerHTML;
          let customer_id=document.querySelector("#customer_id").value;
          let total=document.querySelector("#total").innerHTML;
           
          let jsonData={
              created_at:date,
              updated_at:date,
              customer_id:customer_id,
              remark:"Na",
              receipt_total:total,
              items:cart
          }

          console.log(jsonData);

          try{

            let response=await fetch(`${base_url}/moneyreceipt/save`,{
              method:"POST",
              headers:{"Content-Type":"application/json"},
              body:JSON.stringify(jsonData)
            });

            let json=response.json();           

            cart=[];
            printCart();

          }catch(error){
            console.log(error);
          }

      }

   }
  
</script>