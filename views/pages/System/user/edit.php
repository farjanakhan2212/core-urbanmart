<?php
	$html="";

	echo Page::title(["title"=>"Edit User"]);
	echo Page::body_open();
	echo Page::context_open(["manage"=>"Manage User","route"=>"user"]);


	echo Form::open(["route"=>"edit-user"]);
	echo Form::text(["name"=>"id","type"=>"hidden","value"=>$user->id]);	
	echo Form::text(["label"=>"User Name","name"=>"name","type"=>"text","value"=>$user->name]);	
	echo Form::text(["label"=>"Password","name"=>"password","type"=>"password","placeholder"=>"Leave blank to keep current password"]);
	echo Form::text(["label"=>"Email","name"=>"email","type"=>"text","value"=>$user->email]);	
	
	echo Form::button(["name"=>"update","value"=>"Update","type"=>"submit"]);
	echo Form::close();

	echo Page::context_close();
	echo Page::body_close();

	//echo $html;
?>