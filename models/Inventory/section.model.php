<?php
class Section implements JsonSerializable{
	public $id;
	public $name;

	public function __construct(){
	}
	public function set($id,$name){
		$this->id=$id;
		$this->name=$name;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}sections(name)values('$this->name')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}sections set name='$this->name' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}sections where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name from {$tx}sections");
		$data=[];
		while($section=$result->fetch_object()){
			$data[]=$section;
		}
			return $data;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name from {$tx}sections where id='$id'");
		$section=$result->fetch_object();
			return $section;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}sections");
		$section =$result->fetch_object();
		return $section->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbSection"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}sections");
		while($section=$result->fetch_object()){
			$html.="<option value ='$section->id'>$section->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}sections");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name from {$tx}sections limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-section\">New Section</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th></tr>";
		}
		while($section=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$section->id, "name"=>"Details", "value"=>"Detials", "class"=>"info", "url"=>"details-section"]);
				$action_buttons.= action_button(["id"=>$section->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-section"]);
				$action_buttons.= action_button(["id"=>$section->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"sections"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$section->id</td><td>$section->name</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,name from {$tx}sections where id={$id}");
		$section=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Section Details</th></tr>";
		$html.="<tr><th>Id</th><td>$section->id</td></tr>";
		$html.="<tr><th>Name</th><td>$section->name</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
