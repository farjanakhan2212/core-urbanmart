<?php
class Supplier extends Model implements JsonSerializable{
	public $id;
	public $name;
	public $mobile;
	public $email;
	public $photo;

	public function __construct(){
	}
	public function set($id,$name,$mobile,$email,$photo){
		$this->id=$id;
		$this->name=$name;
		$this->mobile=$mobile;
		$this->email=$email;
		$this->photo=$photo;

	}
	public function save(){
		global $db,$tx,$now;
		$db->query("insert into {$tx}suppliers(name,mobile,email,photo)values('$this->name','$this->mobile','$this->email','$this->photo')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx,$now;
		$db->query("update {$tx}suppliers set name='$this->name',mobile='$this->mobile',email='$this->email',photo='$this->photo' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}suppliers where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,mobile,email,photo from {$tx}suppliers");
		$data=[];
		while($supplier=$result->fetch_object()){
			$data[]=$supplier;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,mobile,email,photo from {$tx}suppliers $criteria limit $top,$perpage");
		$data=[];
		while($supplier=$result->fetch_object()){
			$data[]=$supplier;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}suppliers $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,mobile,email,photo from {$tx}suppliers where id='$id'");
		$supplier=$result->fetch_object();
			return $supplier;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,name,mobile,email,photo from {$tx}suppliers where name like '%$name%'");
		$data=[];
		while($supplier=$result->fetch_object()){
			$data[]=$supplier;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}suppliers");
		$supplier =$result->fetch_object();
		return $supplier->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Mobile:$this->mobile<br> 
		Email:$this->email<br> 
		Photo:$this->photo<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbSupplier"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}suppliers");
		while($supplier=$result->fetch_object()){
			$html.="<option value ='$supplier->id'>$supplier->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}suppliers $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,mobile,email,photo from {$tx}suppliers $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Mobile</th><th>Email</th><th>Photo</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Mobile</th><th>Email</th><th>Photo</th></tr>";
		}
		while($supplier=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"supplier/show/$supplier->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"supplier/edit/$supplier->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"supplier/confirm/$supplier->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$supplier->id</td><td>$supplier->name</td><td>$supplier->mobile</td><td>$supplier->email</td><td><img src='$base_url/img/$supplier->photo' width='100' /></td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,name,mobile,email,photo from {$tx}suppliers where id={$id}");
		$supplier=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Supplier Show</th></tr>";
		$html.="<tr><th>Id</th><td>$supplier->id</td></tr>";
		$html.="<tr><th>Name</th><td>$supplier->name</td></tr>";
		$html.="<tr><th>Mobile</th><td>$supplier->mobile</td></tr>";
		$html.="<tr><th>Email</th><td>$supplier->email</td></tr>";
		$html.="<tr><th>Photo</th><td><img src='$base_url/img/$supplier->photo' width='100' /></td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
