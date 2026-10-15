<?php
class HmsMedicine extends Model implements JsonSerializable{
	public $id;
	public $name;
	public $hms_medicine_category_id;
	public $hms_medicine_type_id;
	public $generic_name;
	public $description;

	public function __construct(){
	}
	public function set($id,$name,$hms_medicine_category_id,$hms_medicine_type_id,$generic_name,$description){
		$this->id=$id;
		$this->name=$name;
		$this->hms_medicine_category_id=$hms_medicine_category_id;
		$this->hms_medicine_type_id=$hms_medicine_type_id;
		$this->generic_name=$generic_name;
		$this->description=$description;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}hms_medicines(name,hms_medicine_category_id,hms_medicine_type_id,generic_name,description)values('$this->name','$this->hms_medicine_category_id','$this->hms_medicine_type_id','$this->generic_name','$this->description')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}hms_medicines set name='$this->name',hms_medicine_category_id='$this->hms_medicine_category_id',hms_medicine_type_id='$this->hms_medicine_type_id',generic_name='$this->generic_name',description='$this->description' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}hms_medicines where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,hms_medicine_category_id,hms_medicine_type_id,generic_name,description from {$tx}hms_medicines");
		$data=[];
		while($hmsmedicine=$result->fetch_object()){
			$data[]=$hmsmedicine;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,hms_medicine_category_id,hms_medicine_type_id,generic_name,description from {$tx}hms_medicines $criteria limit $top,$perpage");
		$data=[];
		while($hmsmedicine=$result->fetch_object()){
			$data[]=$hmsmedicine;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}hms_medicines $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,hms_medicine_category_id,hms_medicine_type_id,generic_name,description from {$tx}hms_medicines where id='$id'");
		$hmsmedicine=$result->fetch_object();
			return $hmsmedicine;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,name,hms_medicine_category_id,hms_medicine_type_id,generic_name,description from {$tx}hms_medicines where name like '%$name%'");
		$data=[];
		while($hmsmedicine=$result->fetch_object()){
			$data[]=$hmsmedicine;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}hms_medicines");
		$hmsmedicine =$result->fetch_object();
		return $hmsmedicine->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Hms Medicine Category Id:$this->hms_medicine_category_id<br> 
		Hms Medicine Type Id:$this->hms_medicine_type_id<br> 
		Generic Name:$this->generic_name<br> 
		Description:$this->description<br> 
";
	}

	//-------------HTML----------//

	static function html_select($arg=[]){
		global $db,$tx;
		$html="<select id='{$arg["name"]}' name='{$arg["name"]}' class='{$arg["class"]}'> ";
		$result =$db->query("select id,concat(id,'-',name) name from {$tx}hms_medicines");
		while($hmsmedicine=$result->fetch_object()){
			$html.="<option value ='$hmsmedicine->id'>$hmsmedicine->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}hms_medicines $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,hms_medicine_category_id,hms_medicine_type_id,generic_name,description from {$tx}hms_medicines $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Hms Medicine Category Id</th><th>Hms Medicine Type Id</th><th>Generic Name</th><th>Description</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Hms Medicine Category Id</th><th>Hms Medicine Type Id</th><th>Generic Name</th><th>Description</th></tr>";
		}
		while($hmsmedicine=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"hmsmedicine/show/$hmsmedicine->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"hmsmedicine/edit/$hmsmedicine->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"hmsmedicine/confirm/$hmsmedicine->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$hmsmedicine->id</td><td>$hmsmedicine->name</td><td>$hmsmedicine->hms_medicine_category_id</td><td>$hmsmedicine->hms_medicine_type_id</td><td>$hmsmedicine->generic_name</td><td>$hmsmedicine->description</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,name,hms_medicine_category_id,hms_medicine_type_id,generic_name,description from {$tx}hms_medicines where id={$id}");
		$hmsmedicine=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">HmsMedicine Show</th></tr>";
		$html.="<tr><th>Id</th><td>$hmsmedicine->id</td></tr>";
		$html.="<tr><th>Name</th><td>$hmsmedicine->name</td></tr>";
		$html.="<tr><th>Hms Medicine Category Id</th><td>$hmsmedicine->hms_medicine_category_id</td></tr>";
		$html.="<tr><th>Hms Medicine Type Id</th><td>$hmsmedicine->hms_medicine_type_id</td></tr>";
		$html.="<tr><th>Generic Name</th><td>$hmsmedicine->generic_name</td></tr>";
		$html.="<tr><th>Description</th><td>$hmsmedicine->description</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
