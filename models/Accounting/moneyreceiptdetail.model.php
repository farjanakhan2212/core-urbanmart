<?php
class MoneyReceiptDetail implements JsonSerializable{
	public $id;
	public $money_receipt_id;
	public $product_id;
	public $price;
	public $qty;

	public function __construct(){
	}
	public function set($id,$money_receipt_id,$product_id,$price,$qty){
		$this->id=$id;
		$this->money_receipt_id=$money_receipt_id;
		$this->product_id=$product_id;
		$this->price=$price;
		$this->qty=$qty;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}money_receipt_details(money_receipt_id,product_id,price,qty)values('$this->money_receipt_id','$this->product_id','$this->price','$this->qty')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}money_receipt_details set money_receipt_id='$this->money_receipt_id',product_id='$this->product_id',price='$this->price',qty='$this->qty' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}money_receipt_details where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,money_receipt_id,product_id,price,qty from {$tx}money_receipt_details");
		$data=[];
		while($moneyreceiptdetail=$result->fetch_object()){
			$data[]=$moneyreceiptdetail;
		}
			return $data;
	}

	public static function Filter($mr_id){
		global $db,$tx;
		$result=$db->query("select id,money_receipt_id,product_id,price,qty from {$tx}money_receipt_details where money_receipt_id='$mr_id'");
		$data=[];
		while($moneyreceiptdetail=$result->fetch_object()){
			$data[]=$moneyreceiptdetail;
		}
		return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,money_receipt_id,product_id,price,qty from {$tx}money_receipt_details $criteria limit $top,$perpage");
		$data=[];
		while($moneyreceiptdetail=$result->fetch_object()){
			$data[]=$moneyreceiptdetail;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}money_receipt_details $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,money_receipt_id,product_id,price,qty from {$tx}money_receipt_details where id='$id'");
		$moneyreceiptdetail=$result->fetch_object();
			return $moneyreceiptdetail;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}money_receipt_details");
		$moneyreceiptdetail =$result->fetch_object();
		return $moneyreceiptdetail->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Money Receipt Id:$this->money_receipt_id<br> 
		Product Id:$this->product_id<br> 
		Price:$this->price<br> 
		Qty:$this->qty<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbMoneyReceiptDetail"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}money_receipt_details");
		while($moneyreceiptdetail=$result->fetch_object()){
			$html.="<option value ='$moneyreceiptdetail->id'>$moneyreceiptdetail->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}money_receipt_details $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,money_receipt_id,product_id,price,qty from {$tx}money_receipt_details $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-moneyreceiptdetail\">New MoneyReceiptDetail</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Money Receipt Id</th><th>Product Id</th><th>Price</th><th>Qty</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Money Receipt Id</th><th>Product Id</th><th>Price</th><th>Qty</th></tr>";
		}
		while($moneyreceiptdetail=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$moneyreceiptdetail->id, "name"=>"Details", "value"=>"Details", "class"=>"info", "url"=>"details-moneyreceiptdetail"]);
				$action_buttons.= action_button(["id"=>$moneyreceiptdetail->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-moneyreceiptdetail"]);
				$action_buttons.= action_button(["id"=>$moneyreceiptdetail->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"money_receipt_details"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$moneyreceiptdetail->id</td><td>$moneyreceiptdetail->money_receipt_id</td><td>$moneyreceiptdetail->product_id</td><td>$moneyreceiptdetail->price</td><td>$moneyreceiptdetail->qty</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,money_receipt_id,product_id,price,qty from {$tx}money_receipt_details where id={$id}");
		$moneyreceiptdetail=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">MoneyReceiptDetail Details</th></tr>";
		$html.="<tr><th>Id</th><td>$moneyreceiptdetail->id</td></tr>";
		$html.="<tr><th>Money Receipt Id</th><td>$moneyreceiptdetail->money_receipt_id</td></tr>";
		$html.="<tr><th>Product Id</th><td>$moneyreceiptdetail->product_id</td></tr>";
		$html.="<tr><th>Price</th><td>$moneyreceiptdetail->price</td></tr>";
		$html.="<tr><th>Qty</th><td>$moneyreceiptdetail->qty</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
