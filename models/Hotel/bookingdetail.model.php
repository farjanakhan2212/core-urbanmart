<?php
class BookingDetail extends Model implements JsonSerializable{
	public $id;
	public $booking_id;
	public $room_id;
	public $from_date;
	public $to_date;
	public $price;

	public function __construct(){
	}
	public function set($id,$booking_id,$room_id,$from_date,$to_date,$price){
		$this->id=$id;
		$this->booking_id=$booking_id;
		$this->room_id=$room_id;
		$this->from_date=$from_date;
		$this->to_date=$to_date;
		$this->price=$price;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}booking_details(booking_id,room_id,from_date,to_date,price)values('$this->booking_id','$this->room_id','$this->from_date','$this->to_date','$this->price')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}booking_details set booking_id='$this->booking_id',room_id='$this->room_id',from_date='$this->from_date',to_date='$this->to_date',price='$this->price' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}booking_details where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,booking_id,room_id,from_date,to_date,price from {$tx}booking_details");
		$data=[];
		while($bookingdetail=$result->fetch_object()){
			$data[]=$bookingdetail;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,booking_id,room_id,from_date,to_date,price from {$tx}booking_details $criteria limit $top,$perpage");
		$data=[];
		while($bookingdetail=$result->fetch_object()){
			$data[]=$bookingdetail;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}booking_details $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,booking_id,room_id,from_date,to_date,price from {$tx}booking_details where id='$id'");
		$bookingdetail=$result->fetch_object();
			return $bookingdetail;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}booking_details");
		$bookingdetail =$result->fetch_object();
		return $bookingdetail->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Booking Id:$this->booking_id<br> 
		Room Id:$this->room_id<br> 
		From Date:$this->from_date<br> 
		To Date:$this->to_date<br> 
		Price:$this->price<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbBookingDetail"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}booking_details");
		while($bookingdetail=$result->fetch_object()){
			$html.="<option value ='$bookingdetail->id'>$bookingdetail->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}booking_details $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,booking_id,room_id,from_date,to_date,price from {$tx}booking_details $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'>".Html::link(["class"=>"btn btn-success","route"=>"bookingdetail/create","text"=>"New BookingDetail"])."</th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Booking Id</th><th>Room Id</th><th>From Date</th><th>To Date</th><th>Price</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Booking Id</th><th>Room Id</th><th>From Date</th><th>To Date</th><th>Price</th></tr>";
		}
		while($bookingdetail=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"bookingdetail/show/$bookingdetail->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"bookingdetail/edit/$bookingdetail->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"bookingdetail/confirm/$bookingdetail->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$bookingdetail->id</td><td>$bookingdetail->booking_id</td><td>$bookingdetail->room_id</td><td>$bookingdetail->from_date</td><td>$bookingdetail->to_date</td><td>$bookingdetail->price</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,booking_id,room_id,from_date,to_date,price from {$tx}booking_details where id={$id}");
		$bookingdetail=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">BookingDetail Show</th></tr>";
		$html.="<tr><th>Id</th><td>$bookingdetail->id</td></tr>";
		$html.="<tr><th>Booking Id</th><td>$bookingdetail->booking_id</td></tr>";
		$html.="<tr><th>Room Id</th><td>$bookingdetail->room_id</td></tr>";
		$html.="<tr><th>From Date</th><td>$bookingdetail->from_date</td></tr>";
		$html.="<tr><th>To Date</th><td>$bookingdetail->to_date</td></tr>";
		$html.="<tr><th>Price</th><td>$bookingdetail->price</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
