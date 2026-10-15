<?php
class Role implements JsonSerializable{
	public $id;
	public $name;
	public $label;

	public function __construct(){
	}
	public function set($id,$name,$label){
		$this->id=$id;
		$this->name=$name;
		$this->label=$label;

	}
	public function save(){
		global $db,$tx;
		$stmt=$db->prepare("insert into {$tx}roles(name,label) values(?,?)");
		$stmt->bind_param("ss",$this->name,$this->label);
		$stmt->execute();
		$id=$db->insert_id;
		$stmt->close();
		return $id;
	}
	public function update(){
		global $db,$tx;
		$stmt=$db->prepare("update {$tx}roles set name=?,label=? where id=?");
		$stmt->bind_param("ssi",$this->name,$this->label,$this->id);
		$stmt->execute();
		$stmt->close();
	}
	public static function delete($id){
		global $db,$tx;
		$stmt=$db->prepare("delete from {$tx}roles where id=?");
		$stmt->bind_param("i",$id);
		$stmt->execute();
		$stmt->close();
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,label from {$tx}roles");
		$data=[];
		while($role=$result->fetch_object()){
			$data[]=$role;
		}
			return $data;
	}
	public static function find($id){
		global $db,$tx;
		$stmt=$db->prepare("select id,name,label from {$tx}roles where id=?");
		$stmt->bind_param("i",$id);
		$stmt->execute();
		$role=$stmt->get_result()->fetch_object();
		$stmt->close();
			return $role;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}roles");
		$role =$result->fetch_object();
		return $role->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Label:$this->label<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbRole"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}roles");
		while($role=$result->fetch_object()){
			$html.="<option value ='$role->id'>$role->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}roles");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,label from {$tx}roles limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-role\">New Role</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Label</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Label</th></tr>";
		}
		while($role=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$role->id, "name"=>"Details", "value"=>"Details", "class"=>"info", "url"=>"details-role"]);
				$action_buttons.= action_button(["id"=>$role->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-role"]);
				$action_buttons.= action_button(["id"=>$role->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"roles"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$role->id</td><td>$role->name</td><td>$role->label</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,name,label from {$tx}roles where id={$id}");
		$role=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Role Details</th></tr>";
		$html.="<tr><th>Id</th><td>$role->id</td></tr>";
		$html.="<tr><th>Name</th><td>$role->name</td></tr>";
		$html.="<tr><th>Label</th><td>$role->label</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>