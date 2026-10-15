<?php
class AcademyPeriod extends Model implements JsonSerializable{
	public $id;
	public $name;
	public $time;

	public function __construct(){
	}
	public function set($id,$name,$time){
		$this->id=$id;
		$this->name=$name;
		$this->time=$time;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}academy_periods(name,time)values('$this->name','$this->time')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}academy_periods set name='$this->name',time='$this->time' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}academy_periods where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,time from {$tx}academy_periods");
		$data=[];
		while($academyperiod=$result->fetch_object()){
			$data[]=$academyperiod;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,time from {$tx}academy_periods $criteria limit $top,$perpage");
		$data=[];
		while($academyperiod=$result->fetch_object()){
			$data[]=$academyperiod;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}academy_periods $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,time from {$tx}academy_periods where id='$id'");
		$academyperiod=$result->fetch_object();
			return $academyperiod;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,name,time from {$tx}academy_periods where name like '%$name%'");
		$data=[];
		while($academyperiod=$result->fetch_object()){
			$data[]=$academyperiod;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}academy_periods");
		$academyperiod =$result->fetch_object();
		return $academyperiod->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Time:$this->time<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbAcademyPeriod"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}academy_periods");
		while($academyperiod=$result->fetch_object()){
			$html.="<option value ='$academyperiod->id'>$academyperiod->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}academy_periods $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,time from {$tx}academy_periods $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Time</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Time</th></tr>";
		}
		while($academyperiod=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"academyperiod/show/$academyperiod->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"academyperiod/edit/$academyperiod->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"academyperiod/confirm/$academyperiod->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$academyperiod->id</td><td>$academyperiod->name</td><td>$academyperiod->time</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,name,time from {$tx}academy_periods where id={$id}");
		$academyperiod=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">AcademyPeriod Show</th></tr>";
		$html.="<tr><th>Id</th><td>$academyperiod->id</td></tr>";
		$html.="<tr><th>Name</th><td>$academyperiod->name</td></tr>";
		$html.="<tr><th>Time</th><td>$academyperiod->time</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
