<?php
class MfgBomDetail implements JsonSerializable{
	public $id;
	public $bom_id;
	public $product_id;
	public $qty;
	public $cost;
	public $uom_id;

	public function __construct(){
	}
	public function set($id,$bom_id,$product_id,$qty,$cost,$uom_id){
		$this->id=$id;
		$this->bom_id=$bom_id;
		$this->product_id=$product_id;
		$this->qty=$qty;
		$this->cost=$cost;
		$this->uom_id=$uom_id;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}mfg_bom_details(bom_id,product_id,qty,cost,uom_id)values('$this->bom_id','$this->product_id','$this->qty','$this->cost','$this->uom_id')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}mfg_bom_details set bom_id='$this->bom_id',product_id='$this->product_id',qty='$this->qty',cost='$this->cost',uom_id='$this->uom_id' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}mfg_bom_details where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,bom_id,product_id,qty,cost,uom_id from {$tx}mfg_bom_details");
		$data=[];
		while($mfgbomdetail=$result->fetch_object()){
			$data[]=$mfgbomdetail;
		}
			return $data;
	}

	public static function Filter($bom_id){
		global $db,$tx;
		$result=$db->query("select d.id,p.name product,p.id product_id,u.name uom,u.id uom_id,d.qty,d.cost from {$tx}mfg_bom_details d,{$tx}products p,{$tx}uoms u  where u.id=d.uom_id and p.id=d.product_id and d.bom_id='$bom_id'");
		$data=[];
		while($mfgbomdetail=$result->fetch_object()){
			$data[]=$mfgbomdetail;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,bom_id,product_id,qty,cost,uom_id from {$tx}mfg_bom_details $criteria limit $top,$perpage");
		$data=[];
		while($mfgbomdetail=$result->fetch_object()){
			$data[]=$mfgbomdetail;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}mfg_bom_details $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,bom_id,product_id,qty,cost,uom_id from {$tx}mfg_bom_details where id='$id'");
		$mfgbomdetail=$result->fetch_object();
			return $mfgbomdetail;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}mfg_bom_details");
		$mfgbomdetail =$result->fetch_object();
		return $mfgbomdetail->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Bom Id:$this->bom_id<br> 
		Product Id:$this->product_id<br> 
		Qty:$this->qty<br> 
		Cost:$this->cost<br> 
		Uom Id:$this->uom_id<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbMfgBomDetail"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}mfg_bom_details");
		while($mfgbomdetail=$result->fetch_object()){
			$html.="<option value ='$mfgbomdetail->id'>$mfgbomdetail->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}mfg_bom_details $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,bom_id,product_id,qty,cost,uom_id from {$tx}mfg_bom_details $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-mfgbomdetail\">New MfgBomDetail</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Bom Id</th><th>Product Id</th><th>Qty</th><th>Cost</th><th>Uom Id</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Bom Id</th><th>Product Id</th><th>Qty</th><th>Cost</th><th>Uom Id</th></tr>";
		}
		while($mfgbomdetail=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$mfgbomdetail->id, "name"=>"Details", "value"=>"Details", "class"=>"info", "url"=>"details-mfgbomdetail"]);
				$action_buttons.= action_button(["id"=>$mfgbomdetail->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-mfgbomdetail"]);
				$action_buttons.= action_button(["id"=>$mfgbomdetail->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"mfg_bom_details"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$mfgbomdetail->id</td><td>$mfgbomdetail->bom_id</td><td>$mfgbomdetail->product_id</td><td>$mfgbomdetail->qty</td><td>$mfgbomdetail->cost</td><td>$mfgbomdetail->uom_id</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,bom_id,product_id,qty,cost,uom_id from {$tx}mfg_bom_details where id={$id}");
		$mfgbomdetail=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">MfgBomDetail Details</th></tr>";
		$html.="<tr><th>Id</th><td>$mfgbomdetail->id</td></tr>";
		$html.="<tr><th>Bom Id</th><td>$mfgbomdetail->bom_id</td></tr>";
		$html.="<tr><th>Product Id</th><td>$mfgbomdetail->product_id</td></tr>";
		$html.="<tr><th>Qty</th><td>$mfgbomdetail->qty</td></tr>";
		$html.="<tr><th>Cost</th><td>$mfgbomdetail->cost</td></tr>";
		$html.="<tr><th>Uom Id</th><td>$mfgbomdetail->uom_id</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
