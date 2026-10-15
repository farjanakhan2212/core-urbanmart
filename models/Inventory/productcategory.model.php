<?php
class ProductCategory extends Model implements JsonSerializable{
	public $id;
	public $name;
	public $section_id;
	public $created_at;
	public $updated_at;

	public function __construct(){
	}
	public function set($id,$name,$section_id,$created_at,$updated_at){
		$this->id=$id;
		$this->name=$name;
		$this->section_id=$section_id;
		$this->created_at=$created_at;
		$this->updated_at=$updated_at;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}product_categories(name,section_id,created_at,updated_at)values('$this->name','$this->section_id','$this->created_at','$this->updated_at')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}product_categories set name='$this->name',section_id='$this->section_id',created_at='$this->created_at',updated_at='$this->updated_at' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}product_categories where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,section_id,created_at,updated_at from {$tx}product_categories");
		$data=[];
		while($productcategory=$result->fetch_object()){
			$data[]=$productcategory;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,section_id,created_at,updated_at from {$tx}product_categories $criteria limit $top,$perpage");
		$data=[];
		while($productcategory=$result->fetch_object()){
			$data[]=$productcategory;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}product_categories $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,section_id,created_at,updated_at from {$tx}product_categories where id='$id'");
		$productcategory=$result->fetch_object();
			return $productcategory;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}product_categories");
		$productcategory =$result->fetch_object();
		return $productcategory->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Section Id:$this->section_id<br> 
		Created At:$this->created_at<br> 
		Updated At:$this->updated_at<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbProductCategory"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}product_categories");
		while($productcategory=$result->fetch_object()){
			$html.="<option value ='$productcategory->id'>$productcategory->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}product_categories $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select c.id,c.name,s.name section from {$tx}product_categories c,{$tx}product_sections s where s.id=c.section_id limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'>".Html::link(["class"=>"btn btn-success","route"=>"productcategory/create","text"=>"New ProductCategory"])."</th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Section</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Section</th></tr>";
		}
		while($productcategory=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"productcategory/show/$productcategory->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"productcategory/edit/$productcategory->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"productcategory/confirm/$productcategory->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$productcategory->id</td><td>$productcategory->name</td><td>$productcategory->section</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,name,section_id,created_at,updated_at from {$tx}product_categories where id={$id}");
		$productcategory=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">ProductCategory Show</th></tr>";
		$html.="<tr><th>Id</th><td>$productcategory->id</td></tr>";
		$html.="<tr><th>Name</th><td>$productcategory->name</td></tr>";
		$html.="<tr><th>Section Id</th><td>$productcategory->section_id</td></tr>";
		$html.="<tr><th>Created At</th><td>$productcategory->created_at</td></tr>";
		$html.="<tr><th>Updated At</th><td>$productcategory->updated_at</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
