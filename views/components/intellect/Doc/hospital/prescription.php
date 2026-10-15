<style>
    .s{
        display:flex;
        justify-content:center;
        align-items:center;
        margin:0;
        padding:0;
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
  .label{font-weight:bold}

  .r1{margin-bottom:30px;flex-direction:column;}
  .r3{display:flex;justify-content:center;align-items:center;padding:20px}
  label{width:200px}
</style>


<div>

<?php
  $patient=$doc->patient;//$arg["patient"];
  $date1 = $patient->dob;
  $date2 = date("Y-m-d H:i:s"); //current date and time 
  $diff = abs(strtotime($date2) - strtotime($date1)); 
  $years = floor($diff / (365*60*60*24));//sec to year


?>
</div>

<section class="s">
<div class="w">

<div class="r r1">
  
<?php //print_r($doc->patient)?>

  <input type="hidden" id="patient_id" value="<?=$patient->id?>" />
  <div>
    <label >Patient Name :</label> 
    <input id="patient_name" type="text" value="<?=$patient->name?>" readonly />
  </div>
  <div>
    <label > Age :</label> 
    <input id="patient_age"  type="text" value="<?=$years?>" readonly />
  </div>
</div>
<div class="r">
   <div class="c c1">
       <div>
         <h4>CC</h4>
         <textarea id="cc"></textarea>
       </div>
       <div>
         <h4>RF</h4>
         <textarea id="rf"></textarea>
       </div>
       <div>
         <h4>Investigation</h4>
         <textarea id="inv"></textarea>
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
                      echo HmsMedicine::html_select(["name"=>"medicine","class"=>"search"]);
                    ?>               
            </td>
            <td>
                <select  id="dose" style="width:100%">
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
<div class="r r3">
  <textarea id="advice"></textarea>
  <input type="button" id="btnPrescription" value="Save" />
</div>
</div>

</section>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script>

 
$(function(){


  let base_url=`<?= $base_url ?>/api`;
     
     let medicines=[];
 
 
     $("#add").on("click",function(){       
       add(); 
     });

     $("#btnPrescription").on("click",function(){
      
       let patient_id=$("#patient_id").val();       
       let cc=$("#cc").val();
       let rf=$("#rf").val();
       let inv=$("#inv").val();
       let advice=$("#advice").val();

       let rxObj={
        patient_id:patient_id,
        cc:cc,
        rf:rf,
        investigation:inv,
        medicines:medicines,
        advice:advice,
        consultant_id:1
       }

       //console.log(rxObj);

       //call api to save prescription

       $.ajax({
        type:"post",
        url:`${base_url}/prescription/save`,
        data:rxObj,
        success:function(res){
         console.log(res);
        }
       });

       
       medicines=[];

       printRx();
       clear();
    });
 
    function clear(){
      $("#cc").val("");
      $("#rf").val("");
      $("#inv").val("");
      // $("#patient_name").val("");
      // $("#patient_age").val("");
    }
 
     function add(){
 
       let medicine={
         medicine_id:$("#medicine").val(),
         medicine_name:$("#medicine option:selected").text(),
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
     html+=`<tr><td>${medicine.medicine_name}</td><td>${medicine.dose}</td><td>${medicine.days}</td><td>${medicine.suggestion}</td></tr>`;
 
    });
 
    $("#rx").html(html);
 
 
   }
 


  


});


</script>