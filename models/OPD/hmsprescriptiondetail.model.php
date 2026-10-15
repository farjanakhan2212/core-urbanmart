<?php
class HmsPrescriptionDetail extends Model implements JsonSerializable{
	public $id;
	public $prescription_id;
	public $medicine_id;
	public $dose;
	public $days;
	public $suggestion;
	public $medicine_name;

	public function __construct(){
	}
	public function set($id,$prescription_id,$medicine_id,$dose,$days,$suggestion,$medicine_name){
		$this->id=$id;
		$this->prescription_id=$prescription_id;
		$this->medicine_id=$medicine_id;
		$this->dose=$dose;
		$this->days=$days;
		$this->suggestion=$suggestion;
		$this->medicine_name=$medicine_name;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}hms_prescription_details(prescription_id,medicine_id,dose,days,suggestion,medicine_name)values('$this->prescription_id','$this->medicine_id','$this->dose','$this->days','$this->suggestion','$this->medicine_name')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}hms_prescription_details set prescription_id='$this->prescription_id',medicine_id='$this->medicine_id',dose='$this->dose',days='$this->days',suggestion='$this->suggestion',medicine_name='$this->medicine_name' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}hms_prescription_details where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,prescription_id,medicine_id,dose,days,suggestion,medicine_name from {$tx}hms_prescription_details");
		$data=[];
		while($hmsprescriptiondetail=$result->fetch_object()){
			$data[]=$hmsprescriptiondetail;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,prescription_id,medicine_id,dose,days,suggestion,medicine_name from {$tx}hms_prescription_details $criteria limit $top,$perpage");
		$data=[];
		while($hmsprescriptiondetail=$result->fetch_object()){
			$data[]=$hmsprescriptiondetail;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}hms_prescription_details $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,prescription_id,medicine_id,dose,days,suggestion,medicine_name from {$tx}hms_prescription_details where id='$id'");
		$hmsprescriptiondetail=$result->fetch_object();
			return $hmsprescriptiondetail;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,prescription_id,medicine_id,dose,days,suggestion,medicine_name from {$tx}hms_prescription_details where name like '%$name%'");
		$data=[];
		while($hmsprescriptiondetail=$result->fetch_object()){
			$data[]=$hmsprescriptiondetail;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}hms_prescription_details");
		$hmsprescriptiondetail =$result->fetch_object();
		return $hmsprescriptiondetail->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Prescription Id:$this->prescription_id<br> 
		Medicine Id:$this->medicine_id<br> 
		Dose:$this->dose<br> 
		Days:$this->days<br> 
		Suggestion:$this->suggestion<br> 
		Medicine Name:$this->medicine_name<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbHmsPrescriptionDetail"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}hms_prescription_details");
		while($hmsprescriptiondetail=$result->fetch_object()){
			$html.="<option value ='$hmsprescriptiondetail->id'>$hmsprescriptiondetail->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}hms_prescription_details $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,prescription_id,medicine_id,dose,days,suggestion,medicine_name from {$tx}hms_prescription_details $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Prescription Id</th><th>Medicine Id</th><th>Dose</th><th>Days</th><th>Suggestion</th><th>Medicine Name</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Prescription Id</th><th>Medicine Id</th><th>Dose</th><th>Days</th><th>Suggestion</th><th>Medicine Name</th></tr>";
		}
		while($hmsprescriptiondetail=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"hmsprescriptiondetail/show/$hmsprescriptiondetail->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"hmsprescriptiondetail/edit/$hmsprescriptiondetail->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"hmsprescriptiondetail/confirm/$hmsprescriptiondetail->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$hmsprescriptiondetail->id</td><td>$hmsprescriptiondetail->prescription_id</td><td>$hmsprescriptiondetail->medicine_id</td><td>$hmsprescriptiondetail->dose</td><td>$hmsprescriptiondetail->days</td><td>$hmsprescriptiondetail->suggestion</td><td>$hmsprescriptiondetail->medicine_name</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,prescription_id,medicine_id,dose,days,suggestion,medicine_name from {$tx}hms_prescription_details where id={$id}");
		$hmsprescriptiondetail=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">HmsPrescriptionDetail Show</th></tr>";
		$html.="<tr><th>Id</th><td>$hmsprescriptiondetail->id</td></tr>";
		$html.="<tr><th>Prescription Id</th><td>$hmsprescriptiondetail->prescription_id</td></tr>";
		$html.="<tr><th>Medicine Id</th><td>$hmsprescriptiondetail->medicine_id</td></tr>";
		$html.="<tr><th>Dose</th><td>$hmsprescriptiondetail->dose</td></tr>";
		$html.="<tr><th>Days</th><td>$hmsprescriptiondetail->days</td></tr>";
		$html.="<tr><th>Suggestion</th><td>$hmsprescriptiondetail->suggestion</td></tr>";
		$html.="<tr><th>Medicine Name</th><td>$hmsprescriptiondetail->medicine_name</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
