<?php
$arg['label'] = isset($arg['label']) ? $arg['label'] : '';
$arg['name'] = isset($arg['name']) ? $arg['name'] : '';
$arg['class'] = isset($arg['class']) ? $arg['class'] : 'form-control';
$arg['value'] = isset($arg['value']) ? $arg['value'] : '';
$arg['placeholder'] = isset($arg['placeholder']) ? $arg['placeholder'] : '';
$arg['required'] = isset($arg['required']) ? $arg['required'] : '';
$arg['checked'] = isset($arg['checked']) ? $arg['checked'] : '';
?>
<div class='form-group row'>
    <input type='reset' class='<?=$arg["class"]?>' name='<?=$arg["name"]?>' id='<?=$arg["name"]?>' value='<?=$arg["value"]?>' placeholder='<?=$arg["placeholder"]?>' <?=$arg["required"]?> <?=$arg["checked"]?> />
</div>