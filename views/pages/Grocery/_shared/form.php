<?php
$gclass = $_GET['class'] ?? 'GroceryCategory';
$gid = (int)($_GET['id'] ?? 0);
$grow = $gid ? call_user_func([$gclass, 'findRow'], $gid) : null;
Page::open();
Row::open();
Col::open(["col" => 8]);
Card::open(['title' => $grow ? 'Edit Record' : 'Create Record']);
Form::open(['route' => $grow ? $gclass . '/update/' . $gid : $gclass . '/save']);
grocery_render_form($gclass, $grow);
Form::close();
Card::close();
Col::close();
Row::close();
Page::close();