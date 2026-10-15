<?php
class TailorOrderMeasurement implements JsonSerializable{
	public $id;
	public $order_id;
	public $measurement_id;
	public $size;
	public $uom_id;
	public $dress_id;

	public function __construct(){
	}
	public function set($id,$order_id,$measurement_id,$size,$uom_id,$dress_id){
		$this->id=$id;
		$this->order_id=$order_id;
		$this->measurement_id=$measurement_id;
		$this->size=$size;
		$this->uom_id=$uom_id;
		$this->dress_id=$dress_id;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}tailor_order_measurements(order_id,measurement_id,size,uom_id,dress_id)values('$this->order_id','$this->measurement_id','$this->size','$this->uom_id','$this->dress_id')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}tailor_order_measurements set order_id='$this->order_id',measurement_id='$this->measurement_id',size='$this->size',uom_id='$this->uom_id',dress_id='$this->dress_id' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}tailor_order_measurements where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,order_id,measurement_id,size,uom_id,dress_id from {$tx}tailor_order_measurements");
		$data=[];
		while($tailorordermeasurement=$result->fetch_object()){
			$data[]=$tailorordermeasurement;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,order_id,measurement_id,size,uom_id,dress_id from {$tx}tailor_order_measurements $criteria limit $top,$perpage");
		$data=[];
		while($tailorordermeasurement=$result->fetch_object()){
			$data[]=$tailorordermeasurement;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}tailor_order_measurements $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,order_id,measurement_id,size,uom_id,dress_id from {$tx}tailor_order_measurements where id='$id'");
		$tailorordermeasurement=$result->fetch_object();
			return $tailorordermeasurement;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}tailor_order_measurements");
		$tailorordermeasurement =$result->fetch_object();
		return $tailorordermeasurement->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Order Id:$this->order_id<br> 
		Measurement Id:$this->measurement_id<br> 
		Size:$this->size<br> 
		Uom Id:$this->uom_id<br> 
		Dress Id:$this->dress_id<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbTailorOrderMeasurement"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}tailor_order_measurements");
		while($tailorordermeasurement=$result->fetch_object()){
			$html.="<option value ='$tailorordermeasurement->id'>$tailorordermeasurement->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}tailor_order_measurements $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,order_id,measurement_id,size,uom_id,dress_id from {$tx}tailor_order_measurements $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-tailorordermeasurement\">New TailorOrderMeasurement</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Order Id</th><th>Measurement Id</th><th>Size</th><th>Uom Id</th><th>Dress Id</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Order Id</th><th>Measurement Id</th><th>Size</th><th>Uom Id</th><th>Dress Id</th></tr>";
		}
		while($tailorordermeasurement=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$tailorordermeasurement->id, "name"=>"Details", "value"=>"Details", "class"=>"info", "url"=>"details-tailorordermeasurement"]);
				$action_buttons.= action_button(["id"=>$tailorordermeasurement->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-tailorordermeasurement"]);
				$action_buttons.= action_button(["id"=>$tailorordermeasurement->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"tailor_order_measurements"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$tailorordermeasurement->id</td><td>$tailorordermeasurement->order_id</td><td>$tailorordermeasurement->measurement_id</td><td>$tailorordermeasurement->size</td><td>$tailorordermeasurement->uom_id</td><td>$tailorordermeasurement->dress_id</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,order_id,measurement_id,size,uom_id,dress_id from {$tx}tailor_order_measurements where id={$id}");
		$tailorordermeasurement=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">TailorOrderMeasurement Details</th></tr>";
		$html.="<tr><th>Id</th><td>$tailorordermeasurement->id</td></tr>";
		$html.="<tr><th>Order Id</th><td>$tailorordermeasurement->order_id</td></tr>";
		$html.="<tr><th>Measurement Id</th><td>$tailorordermeasurement->measurement_id</td></tr>";
		$html.="<tr><th>Size</th><td>$tailorordermeasurement->size</td></tr>";
		$html.="<tr><th>Uom Id</th><td>$tailorordermeasurement->uom_id</td></tr>";
		$html.="<tr><th>Dress Id</th><td>$tailorordermeasurement->dress_id</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
