<?php
class TailorOrderDetail implements JsonSerializable{
	public $id;
	public $dress_id;
	public $price;
	public $qty;
	public $discount;
	public $order_id;

	public function __construct(){
	}
	public function set($id,$dress_id,$price,$qty,$discount,$order_id){
		$this->id=$id;
		$this->dress_id=$dress_id;
		$this->price=$price;
		$this->qty=$qty;
		$this->discount=$discount;
		$this->order_id=$order_id;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}tailor_order_details(dress_id,price,qty,discount,order_id)values('$this->dress_id','$this->price','$this->qty','$this->discount','$this->order_id')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}tailor_order_details set dress_id='$this->dress_id',price='$this->price',qty='$this->qty',discount='$this->discount',order_id='$this->order_id' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}tailor_order_details where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,dress_id,price,qty,discount,order_id from {$tx}tailor_order_details");
		$data=[];
		while($tailororderdetail=$result->fetch_object()){
			$data[]=$tailororderdetail;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,dress_id,price,qty,discount,order_id from {$tx}tailor_order_details $criteria limit $top,$perpage");
		$data=[];
		while($tailororderdetail=$result->fetch_object()){
			$data[]=$tailororderdetail;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}tailor_order_details $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,dress_id,price,qty,discount,order_id from {$tx}tailor_order_details where id='$id'");
		$tailororderdetail=$result->fetch_object();
			return $tailororderdetail;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}tailor_order_details");
		$tailororderdetail =$result->fetch_object();
		return $tailororderdetail->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Dress Id:$this->dress_id<br> 
		Price:$this->price<br> 
		Qty:$this->qty<br> 
		Discount:$this->discount<br> 
		Order Id:$this->order_id<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbTailorOrderDetail"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}tailor_order_details");
		while($tailororderdetail=$result->fetch_object()){
			$html.="<option value ='$tailororderdetail->id'>$tailororderdetail->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}tailor_order_details $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,dress_id,price,qty,discount,order_id from {$tx}tailor_order_details $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-tailororderdetail\">New TailorOrderDetail</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Dress Id</th><th>Price</th><th>Qty</th><th>Discount</th><th>Order Id</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Dress Id</th><th>Price</th><th>Qty</th><th>Discount</th><th>Order Id</th></tr>";
		}
		while($tailororderdetail=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$tailororderdetail->id, "name"=>"Details", "value"=>"Details", "class"=>"info", "url"=>"details-tailororderdetail"]);
				$action_buttons.= action_button(["id"=>$tailororderdetail->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-tailororderdetail"]);
				$action_buttons.= action_button(["id"=>$tailororderdetail->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"tailor_order_details"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$tailororderdetail->id</td><td>$tailororderdetail->dress_id</td><td>$tailororderdetail->price</td><td>$tailororderdetail->qty</td><td>$tailororderdetail->discount</td><td>$tailororderdetail->order_id</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,dress_id,price,qty,discount,order_id from {$tx}tailor_order_details where id={$id}");
		$tailororderdetail=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">TailorOrderDetail Details</th></tr>";
		$html.="<tr><th>Id</th><td>$tailororderdetail->id</td></tr>";
		$html.="<tr><th>Dress Id</th><td>$tailororderdetail->dress_id</td></tr>";
		$html.="<tr><th>Price</th><td>$tailororderdetail->price</td></tr>";
		$html.="<tr><th>Qty</th><td>$tailororderdetail->qty</td></tr>";
		$html.="<tr><th>Discount</th><td>$tailororderdetail->discount</td></tr>";
		$html.="<tr><th>Order Id</th><td>$tailororderdetail->order_id</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
