<?php
$arg['label'] = isset($arg['label']) ? $arg['label'] : '';
$arg['name'] = isset($arg['name']) ? $arg['name'] : '';
$arg['class'] = isset($arg['class']) ? $arg['class'] : 'form-control';
$arg['value'] = isset($arg['value']) ? $arg['value'] : '';
$arg['placeholder'] = isset($arg['placeholder']) ? $arg['placeholder'] : '';
$arg['required'] = isset($arg['required']) ? $arg['required'] : '';
$arg['checked'] = isset($arg['checked']) ? $arg['checked'] : '';
if ((string)$arg['value'] !== '' && !is_numeric($arg['value']) && strlen((string)$arg['value']) >= 10) {
    $arg['value'] = str_replace(' ', 'T', substr((string)$arg['value'], 0, 16));
}
?>
<div class='form-group row'>
    <label for='<?=$arg["name"]?>' class='col-sm-2 col-form-label'><?=$arg["label"]?></label>
    <div class='col-sm-10'>
        <input type='datetime-local' class='<?=$arg["class"]?>' name='<?=$arg["name"]?>' id='<?=$arg["name"]?>' value='<?=$arg["value"]?>' placeholder='<?=$arg["placeholder"]?>' <?=$arg["required"]?> <?=$arg["checked"]?> />
    </div>
</div>