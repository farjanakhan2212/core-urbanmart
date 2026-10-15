<?php
class Product extends Model implements JsonSerializable{
	public $id;
	public $name;
	public $offer_price;
	public $manufacturer_id;
	public $regular_price;
	public $description;
	public $photo;
	public $product_category_id;
	public $product_section_id;
	public $is_featured;
	public $star;
	public $is_brand;
	public $offer_discount;
	public $uom_id;
	public $weight;
	public $barcode;
	public $created_at;
	public $updated_at;
	public $product_type_id;
	public $product_unit_id;

	public function __construct(){
	}
	public function set($id,$name,$offer_price,$manufacturer_id,$regular_price,$description,$photo,$product_category_id,$product_section_id,$is_featured,$star,$is_brand,$offer_discount,$uom_id,$weight,$barcode,$created_at,$updated_at,$product_type_id,$product_unit_id){
		$this->id=$id;
		$this->name=$name;
		$this->offer_price=$offer_price;
		$this->manufacturer_id=$manufacturer_id;
		$this->regular_price=$regular_price;
		$this->description=$description;
		$this->photo=$photo;
		$this->product_category_id=$product_category_id;
		$this->product_section_id=$product_section_id;
		$this->is_featured=$is_featured;
		$this->star=$star;
		$this->is_brand=$is_brand;
		$this->offer_discount=$offer_discount;
		$this->uom_id=$uom_id;
		$this->weight=$weight;
		$this->barcode=$barcode;
		$this->created_at=$created_at;
		$this->updated_at=$updated_at;
		$this->product_type_id=$product_type_id;
		$this->product_unit_id=$product_unit_id;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}products(name,offer_price,manufacturer_id,regular_price,description,photo,product_category_id,product_section_id,is_featured,star,is_brand,offer_discount,uom_id,weight,barcode,created_at,updated_at,product_type_id,product_unit_id)values('$this->name','$this->offer_price','$this->manufacturer_id','$this->regular_price','$this->description','$this->photo','$this->product_category_id','$this->product_section_id','$this->is_featured','$this->star','$this->is_brand','$this->offer_discount','$this->uom_id','$this->weight','$this->barcode','$this->created_at','$this->updated_at','$this->product_type_id','$this->product_unit_id')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}products set name='$this->name',offer_price='$this->offer_price',manufacturer_id='$this->manufacturer_id',regular_price='$this->regular_price',description='$this->description',photo='$this->photo',product_category_id='$this->product_category_id',product_section_id='$this->product_section_id',is_featured='$this->is_featured',star='$this->star',is_brand='$this->is_brand',offer_discount='$this->offer_discount',uom_id='$this->uom_id',weight='$this->weight',barcode='$this->barcode',created_at='$this->created_at',updated_at='$this->updated_at',product_type_id='$this->product_type_id',product_unit_id='$this->product_unit_id' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}products where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,offer_price,manufacturer_id,regular_price,description,photo,product_category_id,product_section_id,is_featured,star,is_brand,offer_discount,uom_id,weight,barcode,created_at,updated_at,product_type_id,product_unit_id from {$tx}products");
		$data=[];
		while($product=$result->fetch_object()){
			$data[]=$product;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,offer_price,manufacturer_id,regular_price,description,photo,product_category_id,product_section_id,is_featured,star,is_brand,offer_discount,uom_id,weight,barcode,created_at,updated_at,product_type_id,product_unit_id from {$tx}products $criteria limit $top,$perpage");
		$data=[];
		while($product=$result->fetch_object()){
			$data[]=$product;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}products $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,offer_price,manufacturer_id,regular_price,description,photo,product_category_id,product_section_id,is_featured,star,is_brand,offer_discount,uom_id,weight,barcode,created_at,updated_at,product_type_id,product_unit_id from {$tx}products where id='$id'");
		$product=$result->fetch_object();
			return $product;
	}
	public static function filter($name){
		global $db,$tx;
		//Update field name after where keyword if don't have name field
		$result=$db->query("select id,name,offer_price,manufacturer_id,regular_price,description,photo,product_category_id,product_section_id,is_featured,star,is_brand,offer_discount,uom_id,weight,barcode,created_at,updated_at,product_type_id,product_unit_id from {$tx}products where name like '%$name%'");
		$data=[];
		while($product=$result->fetch_object()){
			$data[]=$product;
		}
			return $data;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}products");
		$product =$result->fetch_object();
		return $product->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Offer Price:$this->offer_price<br> 
		Manufacturer Id:$this->manufacturer_id<br> 
		Regular Price:$this->regular_price<br> 
		Description:$this->description<br> 
		Photo:$this->photo<br> 
		Product Category Id:$this->product_category_id<br> 
		Product Section Id:$this->product_section_id<br> 
		Is Featured:$this->is_featured<br> 
		Star:$this->star<br> 
		Is Brand:$this->is_brand<br> 
		Offer Discount:$this->offer_discount<br> 
		Uom Id:$this->uom_id<br> 
		Weight:$this->weight<br> 
		Barcode:$this->barcode<br> 
		Created At:$this->created_at<br> 
		Updated At:$this->updated_at<br> 
		Product Type Id:$this->product_type_id<br> 
		Product Unit Id:$this->product_unit_id<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbProduct"){
		global $db,$tx;
		$html="<select id='$name' name='$name' class='form-control form-select'>";
		$result =$db->query("select id,name from {$tx}products");
		while($product=$result->fetch_object()){
			$html.="<option value ='$product->id'>$product->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}products $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,offer_price,manufacturer_id,regular_price,description,photo,product_category_id,product_section_id,is_featured,star,is_brand,offer_discount,uom_id,weight,barcode,created_at,updated_at,product_type_id,product_unit_id from {$tx}products $criteria limit $top,$perpage");
		$html="<table class='table'>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Offer Price</th><th>Manufacturer Id</th><th>Regular Price</th><th>Description</th><th>Photo</th><th>Product Category Id</th><th>Product Section Id</th><th>Is Featured</th><th>Star</th><th>Is Brand</th><th>Offer Discount</th><th>Uom Id</th><th>Weight</th><th>Barcode</th><th>Created At</th><th>Updated At</th><th>Product Type Id</th><th>Product Unit Id</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Offer Price</th><th>Manufacturer Id</th><th>Regular Price</th><th>Description</th><th>Photo</th><th>Product Category Id</th><th>Product Section Id</th><th>Is Featured</th><th>Star</th><th>Is Brand</th><th>Offer Discount</th><th>Uom Id</th><th>Weight</th><th>Barcode</th><th>Created At</th><th>Updated At</th><th>Product Type Id</th><th>Product Unit Id</th></tr>";
		}
		while($product=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"product/show/$product->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"product/edit/$product->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"product/confirm/$product->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$product->id</td><td>$product->name</td><td>$product->offer_price</td><td>$product->manufacturer_id</td><td>$product->regular_price</td><td>$product->description</td><td><img src='$base_url/img/$product->photo' width='100' /></td><td>$product->product_category_id</td><td>$product->product_section_id</td><td>$product->is_featured</td><td>$product->star</td><td>$product->is_brand</td><td>$product->offer_discount</td><td>$product->uom_id</td><td>$product->weight</td><td>$product->barcode</td><td>$product->created_at</td><td>$product->updated_at</td><td>$product->product_type_id</td><td>$product->product_unit_id</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,name,offer_price,manufacturer_id,regular_price,description,photo,product_category_id,product_section_id,is_featured,star,is_brand,offer_discount,uom_id,weight,barcode,created_at,updated_at,product_type_id,product_unit_id from {$tx}products where id={$id}");
		$product=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Product Show</th></tr>";
		$html.="<tr><th>Id</th><td>$product->id</td></tr>";
		$html.="<tr><th>Name</th><td>$product->name</td></tr>";
		$html.="<tr><th>Offer Price</th><td>$product->offer_price</td></tr>";
		$html.="<tr><th>Manufacturer Id</th><td>$product->manufacturer_id</td></tr>";
		$html.="<tr><th>Regular Price</th><td>$product->regular_price</td></tr>";
		$html.="<tr><th>Description</th><td>$product->description</td></tr>";
		$html.="<tr><th>Photo</th><td><img src='$base_url/img/$product->photo' width='100' /></td></tr>";
		$html.="<tr><th>Product Category Id</th><td>$product->product_category_id</td></tr>";
		$html.="<tr><th>Product Section Id</th><td>$product->product_section_id</td></tr>";
		$html.="<tr><th>Is Featured</th><td>$product->is_featured</td></tr>";
		$html.="<tr><th>Star</th><td>$product->star</td></tr>";
		$html.="<tr><th>Is Brand</th><td>$product->is_brand</td></tr>";
		$html.="<tr><th>Offer Discount</th><td>$product->offer_discount</td></tr>";
		$html.="<tr><th>Uom Id</th><td>$product->uom_id</td></tr>";
		$html.="<tr><th>Weight</th><td>$product->weight</td></tr>";
		$html.="<tr><th>Barcode</th><td>$product->barcode</td></tr>";
		$html.="<tr><th>Created At</th><td>$product->created_at</td></tr>";
		$html.="<tr><th>Updated At</th><td>$product->updated_at</td></tr>";
		$html.="<tr><th>Product Type Id</th><td>$product->product_type_id</td></tr>";
		$html.="<tr><th>Product Unit Id</th><td>$product->product_unit_id</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
