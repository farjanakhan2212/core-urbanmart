<?php
class TailorOrder implements JsonSerializable{
	public $id;
	public $customer_name;
	public $paid_amount;
	public $order_datetime;
	public $delivery_datetime;
	public $order_total;
	public $shipping_address;
	public $remark;
	public $mobile;
	public $discount;

	public function __construct(){
	}
	public function set($id,$customer_name,$paid_amount,$order_datetime,$delivery_datetime,$order_total,$shipping_address,$remark,$mobile,$discount){
		$this->id=$id;
		$this->customer_name=$customer_name;
		$this->paid_amount=$paid_amount;
		$this->order_datetime=$order_datetime;
		$this->delivery_datetime=$delivery_datetime;
		$this->order_total=$order_total;
		$this->shipping_address=$shipping_address;
		$this->remark=$remark;
		$this->mobile=$mobile;
		$this->discount=$discount;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}tailor_orders(customer_name,paid_amount,order_datetime,delivery_datetime,order_total,shipping_address,remark,mobile,discount)values('$this->customer_name','$this->paid_amount','$this->order_datetime','$this->delivery_datetime','$this->order_total','$this->shipping_address','$this->remark','$this->mobile','$this->discount')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}tailor_orders set customer_name='$this->customer_name',paid_amount='$this->paid_amount',order_datetime='$this->order_datetime',delivery_datetime='$this->delivery_datetime',order_total='$this->order_total',shipping_address='$this->shipping_address',remark='$this->remark',mobile='$this->mobile',discount='$this->discount' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}tailor_orders where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,customer_name,paid_amount,order_datetime,delivery_datetime,order_total,shipping_address,remark,mobile,discount from {$tx}tailor_orders");
		$data=[];
		while($tailororder=$result->fetch_object()){
			$data[]=$tailororder;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,customer_name,paid_amount,order_datetime,delivery_datetime,order_total,shipping_address,remark,mobile,discount from {$tx}tailor_orders $criteria limit $top,$perpage");
		$data=[];
		while($tailororder=$result->fetch_object()){
			$data[]=$tailororder;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}tailor_orders $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,customer_name,paid_amount,order_datetime,delivery_datetime,order_total,shipping_address,remark,mobile,discount from {$tx}tailor_orders where id='$id'");
		$tailororder=$result->fetch_object();
			return $tailororder;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}tailor_orders");
		$tailororder =$result->fetch_object();
		return $tailororder->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Customer Name:$this->customer_name<br> 
		Paid Amount:$this->paid_amount<br> 
		Order Datetime:$this->order_datetime<br> 
		Delivery Datetime:$this->delivery_datetime<br> 
		Order Total:$this->order_total<br> 
		Shipping Address:$this->shipping_address<br> 
		Remark:$this->remark<br> 
		Mobile:$this->mobile<br> 
		Discount:$this->discount<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbTailorOrder"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}tailor_orders");
		while($tailororder=$result->fetch_object()){
			$html.="<option value ='$tailororder->id'>$tailororder->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}tailor_orders $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,customer_name,paid_amount,order_datetime,delivery_datetime,order_total,shipping_address,remark,mobile,discount from {$tx}tailor_orders $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-tailororder\">New TailorOrder</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Customer Name</th><th>Paid Amount</th><th>Order Datetime</th><th>Delivery Datetime</th><th>Order Total</th><th>Shipping Address</th><th>Remark</th><th>Mobile</th><th>Discount</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Customer Name</th><th>Paid Amount</th><th>Order Datetime</th><th>Delivery Datetime</th><th>Order Total</th><th>Shipping Address</th><th>Remark</th><th>Mobile</th><th>Discount</th></tr>";
		}
		while($tailororder=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$tailororder->id, "name"=>"Details", "value"=>"Details", "class"=>"info", "url"=>"details-tailororder"]);
				$action_buttons.= action_button(["id"=>$tailororder->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-tailororder"]);
				$action_buttons.= action_button(["id"=>$tailororder->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"tailor_orders"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$tailororder->id</td><td>$tailororder->customer_name</td><td>$tailororder->paid_amount</td><td>$tailororder->order_datetime</td><td>$tailororder->delivery_datetime</td><td>$tailororder->order_total</td><td>$tailororder->shipping_address</td><td>$tailororder->remark</td><td>$tailororder->mobile</td><td>$tailororder->discount</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,customer_name,paid_amount,order_datetime,delivery_datetime,order_total,shipping_address,remark,mobile,discount from {$tx}tailor_orders where id={$id}");
		$tailororder=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">TailorOrder Details</th></tr>";
		$html.="<tr><th>Id</th><td>$tailororder->id</td></tr>";
		$html.="<tr><th>Customer Name</th><td>$tailororder->customer_name</td></tr>";
		$html.="<tr><th>Paid Amount</th><td>$tailororder->paid_amount</td></tr>";
		$html.="<tr><th>Order Datetime</th><td>$tailororder->order_datetime</td></tr>";
		$html.="<tr><th>Delivery Datetime</th><td>$tailororder->delivery_datetime</td></tr>";
		$html.="<tr><th>Order Total</th><td>$tailororder->order_total</td></tr>";
		$html.="<tr><th>Shipping Address</th><td>$tailororder->shipping_address</td></tr>";
		$html.="<tr><th>Remark</th><td>$tailororder->remark</td></tr>";
		$html.="<tr><th>Mobile</th><td>$tailororder->mobile</td></tr>";
		$html.="<tr><th>Discount</th><td>$tailororder->discount</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
