<?php $company=Company::find(1); ?>  
<div class="card mb-3">
    <div class="card-body">
        <div class="row align-items-center text-center mb-3">
            <div class="col-sm-6 text-sm-start">
            <img src='<?=$base_url?>/img/<?=$company->logo?>' width='100' /> </div>
            <div class="col text-sm-end mt-3 mt-sm-0">
                <h2 class="mb-3">Invoice</h2>
                <h5><?=$company->name?></h5>
                <p class="fs--1 mb-0"><?=$company->street_address?><br /><?=$company->area?>, <?=$company->city?></p>
            </div>
            <div class="col-12">
                <hr />
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col">
                <h6 class="text-500">Invoice to</h6>
                <h5><?=Customer::html_select("customer_id")?></h5>
                <p class="fs--1">1954 Bloor Street West<br />Torronto ON, M6P 3K9<br />Canada</p>
                <p class="fs--1"><a href="mailto:example@gmail.com">example@gmail.com</a><br /><a
                        href="tel:444466667777">+4444-6666-7777</a></p>
            </div>
            <div class="col-sm-auto ms-auto">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless fs--1">
                        <tbody>
                            <tr>
                                <th class="text-sm-end">Invoice No:</th>
                                <td><?=Invoice::get_last_id()+1?></td>
                            </tr>                          
                            <tr>
                                <th class="text-sm-end">Invoice Date:</th>
                                <td id="date"><?=date("d-M-Y")?></td>
                            </tr>
                                                      
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="table-responsive scrollbar mt-4 fs--1">
            <table class="table table-striped border-bottom">
                <thead class="light">
                    <tr class="bg-primary text-white dark__bg-1000">
                        <th class="border-0">Products</th>
                        <th class="border-0 text-center">Quantity</th>
                        <th class="border-0 text-end">Rate</th>
                        <th class="border-0 text-end">Amount</th>
                        <th class="border-0 text-end"></th>
                    </tr>
                    <tr class="text-white dark__bg-1000">
                        <th class="border-0"><?=Product::html_select("product_id")?></th>
                        <th class="border-0 text-center"><input type="text" class="form-control" style="width:100%" name="unit" id="unit" value="1" /></th>
                        <th class="border-0 text-end"><input type="text" class="form-control" style="width:100%" name="price" id="price" /></th>
                        <th class="border-0 text-end"></th>
                        <th class="border-0 text-end"><input class="form-control" type="button" value=" + " style="width:100%" name="btnAdd" id="btnAdd" /></th>
                    </tr>
                </thead>
                <tbody id="tbody">
                  
                  
                </tbody>
            </table>
        </div>
        <div class="row justify-content-end">
            <div class="col-auto">
                <table class="table table-sm table-borderless fs--1 text-end">
                    <tr>
                        <th class="text-900">Subtotal:</th>
                        <td class="fw-semi-bold" id="subTotal">$00.00 </td>
                    </tr>
                    <tr>
                        <th class="text-900">Tax 3%:</th>
                        <td class="fw-semi-bold" id="tax">$00.00</td>
                    </tr>
                    <tr class="border-top">
                        <th class="text-900">Total:</th>
                        <td class="fw-semi-bold" id="netTotal">$00.00</td>
                    </tr>
                    <tr class="border-top border-top-2 fw-bolder text-900">
                        <th>Amount Due:</th>
                        <td id="due">$00.00</td>
                    </tr>
                    <tr>
                      <th colspan="5">
                      <input type="button" class="btn btn-primary" onclick="CreateInvoice()"  name="btnProcess" value="Create Invoice" />
                    </th>
                    </tr>
                </table>
               
            </div>
            
        </div>
        
    </div>
    <div class="card-footer bg-light">
        <p class="fs--1 mb-0"><strong>Notes: </strong>We really appreciate your business and if there’s anything
            else we can do, please let us know!</p>
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
        let vat=0;
        let discount=0;
        let lineTotal=unit*price-discount+vat;      

        let json={id:cart.length+1,desc:product_name,product_id:product_id,qty:unit,price:price,discount:discount,vat:vat,lineTotal:lineTotal};

        cart.push(json);
        
       console.log(cart);

        printCart();
    });


    function printCart(){
        let html="";
        let total=0;
        cart.forEach((item)=>{

            html+="<tr>";
            html+=`<td class="align-middle"><h6 class="mb-0 text-nowrap">${item.desc}</h6><p class="mb-0">${item.id}</p></td>`;
            html+=`<td class="align-middle text-center">${item.qty}</td>`;
            html+=`<td class="align-middle text-end">${item.price}</td>`;
            html+=`<td class="align-middle text-end">${item.lineTotal}</td>`;
            html+=`<td><input type="button" onclick="del(${item.id})" value="del" /></td>`;
            html+="</tr>";
            total+=item.lineTotal;
        });

      document.querySelector("#tbody").innerHTML=html;
      document.querySelector("#subTotal").innerHTML=total;
      document.querySelector("#netTotal").innerHTML=total;

    }


    function del(id){
      let index=cart.findIndex((item)=>{
        return item.id==id;
      });
       cart.splice(index,1);
       printCart();
    }



    async function CreateInvoice(){        

      if(confirm("Are you sure?")){

        let date=document.querySelector("#date").innerHTML;
        let customer_id=document.querySelector("#customer_id").value;
        let total=document.querySelector("#netTotal").innerHTML;
        
        
        let jsonData={
            created_at:date,
            updated_at:date,
            customer_id:customer_id,
            remark:"Na",
            payment_term:"CASH",
            invoice_total:total,
            paid_total:total,
            previous_due:0,
            items:cart
        }

        console.log(jsonData);

        let response=await fetch(`${base_url}/invoice/save`,{
        method:"POST",
        headers:{"Content-Type":"application/json"},
        body:JSON.stringify(jsonData)
      });

      let json=response.json();
      console.log(json);

      cart=[];
      printCart();

      }

}

</script>