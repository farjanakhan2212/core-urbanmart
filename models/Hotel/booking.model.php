<?php
class Booking extends Model implements JsonSerializable{
	public $id;
	public $created_at;
	public $order_total;
	public $paid_total;
	public $remark;
	public $customer_id;

	public function __construct(){
	}
	public function set($id,$created_at,$order_total,$paid_total,$remark,$customer_id){
		$this->id=$id;
		$this->created_at=$created_at;
		$this->order_total=$order_total;
		$this->paid_total=$paid_total;
		$this->remark=$remark;
		$this->customer_id=$customer_id;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}bookings(created_at,order_total,paid_total,remark,customer_id)values('$this->created_at','$this->order_total','$this->paid_total','$this->remark','$this->customer_id')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}bookings set created_at='$this->created_at',order_total='$this->order_total',paid_total='$this->paid_total',remark='$this->remark',customer_id='$this->customer_id' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}bookings where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,created_at,order_total,paid_total,remark,customer_id from {$tx}bookings");
		$data=[];
		while($booking=$result->fetch_object()){
			$data[]=$booking;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,created_at,order_total,paid_total,remark,customer_id from {$tx}bookings $criteria limit $top,$perpage");
		$data=[];
		while($booking=$result->fetch_object()){
			$data[]=$booking;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}bookings $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,created_at,order_total,paid_total,remark,customer_id from {$tx}bookings where id='$id'");
		$booking=$result->fetch_object();
			return $booking;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}bookings");
		$booking =$result->fetch_object();
		return $booking->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Created At:$this->created_at<br> 
		Order Total:$this->order_total<br> 
		Paid Total:$this->paid_total<br> 
		Remark:$this->remark<br> 
		Customer Id:$this->customer_id<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbBooking"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}bookings");
		while($booking=$result->fetch_object()){
			$html.="<option value ='$booking->id'>$booking->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}bookings $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,created_at,order_total,paid_total,remark,customer_id from {$tx}bookings $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'>".Html::link(["class"=>"btn btn-success","route"=>"booking/create","text"=>"New Booking"])."</th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Created At</th><th>Order Total</th><th>Paid Total</th><th>Remark</th><th>Customer Id</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Created At</th><th>Order Total</th><th>Paid Total</th><th>Remark</th><th>Customer Id</th></tr>";
		}
		while($booking=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"booking/show/$booking->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"booking/edit/$booking->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"booking/confirm/$booking->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$booking->id</td><td>$booking->created_at</td><td>$booking->order_total</td><td>$booking->paid_total</td><td>$booking->remark</td><td>$booking->customer_id</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,created_at,order_total,paid_total,remark,customer_id from {$tx}bookings where id={$id}");
		$booking=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Booking Show</th></tr>";
		$html.="<tr><th>Id</th><td>$booking->id</td></tr>";
		$html.="<tr><th>Created At</th><td>$booking->created_at</td></tr>";
		$html.="<tr><th>Order Total</th><td>$booking->order_total</td></tr>";
		$html.="<tr><th>Paid Total</th><td>$booking->paid_total</td></tr>";
		$html.="<tr><th>Remark</th><td>$booking->remark</td></tr>";
		$html.="<tr><th>Customer Id</th><td>$booking->customer_id</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
