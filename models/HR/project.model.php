<?php
class Project implements JsonSerializable{

	public function __construct(){
	}
	public function set(){

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}projects()values()");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}projects set  where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}projects where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select  from {$tx}projects");
		$data=[];
		while($project=$result->fetch_object()){
			$data[]=$project;
		}
			return $data;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select  from {$tx}projects where id='$id'");
		$project=$result->fetch_object();
			return $project;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}projects");
		$project =$result->fetch_object();
		return $project->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "";
	}

	//-------------HTML----------//

	static function html_select($name="cmbProject"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}projects");
		while($project=$result->fetch_object()){
			$html.="<option value ='$project->id'>$project->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}projects");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select  from {$tx}projects limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-project\">New Project</a></th></tr>";
		if($action){
			$html.="<tr><th>Action</th></tr>";
		}else{
			$html.="<tr></tr>";
		}
		while($project=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$project->id, "name"=>"Details", "value"=>"Detials", "class"=>"info", "url"=>"details-project"]);
				$action_buttons.= action_button(["id"=>$project->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-project"]);
				$action_buttons.= action_button(["id"=>$project->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"projects"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select  from {$tx}projects where id={$id}");
		$project=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Project Details</th></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
