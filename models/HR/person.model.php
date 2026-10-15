<?php
class Person implements JsonSerializable{
	public $id;
	public $name;
	public $position_id;
	public $sex;
	public $dob;
	public $doj;
	public $mobile;
	public $address;
	public $inactive;
	public $photo;

	public function __construct(){
	}
	public function set($id,$name,$position_id,$sex,$dob,$doj,$mobile,$address,$inactive,$photo){
		$this->id=$id;
		$this->name=$name;
		$this->position_id=$position_id;
		$this->sex=$sex;
		$this->dob=$dob;
		$this->doj=$doj;
		$this->mobile=$mobile;
		$this->address=$address;
		$this->inactive=$inactive;
		$this->photo=$photo;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}persons(name,position_id,sex,dob,doj,mobile,address,inactive,photo)values('$this->name','$this->position_id','$this->sex','$this->dob','$this->doj','$this->mobile','$this->address','$this->inactive','$this->photo')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}persons set name='$this->name',position_id='$this->position_id',sex='$this->sex',dob='$this->dob',doj='$this->doj',mobile='$this->mobile',address='$this->address',inactive='$this->inactive',photo='$this->photo' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}persons where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,position_id,sex,dob,doj,mobile,address,inactive,photo from {$tx}persons");
		$data=[];
		while($person=$result->fetch_object()){
			$data[]=$person;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,position_id,sex,dob,doj,mobile,address,inactive,photo from {$tx}persons $criteria limit $top,$perpage");
		$data=[];
		while($person=$result->fetch_object()){
			$data[]=$person;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}persons $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,position_id,sex,dob,doj,mobile,address,inactive,photo from {$tx}persons where id='$id'");
		$person=$result->fetch_object();
			return $person;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}persons");
		$person =$result->fetch_object();
		return $person->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Position Id:$this->position_id<br> 
		Sex:$this->sex<br> 
		Dob:$this->dob<br> 
		Doj:$this->doj<br> 
		Mobile:$this->mobile<br> 
		Address:$this->address<br> 
		Inactive:$this->inactive<br> 
		Photo:$this->photo<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbPerson"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}persons");
		while($person=$result->fetch_object()){
			$html.="<option value ='$person->id'>$person->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}persons $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,position_id,sex,dob,doj,mobile,address,inactive,photo from {$tx}persons $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-person\">New Person</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Position Id</th><th>Sex</th><th>Dob</th><th>Doj</th><th>Mobile</th><th>Address</th><th>Inactive</th><th>Photo</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Position Id</th><th>Sex</th><th>Dob</th><th>Doj</th><th>Mobile</th><th>Address</th><th>Inactive</th><th>Photo</th></tr>";
		}
		while($person=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$person->id, "name"=>"Details", "value"=>"Details", "class"=>"info", "url"=>"details-person"]);
				$action_buttons.= action_button(["id"=>$person->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-person"]);
				$action_buttons.= action_button(["id"=>$person->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"persons"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$person->id</td><td>$person->name</td><td>$person->position_id</td><td>$person->sex</td><td>$person->dob</td><td>$person->doj</td><td>$person->mobile</td><td>$person->address</td><td>$person->inactive</td><td><img src=\"img/$person->photo\" width=\"100\" /></td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,name,position_id,sex,dob,doj,mobile,address,inactive,photo from {$tx}persons where id={$id}");
		$person=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Person Details</th></tr>";
		$html.="<tr><th>Id</th><td>$person->id</td></tr>";
		$html.="<tr><th>Name</th><td>$person->name</td></tr>";
		$html.="<tr><th>Position Id</th><td>$person->position_id</td></tr>";
		$html.="<tr><th>Sex</th><td>$person->sex</td></tr>";
		$html.="<tr><th>Dob</th><td>$person->dob</td></tr>";
		$html.="<tr><th>Doj</th><td>$person->doj</td></tr>";
		$html.="<tr><th>Mobile</th><td>$person->mobile</td></tr>";
		$html.="<tr><th>Address</th><td>$person->address</td></tr>";
		$html.="<tr><th>Inactive</th><td>$person->inactive</td></tr>";
		$html.="<tr><th>Photo</th><td><img src=\"img/$person->photo\" width=\"100\" /></td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
