<?php
class DeliveryDetail extends Model implements JsonSerializable{
	public $id;
	public $delivery_id;
	public $product_id;
	public $qty;
	public $price;

	public function __construct(){
	}
	public function set($id,$delivery_id,$product_id,$qty,$price){
		$this->id=$id;
		$this->delivery_id=$delivery_id;
		$this->product_id=$product_id;
		$this->qty=$qty;
		$this->price=$price;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}delivery_details(delivery_id,product_id,qty,price)values('$this->delivery_id','$this->product_id','$this->qty','$this->price')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}delivery_details set delivery_id='$this->delivery_id',product_id='$this->product_id',qty='$this->qty',price='$this->price' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}delivery_details where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,delivery_id,product_id,qty,price from {$tx}delivery_details");
		$data=[];
		while($deliverydetail=$result->fetch_object()){
			$data[]=$deliverydetail;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,delivery_id,product_id,qty,price from {$tx}delivery_details $criteria limit $top,$perpage");
		$data=[];
		while($deliverydetail=$result->fetch_object()){
			$data[]=$deliverydetail;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}delivery_details $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,delivery_id,product_id,qty,price from {$tx}delivery_details where id='$id'");
		$deliverydetail=$result->fetch_object();
			return $deliverydetail;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,delivery_id,product_id,qty,price from {$tx}delivery_details where name like '%$name%'");
		$data=[];
		while($deliverydetail=$result->fetch_object()){
			$data[]=$deliverydetail;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}delivery_details");
		$deliverydetail =$result->fetch_object();
		return $deliverydetail->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Delivery Id:$this->delivery_id<br> 
		Product Id:$this->product_id<br> 
		Qty:$this->qty<br> 
		Price:$this->price<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbDeliveryDetail"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}delivery_details");
		while($deliverydetail=$result->fetch_object()){
			$html.="<option value ='$deliverydetail->id'>$deliverydetail->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}delivery_details $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,delivery_id,product_id,qty,price from {$tx}delivery_details $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Delivery Id</th><th>Product Id</th><th>Qty</th><th>Price</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Delivery Id</th><th>Product Id</th><th>Qty</th><th>Price</th></tr>";
		}
		while($deliverydetail=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"deliverydetail/show/$deliverydetail->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"deliverydetail/edit/$deliverydetail->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"deliverydetail/confirm/$deliverydetail->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$deliverydetail->id</td><td>$deliverydetail->delivery_id</td><td>$deliverydetail->product_id</td><td>$deliverydetail->qty</td><td>$deliverydetail->price</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,delivery_id,product_id,qty,price from {$tx}delivery_details where id={$id}");
		$deliverydetail=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">DeliveryDetail Show</th></tr>";
		$html.="<tr><th>Id</th><td>$deliverydetail->id</td></tr>";
		$html.="<tr><th>Delivery Id</th><td>$deliverydetail->delivery_id</td></tr>";
		$html.="<tr><th>Product Id</th><td>$deliverydetail->product_id</td></tr>";
		$html.="<tr><th>Qty</th><td>$deliverydetail->qty</td></tr>";
		$html.="<tr><th>Price</th><td>$deliverydetail->price</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
