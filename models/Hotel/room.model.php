<?php
class Room extends Model implements JsonSerializable{
	public $id;
	public $name;
	public $code;
	public $room_type_id;
	public $price;

	public function __construct(){
	}
	public function set($id,$name,$code,$room_type_id,$price){
		$this->id=$id;
		$this->name=$name;
		$this->code=$code;
		$this->room_type_id=$room_type_id;
		$this->price=$price;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}rooms(name,code,room_type_id,price)values('$this->name','$this->code','$this->room_type_id','$this->price')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}rooms set name='$this->name',code='$this->code',room_type_id='$this->room_type_id',price='$this->price' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}rooms where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,code,room_type_id,price from {$tx}rooms");
		$data=[];
		while($room=$result->fetch_object()){
			$data[]=$room;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,code,room_type_id,price from {$tx}rooms $criteria limit $top,$perpage");
		$data=[];
		while($room=$result->fetch_object()){
			$data[]=$room;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}rooms $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,code,room_type_id,price from {$tx}rooms where id='$id'");
		$room=$result->fetch_object();
			return $room;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}rooms");
		$room =$result->fetch_object();
		return $room->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Code:$this->code<br> 
		Room Type Id:$this->room_type_id<br> 
		Price:$this->price<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbRoom"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}rooms");
		while($room=$result->fetch_object()){
			$html.="<option value ='$room->id'>$room->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}rooms $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,code,room_type_id,price from {$tx}rooms $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'>".Html::link(["class"=>"btn btn-success","route"=>"room/create","text"=>"New Room"])."</th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Code</th><th>Room Type Id</th><th>Price</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Code</th><th>Room Type Id</th><th>Price</th></tr>";
		}
		while($room=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"room/show/$room->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"room/edit/$room->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"room/confirm/$room->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$room->id</td><td>$room->name</td><td>$room->code</td><td>$room->room_type_id</td><td>$room->price</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,name,code,room_type_id,price from {$tx}rooms where id={$id}");
		$room=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Room Show</th></tr>";
		$html.="<tr><th>Id</th><td>$room->id</td></tr>";
		$html.="<tr><th>Name</th><td>$room->name</td></tr>";
		$html.="<tr><th>Code</th><td>$room->code</td></tr>";
		$html.="<tr><th>Room Type Id</th><td>$room->room_type_id</td></tr>";
		$html.="<tr><th>Price</th><td>$room->price</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
