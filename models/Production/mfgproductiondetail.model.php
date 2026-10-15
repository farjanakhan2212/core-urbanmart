<?php
class MfgProductionDetail implements JsonSerializable{
	public $id;
	public $production_id;
	public $product_id;
	public $qty;
	public $uom_id;
	public $cost;

	public function __construct(){
	}
	public function set($id,$production_id,$product_id,$qty,$uom_id,$cost){
		$this->id=$id;
		$this->production_id=$production_id;
		$this->product_id=$product_id;
		$this->qty=$qty;
		$this->uom_id=$uom_id;
		$this->cost=$cost;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}mfg_production_details(production_id,product_id,qty,uom_id,cost)values('$this->production_id','$this->product_id','$this->qty','$this->uom_id','$this->cost')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}mfg_production_details set production_id='$this->production_id',product_id='$this->product_id',qty='$this->qty',uom_id='$this->uom_id',cost='$this->cost' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}mfg_production_details where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,production_id,product_id,qty,uom_id,cost from {$tx}mfg_production_details");
		$data=[];
		while($mfgproductiondetail=$result->fetch_object()){
			$data[]=$mfgproductiondetail;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,production_id,product_id,qty,uom_id,cost from {$tx}mfg_production_details $criteria limit $top,$perpage");
		$data=[];
		while($mfgproductiondetail=$result->fetch_object()){
			$data[]=$mfgproductiondetail;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}mfg_production_details $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,production_id,product_id,qty,uom_id,cost from {$tx}mfg_production_details where id='$id'");
		$mfgproductiondetail=$result->fetch_object();
			return $mfgproductiondetail;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}mfg_production_details");
		$mfgproductiondetail =$result->fetch_object();
		return $mfgproductiondetail->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Production Id:$this->production_id<br> 
		Product Id:$this->product_id<br> 
		Qty:$this->qty<br> 
		Uom Id:$this->uom_id<br> 
		Cost:$this->cost<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbMfgProductionDetail"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}mfg_production_details");
		while($mfgproductiondetail=$result->fetch_object()){
			$html.="<option value ='$mfgproductiondetail->id'>$mfgproductiondetail->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}mfg_production_details $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,production_id,product_id,qty,uom_id,cost from {$tx}mfg_production_details $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-mfgproductiondetail\">New MfgProductionDetail</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Production Id</th><th>Product Id</th><th>Qty</th><th>Uom Id</th><th>Cost</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Production Id</th><th>Product Id</th><th>Qty</th><th>Uom Id</th><th>Cost</th></tr>";
		}
		while($mfgproductiondetail=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$mfgproductiondetail->id, "name"=>"Details", "value"=>"Details", "class"=>"info", "url"=>"details-mfgproductiondetail"]);
				$action_buttons.= action_button(["id"=>$mfgproductiondetail->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-mfgproductiondetail"]);
				$action_buttons.= action_button(["id"=>$mfgproductiondetail->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"mfg_production_details"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$mfgproductiondetail->id</td><td>$mfgproductiondetail->production_id</td><td>$mfgproductiondetail->product_id</td><td>$mfgproductiondetail->qty</td><td>$mfgproductiondetail->uom_id</td><td>$mfgproductiondetail->cost</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,production_id,product_id,qty,uom_id,cost from {$tx}mfg_production_details where id={$id}");
		$mfgproductiondetail=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">MfgProductionDetail Details</th></tr>";
		$html.="<tr><th>Id</th><td>$mfgproductiondetail->id</td></tr>";
		$html.="<tr><th>Production Id</th><td>$mfgproductiondetail->production_id</td></tr>";
		$html.="<tr><th>Product Id</th><td>$mfgproductiondetail->product_id</td></tr>";
		$html.="<tr><th>Qty</th><td>$mfgproductiondetail->qty</td></tr>";
		$html.="<tr><th>Uom Id</th><td>$mfgproductiondetail->uom_id</td></tr>";
		$html.="<tr><th>Cost</th><td>$mfgproductiondetail->cost</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
