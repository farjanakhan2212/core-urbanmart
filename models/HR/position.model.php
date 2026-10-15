<?php
class Position implements JsonSerializable{
	public $id;
	public $name;
	public $grade;
	public $department_id;

	public function __construct(){
	}
	public function set($id,$name,$grade,$department_id){
		$this->id=$id;
		$this->name=$name;
		$this->grade=$grade;
		$this->department_id=$department_id;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}positions(name,grade,department_id)values('$this->name','$this->grade','$this->department_id')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}positions set name='$this->name',grade='$this->grade',department_id='$this->department_id' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}positions where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,grade,department_id from {$tx}positions");
		$data=[];
		while($position=$result->fetch_object()){
			$data[]=$position;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,grade,department_id from {$tx}positions $criteria limit $top,$perpage");
		$data=[];
		while($position=$result->fetch_object()){
			$data[]=$position;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}positions $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,grade,department_id from {$tx}positions where id='$id'");
		$position=$result->fetch_object();
			return $position;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}positions");
		$position =$result->fetch_object();
		return $position->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Grade:$this->grade<br> 
		Department Id:$this->department_id<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbPosition"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}positions");
		while($position=$result->fetch_object()){
			$html.="<option value ='$position->id'>$position->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}positions $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select p.id,p.name,p.grade,d.long_name department from {$tx}positions p,{$tx}departments d where d.id=p.department_id $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-position\">New Position</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Grade</th><th>Department</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Grade</th><th>Department</th></tr>";
		}
		while($position=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$position->id, "name"=>"Details", "value"=>"Details", "class"=>"info", "url"=>"details-position"]);
				$action_buttons.= action_button(["id"=>$position->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-position"]);
				$action_buttons.= action_button(["id"=>$position->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"positions"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$position->id</td><td>$position->name</td><td>$position->grade</td><td>$position->department</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,name,grade,department_id from {$tx}positions where id={$id}");
		$position=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Position Details</th></tr>";
		$html.="<tr><th>Id</th><td>$position->id</td></tr>";
		$html.="<tr><th>Name</th><td>$position->name</td></tr>";
		$html.="<tr><th>Grade</th><td>$position->grade</td></tr>";
		$html.="<tr><th>Department Id</th><td>$position->department_id</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
