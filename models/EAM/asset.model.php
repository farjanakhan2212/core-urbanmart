<?php
class Asset extends Model implements JsonSerializable{
	public $id;
	public $name;
	public $price;
	public $purchased_at;

	public function __construct(){
	}
	public function set($id,$name,$price,$purchased_at){
		$this->id=$id;
		$this->name=$name;
		$this->price=$price;
		$this->purchased_at=$purchased_at;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}assets(name,price,purchased_at)values('$this->name','$this->price','$this->purchased_at')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}assets set name='$this->name',price='$this->price',purchased_at='$this->purchased_at' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}assets where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,price,purchased_at from {$tx}assets");
		$data=[];
		while($asset=$result->fetch_object()){
			$data[]=$asset;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,price,purchased_at from {$tx}assets $criteria limit $top,$perpage");
		$data=[];
		while($asset=$result->fetch_object()){
			$data[]=$asset;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}assets $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,price,purchased_at from {$tx}assets where id='$id'");
		$asset=$result->fetch_object();
			return $asset;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}assets");
		$asset =$result->fetch_object();
		return $asset->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Price:$this->price<br> 
		Purchased At:$this->purchased_at<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbAsset"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}assets");
		while($asset=$result->fetch_object()){
			$html.="<option value ='$asset->id'>$asset->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}assets $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,price,purchased_at from {$tx}assets $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'>".Html::link(["class"=>"btn btn-success","route"=>"asset/create","text"=>"New Asset"])."</th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Price</th><th>Purchased At</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Price</th><th>Purchased At</th></tr>";
		}
		while($asset=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"asset/show/$asset->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"asset/edit/$asset->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"asset/confirm/$asset->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$asset->id</td><td>$asset->name</td><td>$asset->price</td><td>$asset->purchased_at</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,name,price,purchased_at from {$tx}assets where id={$id}");
		$asset=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Asset Show</th></tr>";
		$html.="<tr><th>Id</th><td>$asset->id</td></tr>";
		$html.="<tr><th>Name</th><td>$asset->name</td></tr>";
		$html.="<tr><th>Price</th><td>$asset->price</td></tr>";
		$html.="<tr><th>Purchased At</th><td>$asset->purchased_at</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
