<?php
class AcademyBatche extends Model implements JsonSerializable{
	public $id;
	public $name;
	public $current_class_id;

	public function __construct(){
	}
	public function set($id,$name,$current_class_id){
		$this->id=$id;
		$this->name=$name;
		$this->current_class_id=$current_class_id;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}academy_batches(name,current_class_id)values('$this->name','$this->current_class_id')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}academy_batches set name='$this->name',current_class_id='$this->current_class_id' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}academy_batches where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,current_class_id from {$tx}academy_batches");
		$data=[];
		while($academybatche=$result->fetch_object()){
			$data[]=$academybatche;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,current_class_id from {$tx}academy_batches $criteria limit $top,$perpage");
		$data=[];
		while($academybatche=$result->fetch_object()){
			$data[]=$academybatche;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}academy_batches $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,current_class_id from {$tx}academy_batches where id='$id'");
		$academybatche=$result->fetch_object();
			return $academybatche;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,name,current_class_id from {$tx}academy_batches where name like '%$name%'");
		$data=[];
		while($academybatche=$result->fetch_object()){
			$data[]=$academybatche;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}academy_batches");
		$academybatche =$result->fetch_object();
		return $academybatche->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Current Class Id:$this->current_class_id<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbAcademyBatche"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}academy_batches");
		while($academybatche=$result->fetch_object()){
			$html.="<option value ='$academybatche->id'>$academybatche->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}academy_batches $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,current_class_id from {$tx}academy_batches $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Current Class Id</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Current Class Id</th></tr>";
		}
		while($academybatche=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"academybatche/show/$academybatche->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"academybatche/edit/$academybatche->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"academybatche/confirm/$academybatche->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$academybatche->id</td><td>$academybatche->name</td><td>$academybatche->current_class_id</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,name,current_class_id from {$tx}academy_batches where id={$id}");
		$academybatche=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">AcademyBatche Show</th></tr>";
		$html.="<tr><th>Id</th><td>$academybatche->id</td></tr>";
		$html.="<tr><th>Name</th><td>$academybatche->name</td></tr>";
		$html.="<tr><th>Current Class Id</th><td>$academybatche->current_class_id</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
