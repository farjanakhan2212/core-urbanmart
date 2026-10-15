<?php
class HmsPrescription extends Model implements JsonSerializable{
	public $id;
	public $patient_id;
	public $consultant_id;
	public $cc;
	public $rf;
	public $investigation;
	public $advice;

	public function __construct(){
	}
	public function set($id,$patient_id,$consultant_id,$cc,$rf,$investigation,$advice){
		$this->id=$id;
		$this->patient_id=$patient_id;
		$this->consultant_id=$consultant_id;
		$this->cc=$cc;
		$this->rf=$rf;
		$this->investigation=$investigation;
		$this->advice=$advice;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}hms_prescriptions(patient_id,consultant_id,cc,rf,investigation,advice)values('$this->patient_id','$this->consultant_id','$this->cc','$this->rf','$this->investigation','$this->advice')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}hms_prescriptions set patient_id='$this->patient_id',consultant_id='$this->consultant_id',cc='$this->cc',rf='$this->rf',investigation='$this->investigation',advice='$this->advice' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}hms_prescriptions where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,patient_id,consultant_id,cc,rf,investigation,advice from {$tx}hms_prescriptions");
		$data=[];
		while($hmsprescription=$result->fetch_object()){
			$data[]=$hmsprescription;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,patient_id,consultant_id,cc,rf,investigation,advice from {$tx}hms_prescriptions $criteria limit $top,$perpage");
		$data=[];
		while($hmsprescription=$result->fetch_object()){
			$data[]=$hmsprescription;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}hms_prescriptions $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,patient_id,consultant_id,cc,rf,investigation,advice from {$tx}hms_prescriptions where id='$id'");
		$hmsprescription=$result->fetch_object();
			return $hmsprescription;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,patient_id,consultant_id,cc,rf,investigation,advice from {$tx}hms_prescriptions where name like '%$name%'");
		$data=[];
		while($hmsprescription=$result->fetch_object()){
			$data[]=$hmsprescription;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}hms_prescriptions");
		$hmsprescription =$result->fetch_object();
		return $hmsprescription->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Patient Id:$this->patient_id<br> 
		Consultant Id:$this->consultant_id<br> 
		Cc:$this->cc<br> 
		Rf:$this->rf<br> 
		Investigation:$this->investigation<br> 
		Advice:$this->advice<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbHmsPrescription"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}hms_prescriptions");
		while($hmsprescription=$result->fetch_object()){
			$html.="<option value ='$hmsprescription->id'>$hmsprescription->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}hms_prescriptions $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,patient_id,consultant_id,cc,rf,investigation,advice from {$tx}hms_prescriptions $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Patient Id</th><th>Consultant Id</th><th>Cc</th><th>Rf</th><th>Investigation</th><th>Advice</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Patient Id</th><th>Consultant Id</th><th>Cc</th><th>Rf</th><th>Investigation</th><th>Advice</th></tr>";
		}
		while($hmsprescription=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"hmsprescription/show/$hmsprescription->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"hmsprescription/edit/$hmsprescription->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"hmsprescription/confirm/$hmsprescription->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$hmsprescription->id</td><td>$hmsprescription->patient_id</td><td>$hmsprescription->consultant_id</td><td>$hmsprescription->cc</td><td>$hmsprescription->rf</td><td>$hmsprescription->investigation</td><td>$hmsprescription->advice</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,patient_id,consultant_id,cc,rf,investigation,advice from {$tx}hms_prescriptions where id={$id}");
		$hmsprescription=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">HmsPrescription Show</th></tr>";
		$html.="<tr><th>Id</th><td>$hmsprescription->id</td></tr>";
		$html.="<tr><th>Patient Id</th><td>$hmsprescription->patient_id</td></tr>";
		$html.="<tr><th>Consultant Id</th><td>$hmsprescription->consultant_id</td></tr>";
		$html.="<tr><th>Cc</th><td>$hmsprescription->cc</td></tr>";
		$html.="<tr><th>Rf</th><td>$hmsprescription->rf</td></tr>";
		$html.="<tr><th>Investigation</th><td>$hmsprescription->investigation</td></tr>";
		$html.="<tr><th>Advice</th><td>$hmsprescription->advice</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
