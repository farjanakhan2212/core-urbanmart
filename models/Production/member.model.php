<?php
class Member implements JsonSerializable{
	public $id;
	public $name;
	public $member_type_id;
	public $mobile;
	public $address;
	public $doj;
	public $photo;
	public $created_at;

	public function __construct(){
	}
	public function set($id,$name,$member_type_id,$mobile,$address,$doj,$photo,$created_at){
		$this->id=$id;
		$this->name=$name;
		$this->member_type_id=$member_type_id;
		$this->mobile=$mobile;
		$this->address=$address;
		$this->doj=$doj;
		$this->photo=$photo;
		$this->created_at=$created_at;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}members(name,member_type_id,mobile,address,doj,photo,created_at)values('$this->name','$this->member_type_id','$this->mobile','$this->address','$this->doj','$this->photo','$this->created_at')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}members set name='$this->name',member_type_id='$this->member_type_id',mobile='$this->mobile',address='$this->address',doj='$this->doj',photo='$this->photo',created_at='$this->created_at' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}members where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,member_type_id,mobile,address,doj,photo,created_at from {$tx}members");
		$data=[];
		while($member=$result->fetch_object()){
			$data[]=$member;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,member_type_id,mobile,address,doj,photo,created_at from {$tx}members $criteria limit $top,$perpage");
		$data=[];
		while($member=$result->fetch_object()){
			$data[]=$member;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}members $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,member_type_id,mobile,address,doj,photo,created_at from {$tx}members where id='$id'");
		$member=$result->fetch_object();
			return $member;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}members");
		$member =$result->fetch_object();
		return $member->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Member Type Id:$this->member_type_id<br> 
		Mobile:$this->mobile<br> 
		Address:$this->address<br> 
		Doj:$this->doj<br> 
		Photo:$this->photo<br> 
		Created At:$this->created_at<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbMember"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}members");
		while($member=$result->fetch_object()){
			$html.="<option value ='$member->id'>$member->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}members $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,member_type_id,mobile,address,doj,photo,created_at from {$tx}members $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-member\">New Member</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Member Type Id</th><th>Mobile</th><th>Address</th><th>Doj</th><th>Photo</th><th>Created At</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Member Type Id</th><th>Mobile</th><th>Address</th><th>Doj</th><th>Photo</th><th>Created At</th></tr>";
		}
		while($member=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$member->id, "name"=>"Details", "value"=>"Details", "class"=>"info", "url"=>"details-member"]);
				$action_buttons.= action_button(["id"=>$member->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-member"]);
				$action_buttons.= action_button(["id"=>$member->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"members"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$member->id</td><td>$member->name</td><td>$member->member_type_id</td><td>$member->mobile</td><td>$member->address</td><td>$member->doj</td><td><img src='img/$member->photo' width='100' /></td><td>$member->created_at</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,name,member_type_id,mobile,address,doj,photo,created_at from {$tx}members where id={$id}");
		$member=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Member Details</th></tr>";
		$html.="<tr><th>Id</th><td>$member->id</td></tr>";
		$html.="<tr><th>Name</th><td>$member->name</td></tr>";
		$html.="<tr><th>Member Type Id</th><td>$member->member_type_id</td></tr>";
		$html.="<tr><th>Mobile</th><td>$member->mobile</td></tr>";
		$html.="<tr><th>Address</th><td>$member->address</td></tr>";
		$html.="<tr><th>Doj</th><td>$member->doj</td></tr>";
		$html.="<tr><th>Photo</th><td><img src='img/$member->photo' width='100' /></td></tr>";
		$html.="<tr><th>Created At</th><td>$member->created_at</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
