<?php
$arg['name'] = isset($arg['name']) ? $arg['name'] : '';
$arg['value'] = isset($arg['value']) ? $arg['value'] : '';
?>
<input type='hidden' name='<?=$arg["name"]?>' id='<?=$arg["name"]?>' value='<?=$arg["value"]?>'  />