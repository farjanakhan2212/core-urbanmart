<?php
class AcademyStudent extends Model implements JsonSerializable{
	public $id;
	public $name;
	public $fathers_name;
	public $mothers_name;
	public $academy_batch_id;
	public $created_at;
	public $dob;
	public $photo;
	public $contact_no;
	public $address;

	public function __construct(){
	}
	public function set($id,$name,$fathers_name,$mothers_name,$academy_batch_id,$created_at,$dob,$photo,$contact_no,$address){
		$this->id=$id;
		$this->name=$name;
		$this->fathers_name=$fathers_name;
		$this->mothers_name=$mothers_name;
		$this->academy_batch_id=$academy_batch_id;
		$this->created_at=$created_at;
		$this->dob=$dob;
		$this->photo=$photo;
		$this->contact_no=$contact_no;
		$this->address=$address;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}academy_students(name,fathers_name,mothers_name,academy_batch_id,created_at,dob,photo,contact_no,address)values('$this->name','$this->fathers_name','$this->mothers_name','$this->academy_batch_id','$this->created_at','$this->dob','$this->photo','$this->contact_no','$this->address')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}academy_students set name='$this->name',fathers_name='$this->fathers_name',mothers_name='$this->mothers_name',academy_batch_id='$this->academy_batch_id',created_at='$this->created_at',dob='$this->dob',photo='$this->photo',contact_no='$this->contact_no',address='$this->address' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}academy_students where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,fathers_name,mothers_name,academy_batch_id,created_at,dob,photo,contact_no,address from {$tx}academy_students");
		$data=[];
		while($academystudent=$result->fetch_object()){
			$data[]=$academystudent;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,fathers_name,mothers_name,academy_batch_id,created_at,dob,photo,contact_no,address from {$tx}academy_students $criteria limit $top,$perpage");
		$data=[];
		while($academystudent=$result->fetch_object()){
			$data[]=$academystudent;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}academy_students $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,fathers_name,mothers_name,academy_batch_id,created_at,dob,photo,contact_no,address from {$tx}academy_students where id='$id'");
		$academystudent=$result->fetch_object();
			return $academystudent;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,name,fathers_name,mothers_name,academy_batch_id,created_at,dob,photo,contact_no,address from {$tx}academy_students where name like '%$name%'");
		$data=[];
		while($academystudent=$result->fetch_object()){
			$data[]=$academystudent;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}academy_students");
		$academystudent =$result->fetch_object();
		return $academystudent->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Fathers Name:$this->fathers_name<br> 
		Mothers Name:$this->mothers_name<br> 
		Academy Batch Id:$this->academy_batch_id<br> 
		Created At:$this->created_at<br> 
		Dob:$this->dob<br> 
		Photo:$this->photo<br> 
		Contact No:$this->contact_no<br> 
		Address:$this->address<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbAcademyStudent"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}academy_students");
		while($academystudent=$result->fetch_object()){
			$html.="<option value ='$academystudent->id'>$academystudent->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
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
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"academystudent/show/$academystudent->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"academystudent/edit/$academystudent->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"academystudent/confirm/$academystudent->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$academystudent->id</td><td>$academystudent->name</td><td>$academystudent->fathers_name</td><td>$academystudent->mothers_name</td><td>$academystudent->academy_batch_id</td><td>$academystudent->created_at</td><td>$academystudent->dob</td><td><img src='$base_url/img/$academystudent->photo' width='100' /></td><td>$academystudent->contact_no</td><td>$academystudent->address</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,name,fathers_name,mothers_name,academy_batch_id,created_at,dob,photo,contact_no,address from {$tx}academy_students where id={$id}");
		$academystudent=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">AcademyStudent Show</th></tr>";
		$html.="<tr><th>Id</th><td>$academystudent->id</td></tr>";
		$html.="<tr><th>Name</th><td>$academystudent->name</td></tr>";
		$html.="<tr><th>Fathers Name</th><td>$academystudent->fathers_name</td></tr>";
		$html.="<tr><th>Mothers Name</th><td>$academystudent->mothers_name</td></tr>";
		$html.="<tr><th>Academy Batch Id</th><td>$academystudent->academy_batch_id</td></tr>";
		$html.="<tr><th>Created At</th><td>$academystudent->created_at</td></tr>";
		$html.="<tr><th>Dob</th><td>$academystudent->dob</td></tr>";
		$html.="<tr><th>Photo</th><td><img src='$base_url/img/$academystudent->photo' width='100' /></td></tr>";
		$html.="<tr><th>Contact No</th><td>$academystudent->contact_no</td></tr>";
		$html.="<tr><th>Address</th><td>$academystudent->address</td></tr>";

		$html.="</table>";
		return $html;
	}

	
}
?>
