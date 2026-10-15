<?php
class MoneyReceipt implements JsonSerializable{
	public $id;
	public $created_at;
	public $updated_at;
	public $customer_id;
	public $remark;
	public $receipt_total;

	public function __construct(){
	}
	public function set($id,$created_at,$updated_at,$customer_id,$remark,$receipt_total){
		$this->id=$id;
		$this->created_at=$created_at;
		$this->updated_at=$updated_at;
		$this->customer_id=$customer_id;
		$this->remark=$remark;
		$this->receipt_total=$receipt_total;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}money_receipts(created_at,updated_at,customer_id,remark,receipt_total)values('$this->created_at','$this->updated_at','$this->customer_id','$this->remark','$this->receipt_total')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}money_receipts set created_at='$this->created_at',updated_at='$this->updated_at',customer_id='$this->customer_id',remark='$this->remark',receipt_total='$this->receipt_total' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$id=(int)$id;
		if($id<=0) return false;

		$check=$db->prepare("select id from {$tx}money_receipts where id=? limit 1");
		$check->bind_param("i",$id);
		$check->execute();
		$found=$check->get_result()->fetch_row();
		$check->close();
		if(!$found) return false;

		$db->begin_transaction();
		try {
			$details=$db->prepare("delete from {$tx}money_receipt_details where money_receipt_id=?");
			$details->bind_param("i",$id);
			$details->execute();
			$details->close();

			$header=$db->prepare("delete from {$tx}money_receipts where id=?");
			$header->bind_param("i",$id);
			$header->execute();
			$header->close();

			$db->commit();
			return true;
		} catch (\Throwable $e) {
			$db->rollback();
			return false;
		}
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,created_at,updated_at,customer_id,remark,receipt_total from {$tx}money_receipts");
		$data=[];
		while($moneyreceipt=$result->fetch_object()){
			$data[]=$moneyreceipt;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,created_at,updated_at,customer_id,remark,receipt_total from {$tx}money_receipts $criteria limit $top,$perpage");
		$data=[];
		while($moneyreceipt=$result->fetch_object()){
			$data[]=$moneyreceipt;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}money_receipts $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,created_at,updated_at,customer_id,remark,receipt_total from {$tx}money_receipts where id='$id'");
		$moneyreceipt=$result->fetch_object();
			return $moneyreceipt;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}money_receipts");
		$moneyreceipt =$result->fetch_object();
		return $moneyreceipt->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Created At:$this->created_at<br> 
		Updated At:$this->updated_at<br> 
		Customer Id:$this->customer_id<br> 
		Remark:$this->remark<br> 
		Receipt Total:$this->receipt_total<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbMoneyReceipt"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}money_receipts");
		while($moneyreceipt=$result->fetch_object()){
			$html.="<option value ='$moneyreceipt->id'>$moneyreceipt->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}money_receipts $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,created_at,updated_at,customer_id,remark,receipt_total from {$tx}money_receipts $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"moneyreceipt/create\">New Money Receipt</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Created At</th><th>Updated At</th><th>Customer Id</th><th>Remark</th><th>Receipt Total</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Created At</th><th>Updated At</th><th>Customer Id</th><th>Remark</th><th>Receipt Total</th></tr>";
		}
		while($moneyreceipt=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;flex-wrap:wrap;gap:3px;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"View", "class"=>"btn btn-info btn-sm", "route"=>"moneyreceipt/show/$moneyreceipt->id"]);
				$action_buttons.= Event::button(["name"=>"print", "value"=>"Print", "class"=>"btn btn-secondary btn-sm", "route"=>"moneyreceipt/print/$moneyreceipt->id"]);
				$action_buttons.= Event::button(["name"=>"pdf", "value"=>"PDF", "class"=>"btn btn-dark btn-sm", "route"=>"moneyreceipt/pdf/$moneyreceipt->id"]);
				$action_buttons.= Event::button(["name"=>"download", "value"=>"Download", "class"=>"btn btn-success btn-sm", "route"=>"moneyreceipt/download/$moneyreceipt->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary btn-sm", "route"=>"moneyreceipt/edit/$moneyreceipt->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger btn-sm", "route"=>"moneyreceipt/confirm/$moneyreceipt->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$moneyreceipt->id</td><td>$moneyreceipt->created_at</td><td>$moneyreceipt->updated_at</td><td>$moneyreceipt->customer_id</td><td>$moneyreceipt->remark</td><td>$moneyreceipt->receipt_total</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;

		
		$result =$db->query("select id,created_at,updated_at,customer_id,remark,receipt_total from {$tx}money_receipts where id={$id}");
		$moneyreceipt=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">MoneyReceipt Details</th></tr>";
		$html.="<tr><th>Id</th><td>$moneyreceipt->id</td></tr>";
		$html.="<tr><th>Created At</th><td>$moneyreceipt->created_at</td></tr>";
		$html.="<tr><th>Updated At</th><td>$moneyreceipt->updated_at</td></tr>";
		$html.="<tr><th>Customer Id</th><td>$moneyreceipt->customer_id</td></tr>";
		$html.="<tr><th>Remark</th><td>$moneyreceipt->remark</td></tr>";
		$html.="<tr><th>Receipt Total</th><td>$moneyreceipt->receipt_total</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
