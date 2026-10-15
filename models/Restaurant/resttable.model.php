<?php
class RestTable extends Model implements JsonSerializable{
	public $id;
	public $name;
	public $status;
	public $photo;
	public $seats;

	public function __construct(){
	}
	public function set($id,$name,$status,$photo,$seats){
		$this->id=$id;
		$this->name=$name;
		$this->status=$status;
		$this->photo=$photo;
		$this->seats=$seats;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}rest_tables(name,status,photo,seats)values('$this->name','$this->status','$this->photo','$this->seats')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}rest_tables set name='$this->name',status='$this->status',photo='$this->photo',seats='$this->seats' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}rest_tables where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,status,photo,seats from {$tx}rest_tables");
		$data=[];
		while($resttable=$result->fetch_object()){
			$data[]=$resttable;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,status,photo,seats from {$tx}rest_tables $criteria limit $top,$perpage");
		$data=[];
		while($resttable=$result->fetch_object()){
			$data[]=$resttable;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}rest_tables $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,status,photo,seats from {$tx}rest_tables where id='$id'");
		$resttable=$result->fetch_object();
			return $resttable;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,name,status,photo,seats from {$tx}rest_tables where name like '%$name%'");
		$data=[];
		while($resttable=$result->fetch_object()){
			$data[]=$resttable;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}rest_tables");
		$resttable =$result->fetch_object();
		return $resttable->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Status:$this->status<br> 
		Photo:$this->photo<br> 
		Seats:$this->seats<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbRestTable"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}rest_tables");
		while($resttable=$result->fetch_object()){
			$html.="<option value ='$resttable->id'>$resttable->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}rest_tables $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,status,photo,seats from {$tx}rest_tables $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Status</th><th>Photo</th><th>Seats</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Status</th><th>Photo</th><th>Seats</th></tr>";
		}
		while($resttable=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"resttable/show/$resttable->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"resttable/edit/$resttable->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"resttable/confirm/$resttable->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$resttable->id</td><td>$resttable->name</td><td>$resttable->status</td><td><img src='$base_url/img/$resttable->photo' width='100' /></td><td>$resttable->seats</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,name,status,photo,seats from {$tx}rest_tables where id={$id}");
		$resttable=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">RestTable Show</th></tr>";
		$html.="<tr><th>Id</th><td>$resttable->id</td></tr>";
		$html.="<tr><th>Name</th><td>$resttable->name</td></tr>";
		$html.="<tr><th>Status</th><td>$resttable->status</td></tr>";
		$html.="<tr><th>Photo</th><td><img src='$base_url/img/$resttable->photo' width='100' /></td></tr>";
		$html.="<tr><th>Seats</th><td>$resttable->seats</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
