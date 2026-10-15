<?php
class Invoice extends Model implements JsonSerializable{
	public $id;
	public $customer_id;
	public $created_at;
	public $remark;
	public $payment_term;
	public $updated_at;
	public $invoice_total;
	public $paid_total;
	public $previous_due;

	public function __construct(){
	}
	public function set($id,$customer_id,$created_at,$remark,$payment_term,$updated_at,$invoice_total,$paid_total,$previous_due){
		$this->id=$id;
		$this->customer_id=$customer_id;
		$this->created_at=$created_at;
		$this->remark=$remark;
		$this->payment_term=$payment_term;
		$this->updated_at=$updated_at;
		$this->invoice_total=$invoice_total;
		$this->paid_total=$paid_total;
		$this->previous_due=$previous_due;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}invoices(customer_id,created_at,remark,payment_term,updated_at,invoice_total,paid_total,previous_due)values('$this->customer_id','$this->created_at','$this->remark','$this->payment_term','$this->updated_at','$this->invoice_total','$this->paid_total','$this->previous_due')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}invoices set customer_id='$this->customer_id',created_at='$this->created_at',remark='$this->remark',payment_term='$this->payment_term',updated_at='$this->updated_at',invoice_total='$this->invoice_total',paid_total='$this->paid_total',previous_due='$this->previous_due' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$id=(int)$id;
		if($id<=0) return false;

		$check=$db->prepare("select id from {$tx}invoices where id=? limit 1");
		$check->bind_param("i",$id);
		$check->execute();
		$found=$check->get_result()->fetch_row();
		$check->close();
		if(!$found) return false;

		$db->begin_transaction();
		try {
			$details=$db->prepare("delete from {$tx}invoice_details where invoice_id=?");
			$details->bind_param("i",$id);
			$details->execute();
			$details->close();

			$header=$db->prepare("delete from {$tx}invoices where id=?");
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
		$result=$db->query("select id,customer_id,created_at,remark,payment_term,updated_at,invoice_total,paid_total,previous_due from {$tx}invoices");
		$data=[];
		while($invoice=$result->fetch_object()){
			$data[]=$invoice;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,customer_id,created_at,remark,payment_term,updated_at,invoice_total,paid_total,previous_due from {$tx}invoices $criteria limit $top,$perpage");
		$data=[];
		while($invoice=$result->fetch_object()){
			$data[]=$invoice;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}invoices $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,customer_id,created_at,remark,payment_term,updated_at,invoice_total,paid_total,previous_due from {$tx}invoices where id='$id'");
		$invoice=$result->fetch_object();
			return $invoice;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,customer_id,created_at,remark,payment_term,updated_at,invoice_total,paid_total,previous_due from {$tx}invoices where name like '%$name%'");
		$data=[];
		while($invoice=$result->fetch_object()){
			$data[]=$invoice;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}invoices");
		$invoice =$result->fetch_object();
		return $invoice->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Customer Id:$this->customer_id<br> 
		Created At:$this->created_at<br> 
		Remark:$this->remark<br> 
		Payment Term:$this->payment_term<br> 
		Updated At:$this->updated_at<br> 
		Invoice Total:$this->invoice_total<br> 
		Paid Total:$this->paid_total<br> 
		Previous Due:$this->previous_due<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbInvoice"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}invoices");
		while($invoice=$result->fetch_object()){
			$html.="<option value ='$invoice->id'>$invoice->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}invoices $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,customer_id,created_at,remark,payment_term,updated_at,invoice_total,paid_total,previous_due from {$tx}invoices $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Customer Id</th><th>Created At</th><th>Remark</th><th>Payment Term</th><th>Updated At</th><th>Invoice Total</th><th>Paid Total</th><th>Previous Due</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Customer Id</th><th>Created At</th><th>Remark</th><th>Payment Term</th><th>Updated At</th><th>Invoice Total</th><th>Paid Total</th><th>Previous Due</th></tr>";
		}
		while($invoice=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;flex-wrap:wrap;gap:3px;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"View", "class"=>"btn btn-info btn-sm", "route"=>"invoice/show/$invoice->id"]);
				$action_buttons.= Event::button(["name"=>"print", "value"=>"Print", "class"=>"btn btn-secondary btn-sm", "route"=>"invoice/print/$invoice->id"]);
				$action_buttons.= Event::button(["name"=>"pdf", "value"=>"PDF", "class"=>"btn btn-dark btn-sm", "route"=>"invoice/pdf/$invoice->id"]);
				$action_buttons.= Event::button(["name"=>"download", "value"=>"Download", "class"=>"btn btn-success btn-sm", "route"=>"invoice/download/$invoice->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary btn-sm", "route"=>"invoice/edit/$invoice->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger btn-sm", "route"=>"invoice/confirm/$invoice->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$invoice->id</td><td>$invoice->customer_id</td><td>$invoice->created_at</td><td>$invoice->remark</td><td>$invoice->payment_term</td><td>$invoice->updated_at</td><td>$invoice->invoice_total</td><td>$invoice->paid_total</td><td>$invoice->previous_due</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,customer_id,created_at,remark,payment_term,updated_at,invoice_total,paid_total,previous_due from {$tx}invoices where id={$id}");
		$invoice=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Invoice Show</th></tr>";
		$html.="<tr><th>Id</th><td>$invoice->id</td></tr>";
		$html.="<tr><th>Customer Id</th><td>$invoice->customer_id</td></tr>";
		$html.="<tr><th>Created At</th><td>$invoice->created_at</td></tr>";
		$html.="<tr><th>Remark</th><td>$invoice->remark</td></tr>";
		$html.="<tr><th>Payment Term</th><td>$invoice->payment_term</td></tr>";
		$html.="<tr><th>Updated At</th><td>$invoice->updated_at</td></tr>";
		$html.="<tr><th>Invoice Total</th><td>$invoice->invoice_total</td></tr>";
		$html.="<tr><th>Paid Total</th><td>$invoice->paid_total</td></tr>";
		$html.="<tr><th>Previous Due</th><td>$invoice->previous_due</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
