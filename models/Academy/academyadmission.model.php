<?php
class AcademyAdmission extends Model implements JsonSerializable{
	public $id;
	public $academy_student_id;
	public $academy_batch_id;
	public $academy_section_id;
	public $created_at;
	public $roll;

	public function __construct(){
	}
	public function set($id,$academy_student_id,$academy_batch_id,$academy_section_id,$created_at,$roll){
		$this->id=$id;
		$this->academy_student_id=$academy_student_id;
		$this->academy_batch_id=$academy_batch_id;
		$this->academy_section_id=$academy_section_id;
		$this->created_at=$created_at;
		$this->roll=$roll;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}academy_admissions(academy_student_id,academy_batch_id,academy_section_id,created_at,roll)values('$this->academy_student_id','$this->academy_batch_id','$this->academy_section_id','$this->created_at','$this->roll')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}academy_admissions set academy_student_id='$this->academy_student_id',academy_batch_id='$this->academy_batch_id',academy_section_id='$this->academy_section_id',created_at='$this->created_at',roll='$this->roll' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}academy_admissions where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,academy_student_id,academy_batch_id,academy_section_id,created_at,roll from {$tx}academy_admissions");
		$data=[];
		while($academyadmission=$result->fetch_object()){
			$data[]=$academyadmission;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,academy_student_id,academy_batch_id,academy_section_id,created_at,roll from {$tx}academy_admissions $criteria limit $top,$perpage");
		$data=[];
		while($academyadmission=$result->fetch_object()){
			$data[]=$academyadmission;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}academy_admissions $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,academy_student_id,academy_batch_id,academy_section_id,created_at,roll from {$tx}academy_admissions where id='$id'");
		$academyadmission=$result->fetch_object();
			return $academyadmission;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,academy_student_id,academy_batch_id,academy_section_id,created_at,roll from {$tx}academy_admissions where name like '%$name%'");
		$data=[];
		while($academyadmission=$result->fetch_object()){
			$data[]=$academyadmission;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}academy_admissions");
		$academyadmission =$result->fetch_object();
		return $academyadmission->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Academy Student Id:$this->academy_student_id<br> 
		Academy Batch Id:$this->academy_batch_id<br> 
		Academy Section Id:$this->academy_section_id<br> 
		Created At:$this->created_at<br> 
		Roll:$this->roll<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbAcademyAdmission"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}academy_admissions");
		while($academyadmission=$result->fetch_object()){
			$html.="<option value ='$academyadmission->id'>$academyadmission->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}academy_admissions $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,academy_student_id,academy_batch_id,academy_section_id,created_at,roll from {$tx}academy_admissions $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Academy Student Id</th><th>Academy Batch Id</th><th>Academy Section Id</th><th>Created At</th><th>Roll</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Academy Student Id</th><th>Academy Batch Id</th><th>Academy Section Id</th><th>Created At</th><th>Roll</th></tr>";
		}
		while($academyadmission=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"academyadmission/show/$academyadmission->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"academyadmission/edit/$academyadmission->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"academyadmission/confirm/$academyadmission->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$academyadmission->id</td><td>$academyadmission->academy_student_id</td><td>$academyadmission->academy_batch_id</td><td>$academyadmission->academy_section_id</td><td>$academyadmission->created_at</td><td>$academyadmission->roll</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,academy_student_id,academy_batch_id,academy_section_id,created_at,roll from {$tx}academy_admissions where id={$id}");
		$academyadmission=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">AcademyAdmission Show</th></tr>";
		$html.="<tr><th>Id</th><td>$academyadmission->id</td></tr>";
		$html.="<tr><th>Academy Student Id</th><td>$academyadmission->academy_student_id</td></tr>";
		$html.="<tr><th>Academy Batch Id</th><td>$academyadmission->academy_batch_id</td></tr>";
		$html.="<tr><th>Academy Section Id</th><td>$academyadmission->academy_section_id</td></tr>";
		$html.="<tr><th>Created At</th><td>$academyadmission->created_at</td></tr>";
		$html.="<tr><th>Roll</th><td>$academyadmission->roll</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
