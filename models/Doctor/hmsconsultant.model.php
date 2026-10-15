<?php
class HmsConsultant extends Model implements JsonSerializable{
	public $id;
	public $name;
	public $hms_department_id;
	public $designation;

	public function __construct(){
	}
	public function set($id,$name,$hms_department_id,$designation){
		$this->id=$id;
		$this->name=$name;
		$this->hms_department_id=$hms_department_id;
		$this->designation=$designation;

	}
	public function save(){
		global $db,$tx,$now;
		$db->query("insert into {$tx}hms_consultants(name,hms_department_id,designation)values('$this->name','$this->hms_department_id','$this->designation')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx,$now;
		$db->query("update {$tx}hms_consultants set name='$this->name',hms_department_id='$this->hms_department_id',designation='$this->designation' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}hms_consultants where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,hms_department_id,designation from {$tx}hms_consultants");
		$data=[];
		while($hmsconsultant=$result->fetch_object()){
			$data[]=$hmsconsultant;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,hms_department_id,designation from {$tx}hms_consultants $criteria limit $top,$perpage");
		$data=[];
		while($hmsconsultant=$result->fetch_object()){
			$data[]=$hmsconsultant;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}hms_consultants $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,hms_department_id,designation from {$tx}hms_consultants where id='$id'");
		$hmsconsultant=$result->fetch_object();
			return $hmsconsultant;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,name,hms_department_id,designation from {$tx}hms_consultants where name like '%$name%'");
		$data=[];
		while($hmsconsultant=$result->fetch_object()){
			$data[]=$hmsconsultant;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}hms_consultants");
		$hmsconsultant =$result->fetch_object();
		return $hmsconsultant->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Hms Department Id:$this->hms_department_id<br> 
		Designation:$this->designation<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbHmsConsultant"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}hms_consultants");
		while($hmsconsultant=$result->fetch_object()){
			$html.="<option value ='$hmsconsultant->id'>$hmsconsultant->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}hms_consultants $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select c.id,c.name,d.name department,designation from {$tx}hms_consultants c, {$tx}hms_departments d where d.id=c.hms_department_id $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Hms Department</th><th>Designation</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Hms Department</th><th>Designation</th></tr>";
		}
		while($hmsconsultant=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"hmsconsultant/show/$hmsconsultant->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"hmsconsultant/edit/$hmsconsultant->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"hmsconsultant/confirm/$hmsconsultant->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$hmsconsultant->id</td><td>$hmsconsultant->name</td><td>$hmsconsultant->department</td><td>$hmsconsultant->designation</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,name,hms_department_id,designation from {$tx}hms_consultants where id={$id}");
		$hmsconsultant=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">HmsConsultant Show</th></tr>";
		$html.="<tr><th>Id</th><td>$hmsconsultant->id</td></tr>";
		$html.="<tr><th>Name</th><td>$hmsconsultant->name</td></tr>";
		$html.="<tr><th>Hms Department Id</th><td>$hmsconsultant->hms_department_id</td></tr>";
		$html.="<tr><th>Designation</th><td>$hmsconsultant->designation</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
