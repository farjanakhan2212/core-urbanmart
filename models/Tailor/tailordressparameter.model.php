<?php
class TailorDressParameter implements JsonSerializable{
	public $id;
	public $name;
	public $dress_id;
	public $default_size;

	public function __construct(){
	}
	public function set($id,$name,$dress_id,$default_size){
		$this->id=$id;
		$this->name=$name;
		$this->dress_id=$dress_id;
		$this->default_size=$default_size;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}tailor_dress_parameters(name,dress_id,default_size)values('$this->name','$this->dress_id','$this->default_size')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}tailor_dress_parameters set name='$this->name',dress_id='$this->dress_id',default_size='$this->default_size' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}tailor_dress_parameters where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,dress_id,default_size from {$tx}tailor_dress_parameters");
		$data=[];
		while($tailordressparameter=$result->fetch_object()){
			$data[]=$tailordressparameter;
		}
			return $data;
	}

	public static function filter($dress_id){
		global $db,$tx;
		$result=$db->query("select id,name,dress_id,default_size from {$tx}tailor_dress_parameters where dress_id='$dress_id'");
		$data=[];
		while($tailordressparameter=$result->fetch_object()){
			$data[]=$tailordressparameter;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,dress_id,default_size from {$tx}tailor_dress_parameters $criteria limit $top,$perpage");
		$data=[];
		while($tailordressparameter=$result->fetch_object()){
			$data[]=$tailordressparameter;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}tailor_dress_parameters $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,dress_id,default_size from {$tx}tailor_dress_parameters where id='$id'");
		$tailordressparameter=$result->fetch_object();
			return $tailordressparameter;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}tailor_dress_parameters");
		$tailordressparameter =$result->fetch_object();
		return $tailordressparameter->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Dress Id:$this->dress_id<br> 
		Default Size:$this->default_size<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbTailorDressParameter"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}tailor_dress_parameters");
		while($tailordressparameter=$result->fetch_object()){
			$html.="<option value ='$tailordressparameter->id'>$tailordressparameter->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}tailor_dress_parameters $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,dress_id,default_size from {$tx}tailor_dress_parameters $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-tailordressparameter\">New TailorDressParameter</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Dress Id</th><th>Default Size</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Dress Id</th><th>Default Size</th></tr>";
		}
		while($tailordressparameter=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$tailordressparameter->id, "name"=>"Details", "value"=>"Details", "class"=>"info", "url"=>"details-tailordressparameter"]);
				$action_buttons.= action_button(["id"=>$tailordressparameter->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-tailordressparameter"]);
				$action_buttons.= action_button(["id"=>$tailordressparameter->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"tailor_dress_parameters"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$tailordressparameter->id</td><td>$tailordressparameter->name</td><td>$tailordressparameter->dress_id</td><td>$tailordressparameter->default_size</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,name,dress_id,default_size from {$tx}tailor_dress_parameters where id={$id}");
		$tailordressparameter=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">TailorDressParameter Details</th></tr>";
		$html.="<tr><th>Id</th><td>$tailordressparameter->id</td></tr>";
		$html.="<tr><th>Name</th><td>$tailordressparameter->name</td></tr>";
		$html.="<tr><th>Dress Id</th><td>$tailordressparameter->dress_id</td></tr>";
		$html.="<tr><th>Default Size</th><td>$tailordressparameter->default_size</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
