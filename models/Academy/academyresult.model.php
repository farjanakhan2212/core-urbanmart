<?php
class AcademyResult extends Model implements JsonSerializable{
	public $id;
	public $academy_student_id;
	public $academy_subject_id;
	public $academy_term_id;
	public $academy_exam_type_id;
	public $mark;
	public $fullmark;

	public function __construct(){
	}
	public function set($id,$academy_student_id,$academy_subject_id,$academy_term_id,$academy_exam_type_id,$mark,$fullmark){
		$this->id=$id;
		$this->academy_student_id=$academy_student_id;
		$this->academy_subject_id=$academy_subject_id;
		$this->academy_term_id=$academy_term_id;
		$this->academy_exam_type_id=$academy_exam_type_id;
		$this->mark=$mark;
		$this->fullmark=$fullmark;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}academy_results(academy_student_id,academy_subject_id,academy_term_id,academy_exam_type_id,mark,fullmark)values('$this->academy_student_id','$this->academy_subject_id','$this->academy_term_id','$this->academy_exam_type_id','$this->mark','$this->fullmark')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}academy_results set academy_student_id='$this->academy_student_id',academy_subject_id='$this->academy_subject_id',academy_term_id='$this->academy_term_id',academy_exam_type_id='$this->academy_exam_type_id',mark='$this->mark',fullmark='$this->fullmark' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}academy_results where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,academy_student_id,academy_subject_id,academy_term_id,academy_exam_type_id,mark,fullmark from {$tx}academy_results");
		$data=[];
		while($academyresult=$result->fetch_object()){
			$data[]=$academyresult;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,academy_student_id,academy_subject_id,academy_term_id,academy_exam_type_id,mark,fullmark from {$tx}academy_results $criteria limit $top,$perpage");
		$data=[];
		while($academyresult=$result->fetch_object()){
			$data[]=$academyresult;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}academy_results $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,academy_student_id,academy_subject_id,academy_term_id,academy_exam_type_id,mark,fullmark from {$tx}academy_results where id='$id'");
		$academyresult=$result->fetch_object();
			return $academyresult;
	}
	public static function filter($student_id,$term_id){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select r.id,s.name subject,t.name type,mark,fullmark from {$tx}academy_results r,{$tx}academy_subjects s,{$tx}academy_exam_types t where r.academy_subject_id=s.id and r.academy_exam_type_id=t.id and  academy_student_id='$student_id' and academy_term_id='$term_id' ");
		$data=[];
		while($academyresult=$result->fetch_object()){
			$data[]=$academyresult;
		}
		return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}academy_results");
		$academyresult =$result->fetch_object();
		return $academyresult->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Academy Student Id:$this->academy_student_id<br> 
		Academy Subject Id:$this->academy_subject_id<br> 
		Academy Term Id:$this->academy_term_id<br> 
		Academy Exam Type Id:$this->academy_exam_type_id<br> 
		Mark:$this->mark<br> 
		Fullmark:$this->fullmark<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbAcademyResult"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}academy_results");
		while($academyresult=$result->fetch_object()){
			$html.="<option value ='$academyresult->id'>$academyresult->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}academy_results $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,academy_student_id,academy_subject_id,academy_term_id,academy_exam_type_id,mark,fullmark from {$tx}academy_results $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Academy Student Id</th><th>Academy Subject Id</th><th>Academy Term Id</th><th>Academy Exam Type Id</th><th>Mark</th><th>Fullmark</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Academy Student Id</th><th>Academy Subject Id</th><th>Academy Term Id</th><th>Academy Exam Type Id</th><th>Mark</th><th>Fullmark</th></tr>";
		}
		while($academyresult=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"academyresult/show/$academyresult->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"academyresult/edit/$academyresult->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"academyresult/confirm/$academyresult->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$academyresult->id</td><td>$academyresult->academy_student_id</td><td>$academyresult->academy_subject_id</td><td>$academyresult->academy_term_id</td><td>$academyresult->academy_exam_type_id</td><td>$academyresult->mark</td><td>$academyresult->fullmark</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,academy_student_id,academy_subject_id,academy_term_id,academy_exam_type_id,mark,fullmark from {$tx}academy_results where id={$id}");
		$academyresult=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">AcademyResult Show</th></tr>";
		$html.="<tr><th>Id</th><td>$academyresult->id</td></tr>";
		$html.="<tr><th>Academy Student Id</th><td>$academyresult->academy_student_id</td></tr>";
		$html.="<tr><th>Academy Subject Id</th><td>$academyresult->academy_subject_id</td></tr>";
		$html.="<tr><th>Academy Term Id</th><td>$academyresult->academy_term_id</td></tr>";
		$html.="<tr><th>Academy Exam Type Id</th><td>$academyresult->academy_exam_type_id</td></tr>";
		$html.="<tr><th>Mark</th><td>$academyresult->mark</td></tr>";
		$html.="<tr><th>Fullmark</th><td>$academyresult->fullmark</td></tr>";

		$html.="</table>";
		return $html;
	}

	static function html_student_reports($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}academy_students $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,fathers_name,mothers_name,academy_batch_id,created_at,dob,photo,contact_no,address from {$tx}academy_students $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Fathers Name</th><th>Mothers Name</th><th>Academy Batch Id</th><th>Created At</th><th>Dob</th><th>Photo</th><th>Contact No</th><th>Address</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Fathers Name</th><th>Mothers Name</th><th>Academy Batch Id</th><th>Created At</th><th>Dob</th><th>Photo</th><th>Contact No</th><th>Address</th></tr>";
		}
		while($academystudent=$result->fetch_object()){
			$action_buttons = "";
			
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				
				$action_buttons.= Event::button(["name"=>"report", "value"=>"Report", "class"=>"btn btn-info", "route"=>"academyresult/report/$academystudent->id"]);
				
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$academystudent->id</td><td>$academystudent->name</td><td>$academystudent->fathers_name</td><td>$academystudent->mothers_name</td><td>$academystudent->academy_batch_id</td><td>$academystudent->created_at</td><td>$academystudent->dob</td><td><img src='$base_url/img/$academystudent->photo' width='100' /></td><td>$academystudent->contact_no</td><td>$academystudent->address</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}

	public static function report($id,$term_id=1){
         
	    $results=AcademyResult::filter($id,$term_id);

		$html="<table class='table'>";
		$html.="<tr><th>Subject</th><th colspan='2'>MCQ</th></tr>";
		$total=0;
		$full_total=0;
		foreach($results as $result){
            $html.="<tr><td>$result->subject</td><td>$result->mark</td><td>$result->fullmark</td></tr>";
		   
			$total+=$result->mark;
			$full_total+=$result->fullmark;
		}
		$html.="<tr><th>Total</th><th>$total</th><th>$full_total</th></tr>";
		$html.="</table>";		
        return $html;
	}

}
?>
