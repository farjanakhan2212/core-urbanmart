<?php
class Warehouse extends Model implements JsonSerializable{
	public $id;
	public $name;
	public $city;
	public $contact;

	public function __construct(){
	}
	public function set($id,$name,$city,$contact){
		$this->id=$id;
		$this->name=$name;
		$this->city=$city;
		$this->contact=$contact;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}warehouses(name,city,contact)values('$this->name','$this->city','$this->contact')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}warehouses set name='$this->name',city='$this->city',contact='$this->contact' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}warehouses where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,city,contact from {$tx}warehouses");
		$data=[];
		while($warehouse=$result->fetch_object()){
			$data[]=$warehouse;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,city,contact from {$tx}warehouses $criteria limit $top,$perpage");
		$data=[];
		while($warehouse=$result->fetch_object()){
			$data[]=$warehouse;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}warehouses $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,city,contact from {$tx}warehouses where id='$id'");
		$warehouse=$result->fetch_object();
			return $warehouse;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}warehouses");
		$warehouse =$result->fetch_object();
		return $warehouse->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		City:$this->city<br> 
		Contact:$this->contact<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbWarehouse"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}warehouses");
		while($warehouse=$result->fetch_object()){
			$html.="<option value ='$warehouse->id'>$warehouse->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}warehouses $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,city,contact from {$tx}warehouses $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-warehouse\">New Warehouse</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>City</th><th>Contact</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>City</th><th>Contact</th></tr>";
		}
		while($warehouse=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$warehouse->id, "name"=>"Details", "value"=>"Details", "class"=>"info", "url"=>"details-warehouse"]);
				$action_buttons.= action_button(["id"=>$warehouse->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-warehouse"]);
				$action_buttons.= action_button(["id"=>$warehouse->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"warehouses"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$warehouse->id</td><td>$warehouse->name</td><td>$warehouse->city</td><td>$warehouse->contact</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,name,city,contact from {$tx}warehouses where id={$id}");
		$warehouse=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Warehouse Details</th></tr>";
		$html.="<tr><th>Id</th><td>$warehouse->id</td></tr>";
		$html.="<tr><th>Name</th><td>$warehouse->name</td></tr>";
		$html.="<tr><th>City</th><td>$warehouse->city</td></tr>";
		$html.="<tr><th>Contact</th><td>$warehouse->contact</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
