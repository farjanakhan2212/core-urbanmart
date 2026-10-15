<div>
  <?php echo $arg["id"]?>
Division<br>
<?php
  echo  Division::html_select("division");
 ?>
 </div>
<div>
    District
 <select id="district">

</select>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
 
 $(function(){
   
    $("#division").on("change",function(e){

        let id=e.target.value;
        
        $.ajax({
            type:"GET",
            url:"<?= $base_url ?>/api/district",
            success:function(res){
              let districts=JSON.parse(res).districts;
              //console.log(districts)

               let html="";
              districts.forEach((v)=>{

                if(id==v.division_id){
                  html+=`<option value="${v.id}">${v.name}</option>`;
                }
            });

              $("#district").html(html);


            }
        });

    });
    

 });

</script>