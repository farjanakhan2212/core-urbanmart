<?php
class MfgProduction implements JsonSerializable{
	public $id;
	public $production_datetime;
	public $bom_id;
	public $qty;
	public $labor_cost;
	public $manager_id;
	public $total_cost;
	public $product_id;
	public $uom_id;

	public function __construct(){
	}
	public function set($id,$production_datetime,$bom_id,$qty,$labor_cost,$manager_id,$total_cost,$product_id,$uom_id){
		$this->id=$id;
		$this->production_datetime=$production_datetime;
		$this->bom_id=$bom_id;
		$this->qty=$qty;
		$this->labor_cost=$labor_cost;
		$this->manager_id=$manager_id;
		$this->total_cost=$total_cost;
		$this->product_id=$product_id;
		$this->uom_id=$uom_id;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}mfg_productions(production_datetime,bom_id,qty,labor_cost,manager_id,total_cost,product_id,uom_id)values('$this->production_datetime','$this->bom_id','$this->qty','$this->labor_cost','$this->manager_id','$this->total_cost','$this->product_id','$this->uom_id')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}mfg_productions set production_datetime='$this->production_datetime',bom_id='$this->bom_id',qty='$this->qty',labor_cost='$this->labor_cost',manager_id='$this->manager_id',total_cost='$this->total_cost',product_id='$this->product_id',uom_id='$this->uom_id' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}mfg_productions where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,production_datetime,bom_id,qty,labor_cost,manager_id,total_cost,product_id,uom_id from {$tx}mfg_productions");
		$data=[];
		while($mfgproduction=$result->fetch_object()){
			$data[]=$mfgproduction;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,production_datetime,bom_id,qty,labor_cost,manager_id,total_cost,product_id,uom_id from {$tx}mfg_productions $criteria limit $top,$perpage");
		$data=[];
		while($mfgproduction=$result->fetch_object()){
			$data[]=$mfgproduction;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}mfg_productions $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,production_datetime,bom_id,qty,labor_cost,manager_id,total_cost,product_id,uom_id from {$tx}mfg_productions where id='$id'");
		$mfgproduction=$result->fetch_object();
			return $mfgproduction;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}mfg_productions");
		$mfgproduction =$result->fetch_object();
		return $mfgproduction->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Production Datetime:$this->production_datetime<br> 
		Bom Id:$this->bom_id<br> 
		Qty:$this->qty<br> 
		Labor Cost:$this->labor_cost<br> 
		Manager Id:$this->manager_id<br> 
		Total Cost:$this->total_cost<br> 
		Product Id:$this->product_id<br> 
		Uom Id:$this->uom_id<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbMfgProduction"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}mfg_productions");
		while($mfgproduction=$result->fetch_object()){
			$html.="<option value ='$mfgproduction->id'>$mfgproduction->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}mfg_productions $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,production_datetime,bom_id,qty,labor_cost,manager_id,total_cost,product_id,uom_id from {$tx}mfg_productions $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-mfgproduction\">New MfgProduction</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Production Datetime</th><th>Bom Id</th><th>Qty</th><th>Labor Cost</th><th>Manager Id</th><th>Total Cost</th><th>Product Id</th><th>Uom Id</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Production Datetime</th><th>Bom Id</th><th>Qty</th><th>Labor Cost</th><th>Manager Id</th><th>Total Cost</th><th>Product Id</th><th>Uom Id</th></tr>";
		}
		while($mfgproduction=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$mfgproduction->id, "name"=>"Details", "value"=>"Details", "class"=>"info", "url"=>"details-mfgproduction"]);
				$action_buttons.= action_button(["id"=>$mfgproduction->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-mfgproduction"]);
				$action_buttons.= action_button(["id"=>$mfgproduction->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"mfg_productions"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$mfgproduction->id</td><td>$mfgproduction->production_datetime</td><td>$mfgproduction->bom_id</td><td>$mfgproduction->qty</td><td>$mfgproduction->labor_cost</td><td>$mfgproduction->manager_id</td><td>$mfgproduction->total_cost</td><td>$mfgproduction->product_id</td><td>$mfgproduction->uom_id</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,production_datetime,bom_id,qty,labor_cost,manager_id,total_cost,product_id,uom_id from {$tx}mfg_productions where id={$id}");
		$mfgproduction=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">MfgProduction Details</th></tr>";
		$html.="<tr><th>Id</th><td>$mfgproduction->id</td></tr>";
		$html.="<tr><th>Production Datetime</th><td>$mfgproduction->production_datetime</td></tr>";
		$html.="<tr><th>Bom Id</th><td>$mfgproduction->bom_id</td></tr>";
		$html.="<tr><th>Qty</th><td>$mfgproduction->qty</td></tr>";
		$html.="<tr><th>Labor Cost</th><td>$mfgproduction->labor_cost</td></tr>";
		$html.="<tr><th>Manager Id</th><td>$mfgproduction->manager_id</td></tr>";
		$html.="<tr><th>Total Cost</th><td>$mfgproduction->total_cost</td></tr>";
		$html.="<tr><th>Product Id</th><td>$mfgproduction->product_id</td></tr>";
		$html.="<tr><th>Uom Id</th><td>$mfgproduction->uom_id</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
