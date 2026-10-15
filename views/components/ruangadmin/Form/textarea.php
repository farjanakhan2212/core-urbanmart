<?php
$arg['label'] = isset($arg['label']) ? $arg['label'] : '';
$arg['name'] = isset($arg['name']) ? $arg['name'] : '';
$arg['class'] = isset($arg['class']) ? $arg['class'] : 'form-control';
$arg['value'] = isset($arg['value']) ? $arg['value'] : '';
$arg['placeholder'] = isset($arg['placeholder']) ? $arg['placeholder'] : '';
$arg['required'] = isset($arg['required']) ? $arg['required'] : '';
?>
<div class='form-group row'>
<label for='<?=$arg["name"]?>' class='col-sm-2 col-form-label'><?=$arg["label"]?></label>
<div class='col-sm-10'>
  <textarea class='<?=$arg["class"]?>' name='<?=$arg["name"]?>' id='<?=$arg["name"]?>' placeholder='<?=$arg["placeholder"]?>' <?=$arg["required"]?>><?=$arg["value"]?></textarea>
</div>
</div>