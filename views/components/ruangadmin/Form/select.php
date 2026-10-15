<?php
$arg['label'] = isset($arg['label']) ? $arg['label'] : '';
$arg['name'] = isset($arg['name']) ? $arg['name'] : '';
$arg['table'] = isset($arg['table']) ? $arg['table'] : '';
$arg['value'] = isset($arg['value']) ? $arg['value'] : '';
$id = isset($arg['value_column']) ? $arg['value_column'] : (isset($id) ? $id : 'id');
$name = isset($arg['display_column']) ? $arg['display_column'] : (isset($name) ? $name : 'name');
?>
<div class='form-group row'>
<label class='col-sm-2 col-form-label'><?=$arg["label"]?></label>
<div class='col-sm-10'>
<select name='<?=$arg["name"]?>' id='<?=$arg["name"]?>' class='form-select' style='width:100%'>
<?php
if ($arg['table'] !== '') {
    $result = $db->query("select $id,$name from {$tx}{$arg["table"]}");
    while(list($id,$name)=$result->fetch_row()){
        if($id==$arg["value"]){
            echo "<option value='$id' selected>$name</option>";
        }else{
            echo "<option value='$id'>$name</option>";
        }
    }
}
?>
</select>
</div>
</div>