<?php
class Delivery extends Model implements JsonSerializable{
	public $id;
	public $create_at;
	public $order_id;
	public $person_id;
	public $shipper_id;
	public $shipped_at;
	public $delivery_status_id;

	public function __construct(){
	}
	public function set($id,$create_at,$order_id,$person_id,$shipper_id,$shipped_at,$delivery_status_id){
		$this->id=$id;
		$this->create_at=$create_at;
		$this->order_id=$order_id;
		$this->person_id=$person_id;
		$this->shipper_id=$shipper_id;
		$this->shipped_at=$shipped_at;
		$this->delivery_status_id=$delivery_status_id;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}deliveries(create_at,order_id,person_id,shipper_id,shipped_at,delivery_status_id)values('$this->create_at','$this->order_id','$this->person_id','$this->shipper_id','$this->shipped_at','$this->delivery_status_id')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}deliveries set create_at='$this->create_at',order_id='$this->order_id',person_id='$this->person_id',shipper_id='$this->shipper_id',shipped_at='$this->shipped_at',delivery_status_id='$this->delivery_status_id' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}deliveries where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,create_at,order_id,person_id,shipper_id,shipped_at,delivery_status_id from {$tx}deliveries");
		$data=[];
		while($delivery=$result->fetch_object()){
			$data[]=$delivery;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,create_at,order_id,person_id,shipper_id,shipped_at,delivery_status_id from {$tx}deliveries $criteria limit $top,$perpage");
		$data=[];
		while($delivery=$result->fetch_object()){
			$data[]=$delivery;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}deliveries $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,create_at,order_id,person_id,shipper_id,shipped_at,delivery_status_id from {$tx}deliveries where id='$id'");
		$delivery=$result->fetch_object();
			return $delivery;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,create_at,order_id,person_id,shipper_id,shipped_at,delivery_status_id from {$tx}deliveries where name like '%$name%'");
		$data=[];
		while($delivery=$result->fetch_object()){
			$data[]=$delivery;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}deliveries");
		$delivery =$result->fetch_object();
		return $delivery->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Create At:$this->create_at<br> 
		Order Id:$this->order_id<br> 
		Person Id:$this->person_id<br> 
		Shipper Id:$this->shipper_id<br> 
		Shipped At:$this->shipped_at<br> 
		Delivery Status Id:$this->delivery_status_id<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbDelivery"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}deliveries");
		while($delivery=$result->fetch_object()){
			$html.="<option value ='$delivery->id'>$delivery->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}deliveries $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,create_at,order_id,person_id,shipper_id,shipped_at,delivery_status_id from {$tx}deliveries $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Create At</th><th>Order Id</th><th>Person Id</th><th>Shipper Id</th><th>Shipped At</th><th>Delivery Status Id</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Create At</th><th>Order Id</th><th>Person Id</th><th>Shipper Id</th><th>Shipped At</th><th>Delivery Status Id</th></tr>";
		}
		while($delivery=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"delivery/show/$delivery->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"delivery/edit/$delivery->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"delivery/confirm/$delivery->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$delivery->id</td><td>$delivery->create_at</td><td>$delivery->order_id</td><td>$delivery->person_id</td><td>$delivery->shipper_id</td><td>$delivery->shipped_at</td><td>$delivery->delivery_status_id</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,create_at,order_id,person_id,shipper_id,shipped_at,delivery_status_id from {$tx}deliveries where id={$id}");
		$delivery=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Delivery Show</th></tr>";
		$html.="<tr><th>Id</th><td>$delivery->id</td></tr>";
		$html.="<tr><th>Create At</th><td>$delivery->create_at</td></tr>";
		$html.="<tr><th>Order Id</th><td>$delivery->order_id</td></tr>";
		$html.="<tr><th>Person Id</th><td>$delivery->person_id</td></tr>";
		$html.="<tr><th>Shipper Id</th><td>$delivery->shipper_id</td></tr>";
		$html.="<tr><th>Shipped At</th><td>$delivery->shipped_at</td></tr>";
		$html.="<tr><th>Delivery Status Id</th><td>$delivery->delivery_status_id</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
