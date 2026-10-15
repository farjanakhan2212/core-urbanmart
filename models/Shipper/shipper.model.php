<?php
class Shipper extends Model implements JsonSerializable{
	public $id;
	public $name;
	public $contact_person;
	public $contact_no;

	public function __construct(){
	}
	public function set($id,$name,$contact_person,$contact_no){
		$this->id=$id;
		$this->name=$name;
		$this->contact_person=$contact_person;
		$this->contact_no=$contact_no;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}shippers(name,contact_person,contact_no)values('$this->name','$this->contact_person','$this->contact_no')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}shippers set name='$this->name',contact_person='$this->contact_person',contact_no='$this->contact_no' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}shippers where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,contact_person,contact_no from {$tx}shippers");
		$data=[];
		while($shipper=$result->fetch_object()){
			$data[]=$shipper;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,contact_person,contact_no from {$tx}shippers $criteria limit $top,$perpage");
		$data=[];
		while($shipper=$result->fetch_object()){
			$data[]=$shipper;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}shippers $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,contact_person,contact_no from {$tx}shippers where id='$id'");
		$shipper=$result->fetch_object();
			return $shipper;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,name,contact_person,contact_no from {$tx}shippers where name like '%$name%'");
		$data=[];
		while($shipper=$result->fetch_object()){
			$data[]=$shipper;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}shippers");
		$shipper =$result->fetch_object();
		return $shipper->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Contact Person:$this->contact_person<br> 
		Contact No:$this->contact_no<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbShipper"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}shippers");
		while($shipper=$result->fetch_object()){
			$html.="<option value ='$shipper->id'>$shipper->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}shippers $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,contact_person,contact_no from {$tx}shippers $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Contact Person</th><th>Contact No</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Contact Person</th><th>Contact No</th></tr>";
		}
		while($shipper=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"shipper/show/$shipper->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"shipper/edit/$shipper->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"shipper/confirm/$shipper->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$shipper->id</td><td>$shipper->name</td><td>$shipper->contact_person</td><td>$shipper->contact_no</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,name,contact_person,contact_no from {$tx}shippers where id={$id}");
		$shipper=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Shipper Show</th></tr>";
		$html.="<tr><th>Id</th><td>$shipper->id</td></tr>";
		$html.="<tr><th>Name</th><td>$shipper->name</td></tr>";
		$html.="<tr><th>Contact Person</th><td>$shipper->contact_person</td></tr>";
		$html.="<tr><th>Contact No</th><td>$shipper->contact_no</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
