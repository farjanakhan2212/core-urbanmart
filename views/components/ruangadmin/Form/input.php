<?php
$arg['type'] = isset($arg['type']) ? $arg['type'] : 'text';
$arg['label'] = isset($arg['label']) ? $arg['label'] : '';
$arg['name'] = isset($arg['name']) ? $arg['name'] : '';
$arg['class'] = isset($arg['class']) ? $arg['class'] : 'form-control';
$arg['value'] = isset($arg['value']) ? $arg['value'] : '';
$arg['placeholder'] = isset($arg['placeholder']) ? $arg['placeholder'] : '';
$arg['required'] = isset($arg['required']) ? $arg['required'] : '';
$arg['checked'] = isset($arg['checked']) ? $arg['checked'] : '';
$css_class = isset($arg['class']) ? $arg['class'] : 'form-control';
$id = isset($arg['value_column']) ? $arg['value_column'] : (isset($id) ? $id : 'id');
$name = isset($arg['display_column']) ? $arg['display_column'] : (isset($name) ? $name : 'name');
?>
<div class='form-group row'>
    <?php
    if($arg['type']!="hidden" && $arg['type']!="submit" && $arg['type']!="reset"){
        echo "<label for='{$arg['name']}' class='col-sm-2 col-form-label'>{$arg['label']}</label>";
    }
    ?>

    <div class='col-sm-10'>

    <?php if(isset($arg['table'])){ ?>

        <select id='<?=$arg["name"]?>' name='<?=$arg["name"]?>' class='form-select' style='width:100%'>

          <?php
          $result=$db->query("select $id,$name from {$tx}{$arg["table"]}");
          while(list($id,$name)=$result->fetch_row()){
            if($id==$arg["value"]){
              echo "<option value='$id' selected>$name</option>";
            }else{
              echo "<option value='$id'>$name</option>";
            }
          }?>
          </select>

    <?php

      }else{//Input tag

            if($arg["type"]=="radio" || $arg["type"]=="checkbox"){
               $css_class="form-check-input";
            }

            if($arg["type"]=="textarea"){

              echo "<textarea class='$css_class' name='{$arg["name"]}' id='{$arg["name"]}' placeholder='{$arg["placeholder"]}' {$arg["required"]}>{$arg["value"]}</textarea>";

            }elseif($arg["type"]=="file"){

              echo  "<input type='{$arg["type"]}' class='$css_class' name='{$arg["name"]}' id='{$arg["name"]}' value='{$arg["value"]}' placeholder='{$arg["placeholder"]}' {$arg["required"]} {$arg["checked"]}>";

              if($arg["value"]!=""){
                 echo "<img src='{$base_url}/img/{$arg["value"]}' width='200' />";
               }

            }else{
              echo "<input type='{$arg["type"]}' class='$css_class' name='{$arg["name"]}' id='{$arg["name"]}' value='{$arg["value"]}' placeholder='{$arg["placeholder"]}' {$arg["required"]} {$arg["checked"]}>";
            }
        }
     ?>

</div>
</div>