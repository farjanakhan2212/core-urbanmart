<?php
class HmsPatient extends Model implements JsonSerializable{
	public $id;
	public $name;
	public $mobile;
	public $dob;
	public $mob_ext;
	public $gender;
	public $profession;

	public function __construct(){
	}
	public function set($id,$name,$mobile,$dob,$mob_ext,$gender,$profession){
		$this->id=$id;
		$this->name=$name;
		$this->mobile=$mobile;
		$this->dob=$dob;
		$this->mob_ext=$mob_ext;
		$this->gender=$gender;
		$this->profession=$profession;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}hms_patients(name,mobile,dob,mob_ext,gender,profession)values('$this->name','$this->mobile','$this->dob','$this->mob_ext','$this->gender','$this->profession')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}hms_patients set name='$this->name',mobile='$this->mobile',dob='$this->dob',mob_ext='$this->mob_ext',gender='$this->gender',profession='$this->profession' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}hms_patients where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,mobile,dob,mob_ext,gender,profession from {$tx}hms_patients");
		$data=[];
		while($hmspatient=$result->fetch_object()){
			$data[]=$hmspatient;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,mobile,dob,mob_ext,gender,profession from {$tx}hms_patients $criteria limit $top,$perpage");
		$data=[];
		while($hmspatient=$result->fetch_object()){
			$data[]=$hmspatient;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}hms_patients $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,mobile,dob,mob_ext,gender,profession from {$tx}hms_patients where id='$id'");
		$hmspatient=$result->fetch_object();
			return $hmspatient;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,name,mobile,dob,mob_ext,gender,profession from {$tx}hms_patients where name like '%$name%'");
		$data=[];
		while($hmspatient=$result->fetch_object()){
			$data[]=$hmspatient;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}hms_patients");
		$hmspatient =$result->fetch_object();
		return $hmspatient->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Mobile:$this->mobile<br> 
		Dob:$this->dob<br> 
		Mob Ext:$this->mob_ext<br> 
		Gender:$this->gender<br> 
		Profession:$this->profession<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbHmsPatient"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}hms_patients");
		while($hmspatient=$result->fetch_object()){
			$html.="<option value ='$hmspatient->id'>$hmspatient->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}hms_patients $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,mobile,dob,mob_ext,gender,profession from {$tx}hms_patients $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Mobile</th><th>Dob</th><th>Mob Ext</th><th>Gender</th><th>Profession</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Mobile</th><th>Dob</th><th>Mob Ext</th><th>Gender</th><th>Profession</th></tr>";
		}
		while($hmspatient=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"hmspatient/show/$hmspatient->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"hmspatient/edit/$hmspatient->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"hmspatient/confirm/$hmspatient->id"]);
				$action_buttons.= Event::button(["name"=>"rx", "value"=>"Rx", "class"=>"btn btn-success", "route"=>"prescription/create/$hmspatient->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$hmspatient->id</td><td>$hmspatient->name</td><td>$hmspatient->mobile</td><td>$hmspatient->dob</td><td>$hmspatient->mob_ext</td><td>$hmspatient->gender</td><td>$hmspatient->profession</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,name,mobile,dob,mob_ext,gender,profession from {$tx}hms_patients where id={$id}");
		$hmspatient=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">HmsPatient Show</th></tr>";
		$html.="<tr><th>Id</th><td>$hmspatient->id</td></tr>";
		$html.="<tr><th>Name</th><td>$hmspatient->name</td></tr>";
		$html.="<tr><th>Mobile</th><td>$hmspatient->mobile</td></tr>";
		$html.="<tr><th>Dob</th><td>$hmspatient->dob</td></tr>";
		$html.="<tr><th>Mob Ext</th><td>$hmspatient->mob_ext</td></tr>";
		$html.="<tr><th>Gender</th><td>$hmspatient->gender</td></tr>";
		$html.="<tr><th>Profession</th><td>$hmspatient->profession</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
