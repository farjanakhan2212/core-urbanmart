<?php
class AcademyTeacher extends Model implements JsonSerializable{
	public $id;
	public $name;
	public $contact_no;
	public $position;

	public function __construct(){
	}
	public function set($id,$name,$contact_no,$position){
		$this->id=$id;
		$this->name=$name;
		$this->contact_no=$contact_no;
		$this->position=$position;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}academy_teachers(name,contact_no,position)values('$this->name','$this->contact_no','$this->position')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}academy_teachers set name='$this->name',contact_no='$this->contact_no',position='$this->position' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}academy_teachers where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,contact_no,position from {$tx}academy_teachers");
		$data=[];
		while($academyteacher=$result->fetch_object()){
			$data[]=$academyteacher;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,contact_no,position from {$tx}academy_teachers $criteria limit $top,$perpage");
		$data=[];
		while($academyteacher=$result->fetch_object()){
			$data[]=$academyteacher;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}academy_teachers $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,contact_no,position from {$tx}academy_teachers where id='$id'");
		$academyteacher=$result->fetch_object();
			return $academyteacher;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,name,contact_no,position from {$tx}academy_teachers where name like '%$name%'");
		$data=[];
		while($academyteacher=$result->fetch_object()){
			$data[]=$academyteacher;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}academy_teachers");
		$academyteacher =$result->fetch_object();
		return $academyteacher->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Contact No:$this->contact_no<br> 
		Position:$this->position<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbAcademyTeacher"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}academy_teachers");
		while($academyteacher=$result->fetch_object()){
			$html.="<option value ='$academyteacher->id'>$academyteacher->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}academy_teachers $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,contact_no,position from {$tx}academy_teachers $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Contact No</th><th>Position</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Contact No</th><th>Position</th></tr>";
		}
		while($academyteacher=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"academyteacher/show/$academyteacher->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"academyteacher/edit/$academyteacher->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"academyteacher/confirm/$academyteacher->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$academyteacher->id</td><td>$academyteacher->name</td><td>$academyteacher->contact_no</td><td>$academyteacher->position</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,name,contact_no,position from {$tx}academy_teachers where id={$id}");
		$academyteacher=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">AcademyTeacher Show</th></tr>";
		$html.="<tr><th>Id</th><td>$academyteacher->id</td></tr>";
		$html.="<tr><th>Name</th><td>$academyteacher->name</td></tr>";
		$html.="<tr><th>Contact No</th><td>$academyteacher->contact_no</td></tr>";
		$html.="<tr><th>Position</th><td>$academyteacher->position</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
