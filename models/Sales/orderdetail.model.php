<?php
class OrderDetail implements JsonSerializable{
	public $id;
	public $order_id;
	public $product_id;
	public $qty;
	public $price;
	public $vat;
	public $discount;
	public $created_at;
	public $updated_at;

	public function __construct(){
	}
	public function set($id,$order_id,$product_id,$qty,$price,$vat,$discount,$created_at,$updated_at){
		$this->id=$id;
		$this->order_id=$order_id;
		$this->product_id=$product_id;
		$this->qty=$qty;
		$this->price=$price;
		$this->vat=$vat;
		$this->discount=$discount;
		$this->created_at=$created_at;
		$this->updated_at=$updated_at;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}order_details(order_id,product_id,qty,price,vat,discount,created_at,updated_at)values('$this->order_id','$this->product_id','$this->qty','$this->price','$this->vat','$this->discount','$this->created_at','$this->updated_at')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}order_details set order_id='$this->order_id',product_id='$this->product_id',qty='$this->qty',price='$this->price',vat='$this->vat',discount='$this->discount',created_at='$this->created_at',updated_at='$this->updated_at' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}order_details where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,order_id,product_id,qty,price,vat,discount,created_at,updated_at from {$tx}order_details");
		$data=[];
		while($orderdetail=$result->fetch_object()){
			$data[]=$orderdetail;
		}
			return $data;
	}

	public static function all_by_order_id($order_id){
		global $db,$tx;
		$result=$db->query("select od.product_id,p.name,od.qty,od.price,od.vat,od.discount from {$tx}order_details od,{$tx}products p where p.id=od.product_id and order_id={$order_id}");
		$data=[];
		while($orderdetail=$result->fetch_object()){
			$data[]=$orderdetail;
		}
		return $data;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,order_id,product_id,qty,price,vat,discount,created_at,updated_at from {$tx}order_details where id='$id'");
		$orderdetail=$result->fetch_object();
		return $orderdetail;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}order_details");
		$orderdetail =$result->fetch_object();
		return $orderdetail->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Order Id:$this->order_id<br> 
		Product Id:$this->product_id<br> 
		Qty:$this->qty<br> 
		Price:$this->price<br> 
		Vat:$this->vat<br> 
		Discount:$this->discount<br> 
		Created At:$this->created_at<br> 
		Updated At:$this->updated_at<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbOrderDetail"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}order_details");
		while($orderdetail=$result->fetch_object()){
			$html.="<option value ='$orderdetail->id'>$orderdetail->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}order_details");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,order_id,product_id,qty,price,vat,discount,created_at,updated_at from {$tx}order_details limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-orderdetail\">New OrderDetail</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Order Id</th><th>Product Id</th><th>Qty</th><th>Price</th><th>Vat</th><th>Discount</th><th>Created At</th><th>Updated At</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Order Id</th><th>Product Id</th><th>Qty</th><th>Price</th><th>Vat</th><th>Discount</th><th>Created At</th><th>Updated At</th></tr>";
		}
		while($orderdetail=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$orderdetail->id, "name"=>"Details", "value"=>"Detials", "class"=>"info", "url"=>"details-orderdetail"]);
				$action_buttons.= action_button(["id"=>$orderdetail->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-orderdetail"]);
				$action_buttons.= action_button(["id"=>$orderdetail->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"order_details"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$orderdetail->id</td><td>$orderdetail->order_id</td><td>$orderdetail->product_id</td><td>$orderdetail->qty</td><td>$orderdetail->price</td><td>$orderdetail->vat</td><td>$orderdetail->discount</td><td>$orderdetail->created_at</td><td>$orderdetail->updated_at</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,order_id,product_id,qty,price,vat,discount,created_at,updated_at from {$tx}order_details where id={$id}");
		$orderdetail=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">OrderDetail Details</th></tr>";
		$html.="<tr><th>Id</th><td>$orderdetail->id</td></tr>";
		$html.="<tr><th>Order Id</th><td>$orderdetail->order_id</td></tr>";
		$html.="<tr><th>Product Id</th><td>$orderdetail->product_id</td></tr>";
		$html.="<tr><th>Qty</th><td>$orderdetail->qty</td></tr>";
		$html.="<tr><th>Price</th><td>$orderdetail->price</td></tr>";
		$html.="<tr><th>Vat</th><td>$orderdetail->vat</td></tr>";
		$html.="<tr><th>Discount</th><td>$orderdetail->discount</td></tr>";
		$html.="<tr><th>Created At</th><td>$orderdetail->created_at</td></tr>";
		$html.="<tr><th>Updated At</th><td>$orderdetail->updated_at</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
