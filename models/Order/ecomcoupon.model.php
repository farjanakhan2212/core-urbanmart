<?php
class EcomCoupon extends Model implements JsonSerializable{
	public $id;
	public $coupon;
	public $percent_b2b;
	public $percent_b2c;
	public $percent_m;
	public $start_date;
	public $end_date;
	public $created_at;

	public function __construct(){
	}
	public function set($id,$coupon,$percent_b2b,$percent_b2c,$percent_m,$start_date,$end_date,$created_at){
		$this->id=$id;
		$this->coupon=$coupon;
		$this->percent_b2b=$percent_b2b;
		$this->percent_b2c=$percent_b2c;
		$this->percent_m=$percent_m;
		$this->start_date=$start_date;
		$this->end_date=$end_date;
		$this->created_at=$created_at;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}ecom_coupons(coupon,percent_b2b,percent_b2c,percent_m,start_date,end_date,created_at)values('$this->coupon','$this->percent_b2b','$this->percent_b2c','$this->percent_m','$this->start_date','$this->end_date','$this->created_at')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}ecom_coupons set coupon='$this->coupon',percent_b2b='$this->percent_b2b',percent_b2c='$this->percent_b2c',percent_m='$this->percent_m',start_date='$this->start_date',end_date='$this->end_date',created_at='$this->created_at' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}ecom_coupons where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,coupon,percent_b2b,percent_b2c,percent_m,start_date,end_date,created_at from {$tx}ecom_coupons");
		$data=[];
		while($ecomcoupon=$result->fetch_object()){
			$data[]=$ecomcoupon;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,coupon,percent_b2b,percent_b2c,percent_m,start_date,end_date,created_at from {$tx}ecom_coupons $criteria limit $top,$perpage");
		$data=[];
		while($ecomcoupon=$result->fetch_object()){
			$data[]=$ecomcoupon;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}ecom_coupons $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,coupon,percent_b2b,percent_b2c,percent_m,start_date,end_date,created_at from {$tx}ecom_coupons where id='$id'");
		$ecomcoupon=$result->fetch_object();
			return $ecomcoupon;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,coupon,percent_b2b,percent_b2c,percent_m,start_date,end_date,created_at from {$tx}ecom_coupons where name like '%$name%'");
		$data=[];
		while($ecomcoupon=$result->fetch_object()){
			$data[]=$ecomcoupon;
		}
			return $data;
	}

	public static function search($coupon){
		global $db,$tx;
		$result =$db->query("select id,coupon,percent_b2b,percent_b2c,percent_m,start_date,end_date,created_at from {$tx}ecom_coupons where coupon='$coupon'");
		$ecomcoupon=$result->fetch_object();
			return $ecomcoupon;
	}

	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}ecom_coupons");
		$ecomcoupon =$result->fetch_object();
		return $ecomcoupon->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Coupon:$this->coupon<br> 
		Percent B2b:$this->percent_b2b<br> 
		Percent B2c:$this->percent_b2c<br> 
		Percent M:$this->percent_m<br> 
		Start Date:$this->start_date<br> 
		End Date:$this->end_date<br> 
		Created At:$this->created_at<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbEcomCoupon"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}ecom_coupons");
		while($ecomcoupon=$result->fetch_object()){
			$html.="<option value ='$ecomcoupon->id'>$ecomcoupon->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}ecom_coupons $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,coupon,percent_b2b,percent_b2c,percent_m,start_date,end_date,created_at from {$tx}ecom_coupons $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Coupon</th><th>Percent B2b</th><th>Percent B2c</th><th>Percent M</th><th>Start Date</th><th>End Date</th><th>Created At</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Coupon</th><th>Percent B2b</th><th>Percent B2c</th><th>Percent M</th><th>Start Date</th><th>End Date</th><th>Created At</th></tr>";
		}
		while($ecomcoupon=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"ecomcoupon/show/$ecomcoupon->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"ecomcoupon/edit/$ecomcoupon->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"ecomcoupon/confirm/$ecomcoupon->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$ecomcoupon->id</td><td>$ecomcoupon->coupon</td><td>$ecomcoupon->percent_b2b</td><td>$ecomcoupon->percent_b2c</td><td>$ecomcoupon->percent_m</td><td>$ecomcoupon->start_date</td><td>$ecomcoupon->end_date</td><td>$ecomcoupon->created_at</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,coupon,percent_b2b,percent_b2c,percent_m,start_date,end_date,created_at from {$tx}ecom_coupons where id={$id}");
		$ecomcoupon=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">EcomCoupon Show</th></tr>";
		$html.="<tr><th>Id</th><td>$ecomcoupon->id</td></tr>";
		$html.="<tr><th>Coupon</th><td>$ecomcoupon->coupon</td></tr>";
		$html.="<tr><th>Percent B2b</th><td>$ecomcoupon->percent_b2b</td></tr>";
		$html.="<tr><th>Percent B2c</th><td>$ecomcoupon->percent_b2c</td></tr>";
		$html.="<tr><th>Percent M</th><td>$ecomcoupon->percent_m</td></tr>";
		$html.="<tr><th>Start Date</th><td>$ecomcoupon->start_date</td></tr>";
		$html.="<tr><th>End Date</th><td>$ecomcoupon->end_date</td></tr>";
		$html.="<tr><th>Created At</th><td>$ecomcoupon->created_at</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
