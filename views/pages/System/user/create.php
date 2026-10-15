<?php
if(isset($_POST["create"])){
	$errors=[];

	if(count($errors)==0){
		$user=new User();
		$user->name=$_POST["name"];
		$user->password=password_hash($_POST["password"],PASSWORD_BCRYPT);
		$user->email=$_POST["email"];
		$user->created_at=$now;
		$user->updated_at=$now;	

		$user->save();
	}else{
		 print_r($errors);
	}
}
?>



<?php

echo Page::title(["title"=>"Create User"]);
echo Page::body_open();
echo Page::context_open(["manage-button"=>"Manage User","route"=>"users"]);

echo Form::open(["route"=>"create-user"]);
	$html="";	
	echo Form::text(["label"=>"User Name","name"=>"name","type"=>"text","placeholder"=>"Enter Name"]);	
	echo Form::text(["label"=>"Password","name"=>"password","type"=>"password","placeholder"=>"Enter Password"]);
	echo Form::text(["label"=>"Email","name"=>"email","type"=>"text","placeholder"=>"Enter Email"]);	
	echo Form::button(["name"=>"create","value"=>"Save","type"=>"submit"]);
	
	echo Form::close();
	echo $html;

	echo Page::context_close();
   echo Page::body_close();
?>