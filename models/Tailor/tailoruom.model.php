<?php
class TailorUom implements JsonSerializable{
	public $id;
	public $abbr;
	public $name;
	public $inactive;

	public function __construct(){
	}
	public function set($id,$abbr,$name,$inactive){
		$this->id=$id;
		$this->abbr=$abbr;
		$this->name=$name;
		$this->inactive=$inactive;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}tailor_uoms(abbr,name,inactive)values('$this->abbr','$this->name','$this->inactive')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}tailor_uoms set abbr='$this->abbr',name='$this->name',inactive='$this->inactive' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}tailor_uoms where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,abbr,name,inactive from {$tx}tailor_uoms");
		$data=[];
		while($tailoruom=$result->fetch_object()){
			$data[]=$tailoruom;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,abbr,name,inactive from {$tx}tailor_uoms $criteria limit $top,$perpage");
		$data=[];
		while($tailoruom=$result->fetch_object()){
			$data[]=$tailoruom;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}tailor_uoms $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,abbr,name,inactive from {$tx}tailor_uoms where id='$id'");
		$tailoruom=$result->fetch_object();
			return $tailoruom;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}tailor_uoms");
		$tailoruom =$result->fetch_object();
		return $tailoruom->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Abbr:$this->abbr<br> 
		Name:$this->name<br> 
		Inactive:$this->inactive<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbTailorUom"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}tailor_uoms");
		while($tailoruom=$result->fetch_object()){
			$html.="<option value ='$tailoruom->id'>$tailoruom->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}tailor_uoms $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,abbr,name,inactive from {$tx}tailor_uoms $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-tailoruom\">New TailorUom</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Abbr</th><th>Name</th><th>Inactive</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Abbr</th><th>Name</th><th>Inactive</th></tr>";
		}
		while($tailoruom=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$tailoruom->id, "name"=>"Details", "value"=>"Details", "class"=>"info", "url"=>"details-tailoruom"]);
				$action_buttons.= action_button(["id"=>$tailoruom->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-tailoruom"]);
				$action_buttons.= action_button(["id"=>$tailoruom->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"tailor_uoms"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$tailoruom->id</td><td>$tailoruom->abbr</td><td>$tailoruom->name</td><td>$tailoruom->inactive</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,abbr,name,inactive from {$tx}tailor_uoms where id={$id}");
		$tailoruom=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">TailorUom Details</th></tr>";
		$html.="<tr><th>Id</th><td>$tailoruom->id</td></tr>";
		$html.="<tr><th>Abbr</th><td>$tailoruom->abbr</td></tr>";
		$html.="<tr><th>Name</th><td>$tailoruom->name</td></tr>";
		$html.="<tr><th>Inactive</th><td>$tailoruom->inactive</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
