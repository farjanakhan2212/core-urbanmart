<?php
class AcademyRoutine extends Model implements JsonSerializable{
	public $id;
	public $academy_class_id;
	public $academy_subject_id;
	public $academy_teacher_id;
	public $day;
	public $time;

	public function __construct(){
	}
	public function set($id,$academy_class_id,$academy_subject_id,$academy_teacher_id,$day,$time){
		$this->id=$id;
		$this->academy_class_id=$academy_class_id;
		$this->academy_subject_id=$academy_subject_id;
		$this->academy_teacher_id=$academy_teacher_id;
		$this->day=$day;
		$this->time=$time;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}academy_routines(academy_class_id,academy_subject_id,academy_teacher_id,day,time)values('$this->academy_class_id','$this->academy_subject_id','$this->academy_teacher_id','$this->day','$this->time')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}academy_routines set academy_class_id='$this->academy_class_id',academy_subject_id='$this->academy_subject_id',academy_teacher_id='$this->academy_teacher_id',day='$this->day',time='$this->time' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}academy_routines where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,academy_class_id,academy_subject_id,academy_teacher_id,day,time from {$tx}academy_routines");
		$data=[];
		while($academyroutine=$result->fetch_object()){
			$data[]=$academyroutine;
		}
		return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,academy_class_id,academy_subject_id,academy_teacher_id,day,time from {$tx}academy_routines $criteria limit $top,$perpage");
		$data=[];
		while($academyroutine=$result->fetch_object()){
			$data[]=$academyroutine;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}academy_routines $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,academy_class_id,academy_subject_id,academy_teacher_id,day,time from {$tx}academy_routines where id='$id'");
		$academyroutine=$result->fetch_object();
			return $academyroutine;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,academy_class_id,academy_subject_id,academy_teacher_id,day,time from {$tx}academy_routines where name like '%$name%'");
		$data=[];
		while($academyroutine=$result->fetch_object()){
			$data[]=$academyroutine;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}academy_routines");
		$academyroutine =$result->fetch_object();
		return $academyroutine->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Academy Class Id:$this->academy_class_id<br> 
		Academy Subject Id:$this->academy_subject_id<br> 
		Academy Teacher Id:$this->academy_teacher_id<br> 
		Day:$this->day<br> 
		Time:$this->time<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbAcademyRoutine"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}academy_routines");
		while($academyroutine=$result->fetch_object()){
			$html.="<option value ='$academyroutine->id'>$academyroutine->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}academy_routines $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select r.id,c.name class,s.name subject,t.name teacher ,r.day,r.time from {$tx}academy_routines r, {$tx}academy_teachers t, {$tx}academy_subjects s,{$tx}academy_classes c where s.id=r.academy_subject_id and t.id=r.academy_teacher_id and c.id=r.academy_class_id $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Class</th><th>Subject</th><th>Teacher</th><th>Day</th><th>Time</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Class</th><th>Subject</th><th>Teacher</th><th>Day</th><th>Time</th></tr>";
		}
		while($academyroutine=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"academyroutine/show/$academyroutine->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"academyroutine/edit/$academyroutine->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"academyroutine/confirm/$academyroutine->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$academyroutine->id</td><td>$academyroutine->class</td><td>$academyroutine->subject</td><td>$academyroutine->teacher</td><td>$academyroutine->day</td><td>$academyroutine->time</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,academy_class_id,academy_subject_id,academy_teacher_id,day,time from {$tx}academy_routines where id={$id}");
		$academyroutine=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">AcademyRoutine Show</th></tr>";
		$html.="<tr><th>Id</th><td>$academyroutine->id</td></tr>";
		$html.="<tr><th>Academy Class Id</th><td>$academyroutine->academy_class_id</td></tr>";
		$html.="<tr><th>Academy Subject Id</th><td>$academyroutine->academy_subject_id</td></tr>";
		$html.="<tr><th>Academy Teacher Id</th><td>$academyroutine->academy_teacher_id</td></tr>";
		$html.="<tr><th>Day</th><td>$academyroutine->day</td></tr>";
		$html.="<tr><th>Time</th><td>$academyroutine->time</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
