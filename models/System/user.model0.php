<?php
/**
 * Legacy backup of the User model.
 *
 * This file is NOT auto-loaded by the framework (glob pattern "*.model.php"
 * does not match "user.model0.php") and is kept only as a fallback. It now
 * reflects the actual core_users schema (id, name, email, branch,
 * email_verified_at, password, remember_token, created_at, updated_at) and is
 * guarded so it can only be defined once.
 */
if (!class_exists('User')) {

class User implements JsonSerializable{
	public $id;
	public $name;
	public $email;
	public $branch;
	public $email_verified_at;
	public $password;
	public $remember_token;
	public $created_at;
	public $updated_at;

	public function __construct(){
	}
	public function set($id,$name,$email,$branch,$email_verified_at,$password,$remember_token,$created_at,$updated_at){
		$this->id=$id;
		$this->name=$name;
		$this->email=$email;
		$this->branch=$branch;
		$this->email_verified_at=$email_verified_at;
		$this->password=$password;
		$this->remember_token=$remember_token;
		$this->created_at=$created_at;
		$this->updated_at=$updated_at;
	}
	public function save(){
		global $db,$tx;
		$stmt=$db->prepare("insert into {$tx}users(name,email,password,created_at,updated_at) values(?,?,?,?,?)");
		$stmt->bind_param("sssss",$this->name,$this->email,$this->password,$this->created_at,$this->updated_at);
		$stmt->execute();
		$id=$db->insert_id;
		$stmt->close();
		return $id;
	}
	public function update(){
		global $db,$tx;
		$stmt=$db->prepare("update {$tx}users set name=?,email=?,password=? ,updated_at=? where id=?");
		$stmt->bind_param("ssssi",$this->name,$this->email,$this->password,$this->updated_at,$this->id);
		$stmt->execute();
		$stmt->close();
	}
	public static function delete($id){
		global $db,$tx;
		$stmt=$db->prepare("delete from {$tx}users where id=?");
		$stmt->bind_param("i",$id);
		$stmt->execute();
		$stmt->close();
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,email,branch,email_verified_at,password,remember_token,created_at,updated_at from {$tx}users");
		$data=[];
		while($user=$result->fetch_object()){
			$data[]=$user;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,email,branch,email_verified_at,password,remember_token,created_at,updated_at from {$tx}users $criteria limit $top,$perpage");
		$data=[];
		while($user=$result->fetch_object()){
			$data[]=$user;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}users $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,email,branch,email_verified_at,password,remember_token,created_at,updated_at from {$tx}users where id='$id'");
		$user=$result->fetch_object();
			return $user;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}users");
		$user =$result->fetch_object();
		return $user->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Email:$this->email<br> 
		Branch:$this->branch<br> 
		Email Verified At:$this->email_verified_at<br> 
		Created At:$this->created_at<br> 
		Updated At:$this->updated_at<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbUser"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}users");
		while($user=$result->fetch_object()){
			$html.="<option value ='$user->id'>$user->name</option>";
		}
		$html.="</select>";
		return $html;
	}

	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}users $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,email,branch,email_verified_at,remember_token,created_at,updated_at from {$tx}users $criteria limit $top,$perpage");
		$html="<table class='table'>";			
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Email</th><th>Created At</th><th>Updated At</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Email</th><th>Created At</th><th>Updated At</th></tr>";
		}
		while($user=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= Event::button(["id"=>$user->id, "name"=>"details", "value"=>"Details", "class"=>"btn btn-info", "route"=>"user/show/$user->id"]);
				$action_buttons.= Event::button(["id"=>$user->id, "name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"user/edit/$user->id"]);
				$action_buttons.= Event::button(["id"=>$user->id, "name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"user/confirm/$user->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$user->id</td><td>$user->name</td><td>$user->email</td><td>$user->created_at</td><td>$user->updated_at</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,name,email,branch,email_verified_at,remember_token,created_at,updated_at from {$tx}users where id={$id}");
		$user=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">User Details</th></tr>";
		$html.="<tr><th>Id</th><td>$user->id</td></tr>";
		$html.="<tr><th>Name</th><td>$user->name</td></tr>";
		$html.="<tr><th>Email</th><td>$user->email</td></tr>";
		$html.="<tr><th>Branch</th><td>$user->branch</td></tr>";
		$html.="<tr><th>Email Verified At</th><td>$user->email_verified_at</td></tr>";
		$html.="<tr><th>Created At</th><td>$user->created_at</td></tr>";
		$html.="<tr><th>Updated At</th><td>$user->updated_at</td></tr>";

		$html.="</table>";
		return $html;
	}
}

}
?>