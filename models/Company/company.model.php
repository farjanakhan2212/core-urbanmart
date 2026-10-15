<?php
class Company extends Model implements JsonSerializable{
	public $id;
	public $name;
	public $mobile;
	public $bin;
	public $email;
	public $website;
	public $city;
	public $area;
	public $street_address;
	public $post_code;
	public $inactive;
	public $logo;

	public function __construct(){
	}
	public function set($id,$name,$mobile,$bin,$email,$website,$city,$area,$street_address,$post_code,$inactive,$logo){
		$this->id=$id;
		$this->name=$name;
		$this->mobile=$mobile;
		$this->bin=$bin;
		$this->email=$email;
		$this->website=$website;
		$this->city=$city;
		$this->area=$area;
		$this->street_address=$street_address;
		$this->post_code=$post_code;
		$this->inactive=$inactive;
		$this->logo=$logo;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}company(name,mobile,bin,email,website,city,area,street_address,post_code,inactive,logo)values('$this->name','$this->mobile','$this->bin','$this->email','$this->website','$this->city','$this->area','$this->street_address','$this->post_code','$this->inactive','$this->logo')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}company set name='$this->name',mobile='$this->mobile',bin='$this->bin',email='$this->email',website='$this->website',city='$this->city',area='$this->area',street_address='$this->street_address',post_code='$this->post_code',inactive='$this->inactive',logo='$this->logo' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}company where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,mobile,bin,email,website,city,area,street_address,post_code,inactive,logo from {$tx}company");
		$data=[];
		while($company=$result->fetch_object()){
			$data[]=$company;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,mobile,bin,email,website,city,area,street_address,post_code,inactive,logo from {$tx}company $criteria limit $top,$perpage");
		$data=[];
		while($company=$result->fetch_object()){
			$data[]=$company;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}company $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,mobile,bin,email,website,city,area,street_address,post_code,inactive,logo from {$tx}company where id='$id'");
		$company=$result->fetch_object();
			return $company;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,name,mobile,bin,email,website,city,area,street_address,post_code,inactive,logo from {$tx}company where name like '%$name%'");
		$data=[];
		while($company=$result->fetch_object()){
			$data[]=$company;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}company");
		$company =$result->fetch_object();
		return $company->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Mobile:$this->mobile<br> 
		Bin:$this->bin<br> 
		Email:$this->email<br> 
		Website:$this->website<br> 
		City:$this->city<br> 
		Area:$this->area<br> 
		Street Address:$this->street_address<br> 
		Post Code:$this->post_code<br> 
		Inactive:$this->inactive<br> 
		Logo:$this->logo<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbCompany"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}company");
		while($company=$result->fetch_object()){
			$html.="<option value ='$company->id'>$company->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}company $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,mobile,bin,email,website,city,area,street_address,post_code,inactive,logo from {$tx}company $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Mobile</th><th>Bin</th><th>Email</th><th>Website</th><th>City</th><th>Area</th><th>Street Address</th><th>Post Code</th><th>Inactive</th><th>Logo</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Mobile</th><th>Bin</th><th>Email</th><th>Website</th><th>City</th><th>Area</th><th>Street Address</th><th>Post Code</th><th>Inactive</th><th>Logo</th></tr>";
		}
		while($company=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"company/show/$company->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"company/edit/$company->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"company/confirm/$company->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$company->id</td><td>$company->name</td><td>$company->mobile</td><td>$company->bin</td><td>$company->email</td><td>$company->website</td><td>$company->city</td><td>$company->area</td><td>$company->street_address</td><td>$company->post_code</td><td>$company->inactive</td><td><img src='$base_url/img/$company->logo' width='100' /></td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,name,mobile,bin,email,website,city,area,street_address,post_code,inactive,logo from {$tx}company where id={$id}");
		$company=$result->fetch_object();
		if(!$company){
			return "<div class='alert alert-warning'>Company not found.</div>";
		}
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Company Show</th></tr>";
		$html.="<tr><th>Id</th><td>$company->id</td></tr>";
		$html.="<tr><th>Name</th><td>$company->name</td></tr>";
		$html.="<tr><th>Mobile</th><td>$company->mobile</td></tr>";
		$html.="<tr><th>Bin</th><td>$company->bin</td></tr>";
		$html.="<tr><th>Email</th><td>$company->email</td></tr>";
		$html.="<tr><th>Website</th><td>$company->website</td></tr>";
		$html.="<tr><th>City</th><td>$company->city</td></tr>";
		$html.="<tr><th>Area</th><td>$company->area</td></tr>";
		$html.="<tr><th>Street Address</th><td>$company->street_address</td></tr>";
		$html.="<tr><th>Post Code</th><td>$company->post_code</td></tr>";
		$html.="<tr><th>Inactive</th><td>$company->inactive</td></tr>";
		$html.="<tr><th>Logo</th><td><img src='$base_url/img/$company->logo' width='100' /></td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
