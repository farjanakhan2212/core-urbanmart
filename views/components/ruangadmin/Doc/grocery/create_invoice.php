<?php
$company = Company::find(1);
$invoiceNo = (Invoice::get_last_id() ? Invoice::get_last_id() : 0) + 1;
?>
<div class="card mb-3">
    <div class="card-body">
        <div class="row align-items-center text-center mb-3">
            <div class="col-sm-6 text-sm-start">
            <?php if ($company && !empty($company->logo)) echo "<img src='{$base_url}/img/{$company->logo}' width='100' />"; ?>
            </div>
            <div class="col text-sm-end mt-3 mt-sm-0">
                <h2 class="mb-3">Invoice</h2>
                <h5><?= $company ? htmlspecialchars($company->name) : 'UrbanMart Grocery' ?></h5>
                <p class="fs--1 mb-0"><?= $company && $company->street_address ? htmlspecialchars($company->street_address) : '' ?><br /><?= $company && $company->area ? htmlspecialchars($company->area) : '' ?><?= $company && $company->city ? ', ' . htmlspecialchars($company->city) : '' ?></p>
            </div>
            <div class="col-12">
                <hr />
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col">
                <h6 class="text-500">Invoice to</h6>
                <h5><?= Customer::html_select("customer_id") ?></h5>
            </div>
            <div class="col-sm-auto ms-auto">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless fs--1">
                        <tbody>
                            <tr>
                                <th class="text-sm-end">Invoice No:</th>
                                <td><?= $invoiceNo ?></td>
                            </tr>
                            <tr>
                                <th class="text-sm-end">Invoice Date:</th>
                                <td id="date"><?= date('d-M-Y') ?></td>
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
                        <th class="border-0"><?= Product::html_select("product_id") ?></th>
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
                        <td class="fw-semi-bold" id="subTotal">0.00</td>
                    </tr>
                    <tr>
                        <th class="text-900">Tax:</th>
                        <td class="fw-semi-bold" id="tax">0.00</td>
                    </tr>
                    <tr class="border-top">
                        <th class="text-900">Total:</th>
                        <td class="fw-semi-bold" id="netTotal">0.00</td>
                    </tr>
                    <tr class="border-top border-top-2 fw-bolder text-900">
                        <th>Amount Due:</th>
                        <td id="due">0.00</td>
                    </tr>
                    <tr>
                      <th colspan="5">
                      <input type="button" class="btn btn-primary" onclick="CreateInvoice()" name="btnProcess" value="Create Invoice" />
                      <span id="status" class="ml-2"></span>
                    </th>
                    </tr>
                </table>

            </div>

        </div>

    </div>
    <div class="card-footer bg-light">
        <p class="fs--1 mb-0"><strong>Notes: </strong>We really appreciate your business and if there's anything
            else we can do, please let us know!</p>
    </div>
</div>

<script>
let base_url="<?= $base_url ?>/api";
let list_url="<?= $base_url ?>/invoice";
let cart=[];

document.querySelector("#btnAdd").addEventListener("click",(e)=>{
    let unit=document.querySelector("#unit").value;
    let price=document.querySelector("#price").value;
    let product_id=document.querySelector("#product_id").value;
    let product_name=document.querySelector("#product_id").options[document.querySelector("#product_id").selectedIndex].text;

    if(!product_id || !unit || !price || parseFloat(unit)<=0 || parseFloat(price)<=0){
        alert("Select a product and enter quantity and rate.");
        return;
    }

    let vat=0;
    let discount=0;
    let lineTotal=parseFloat(unit)*parseFloat(price)-discount+vat;
    let json={id:cart.length+1,desc:product_name,product_id:product_id,qty:unit,price:price,discount:discount,vat:vat,lineTotal:lineTotal};
    cart.push(json);
    printCart();
});

function printCart(){
    let html="";
    let total=0;
    cart.forEach((item)=>{
        html+="<tr>";
        html+=`<td class="align-middle"><h6 class="mb-0 text-nowrap">${item.desc}</h6></td>`;
        html+=`<td class="align-middle text-center">${item.qty}</td>`;
        html+=`<td class="align-middle text-end">${item.price}</td>`;
        html+=`<td class="align-middle text-end">${item.lineTotal.toFixed(2)}</td>`;
        html+=`<td><input type="button" onclick="del(${item.id})" value="del" /></td>`;
        html+="</tr>";
        total+=item.lineTotal;
    });
    document.querySelector("#tbody").innerHTML=html;
    document.querySelector("#subTotal").innerHTML=total.toFixed(2);
    document.querySelector("#tax").innerHTML="0.00";
    document.querySelector("#netTotal").innerHTML=total.toFixed(2);
    document.querySelector("#due").innerHTML=total.toFixed(2);
}

function del(id){
    let index=cart.findIndex((item)=>item.id==id);
    cart.splice(index,1);
    printCart();
}

async function CreateInvoice(){
    if(!cart.length){
        alert("Add at least one item first.");
        return;
    }
    if(!confirm("Are you sure you want to create this invoice?")) return;

    let status=document.querySelector("#status");
    status.textContent="Saving...";

    let customer_id=document.querySelector("#customer_id").value;
    let total=document.querySelector("#netTotal").innerHTML;
    let today="<?= date('Y-m-d') ?>";

    let jsonData={
        created_at:today,
        updated_at:today,
        customer_id:customer_id,
        remark:"",
        payment_term:"CASH",
        invoice_total:total,
        paid_total:total,
        previous_due:0,
        items:cart
    };

    try{
        let response=await fetch(`${base_url}/invoice/save`,{
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