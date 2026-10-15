<style>

    .s{
        display:flex;
        justify-content:center;
        align-items:center;
    }
    .w{       
        width: 90%;
       
        padding:20px;
        box-sizing:border-box;
    }
  .r{
    display:flex;
    flex-flow:row wrap;
  }
  .c{}
  .c1{
      width:30%;
  }
  .c2{
    width:70%;
  }
</style>

<div>
    <h1>Create Prescription</h1>
</div>


<section class="s">
<div class="w">

<div class="r">

</div>
<div class="r">
   <div class="c c1">
       <div>
         <h4>CC</h4>
         <textarea id="cc">

         </textarea>
       </div>
       <div>
         <h4>RF</h4>
         <textarea id="rf">
            
         </textarea>
       </div>
       <div>
         <h4>Investigation</h4>
         <textarea id="insv">
            
         </textarea>
       </div>
  </div>
  <div class="c c2">
       <table width="100%">
         <tr>
            <th>Medicine</th>
            <th>Dose</th>
            <th>Days</th>
            <th>Suggestion</th>
         </tr>
         <tr>
            <td>                
                    <?php
                      echo HmsMedicine::html_select("medicine");
                    ?>               
            </td>
            <td>
                <select id="dose" style="width:100%">
                    <option value="0+0+1">0+0+1</option>
                    <option value="0+1+0">0+1+0</option>
                    <option value="1+0+0">1+0+0</option>
                    <option value="1+0+1">1+0+1</option>
                    <option value="0+1+1">0+1+1</option>
                    <option value="1+1+0">1+1+0</option>
                    <option value="1+1+1">1+1+1</option>
                </select>
            </td>
            <td>
                <select id="days" style="width:100%">
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                </select>

            </td>
            <td><input type="text" id="suggestion" placeholder="suggestion" /></td>
            <td><input type="button" id="add" value="+" /></td>
         </tr>
         <tbody id="rx">
         </tbody>
      </table>
  </div>
</div>

</div>

</section>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script>

    let base_url=`<?= $base_url ?>/api`;
     
    let medicines=[];


    $("#add").on("click",function(){
      
      add();

    });


    function add(){

      let medicine={
        name:$("#medicine option:selected").text(),
        dose:$("#dose").val(),
        days:$("#days").val(),
        suggestion:$("#suggestion").val()
      }

      medicines.push(medicine);

      printRx();
      
  }

  function printRx(){  
   
   let html="";
   //console.log(medicines);
   medicines.forEach((medicine)=>{    
    html+=`<tr><td>${medicine.name}</td><td>${medicine.dose}</td><td>${medicine.days}</td><td>${medicine.suggestion}</td></tr>`;

   });

   $("#rx").html(html);


  }

</script>