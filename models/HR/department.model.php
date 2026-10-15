<?php
class Department implements JsonSerializable{
	public $id;
	public $code;
	public $short_name;
	public $long_name;

	public function __construct(){
	}
	public function set($id,$code,$short_name,$long_name){
		$this->id=$id;
		$this->code=$code;
		$this->short_name=$short_name;
		$this->long_name=$long_name;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}departments(code,short_name,long_name)values('$this->code','$this->short_name','$this->long_name')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}departments set code='$this->code',short_name='$this->short_name',long_name='$this->long_name' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}departments where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,code,short_name,long_name from {$tx}departments");
		$data=[];
		while($department=$result->fetch_object()){
			$data[]=$department;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,code,short_name,long_name from {$tx}departments $criteria limit $top,$perpage");
		$data=[];
		while($department=$result->fetch_object()){
			$data[]=$department;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}departments $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,code,short_name,long_name from {$tx}departments where id='$id'");
		$department=$result->fetch_object();
			return $department;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}departments");
		$department =$result->fetch_object();
		return $department->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Code:$this->code<br> 
		Short Name:$this->short_name<br> 
		Long Name:$this->long_name<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbDepartment"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}departments");
		while($department=$result->fetch_object()){
			$html.="<option value ='$department->id'>$department->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}departments $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,code,short_name,long_name from {$tx}departments $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-department\">New Department</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Code</th><th>Short Name</th><th>Long Name</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Code</th><th>Short Name</th><th>Long Name</th></tr>";
		}
		while($department=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$department->id, "name"=>"Details", "value"=>"Details", "class"=>"info", "url"=>"details-department"]);
				$action_buttons.= action_button(["id"=>$department->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-department"]);
				$action_buttons.= action_button(["id"=>$department->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"departments"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$department->id</td><td>$department->code</td><td>$department->short_name</td><td>$department->long_name</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,code,short_name,long_name from {$tx}departments where id={$id}");
		$department=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Department Details</th></tr>";
		$html.="<tr><th>Id</th><td>$department->id</td></tr>";
		$html.="<tr><th>Code</th><td>$department->code</td></tr>";
		$html.="<tr><th>Short Name</th><td>$department->short_name</td></tr>";
		$html.="<tr><th>Long Name</th><td>$department->long_name</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
