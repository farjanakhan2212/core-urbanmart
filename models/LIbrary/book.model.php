<?php
class Book implements JsonSerializable{
	public $id;
	public $name;
	public $author;
	public $price;

	public function __construct(){
	}
	public function set($id,$name,$author,$price){
		$this->id=$id;
		$this->name=$name;
		$this->author=$author;
		$this->price=$price;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}books(name,author,price)values('$this->name','$this->author','$this->price')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}books set name='$this->name',author='$this->author',price='$this->price' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}books where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,author,price from {$tx}books");
		$data=[];
		while($book=$result->fetch_object()){
			$data[]=$book;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,author,price from {$tx}books $criteria limit $top,$perpage");
		$data=[];
		while($book=$result->fetch_object()){
			$data[]=$book;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}books $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,author,price from {$tx}books where id='$id'");
		$book=$result->fetch_object();
			return $book;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}books");
		$book =$result->fetch_object();
		return $book->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Author:$this->author<br> 
		Price:$this->price<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbBook"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}books");
		while($book=$result->fetch_object()){
			$html.="<option value ='$book->id'>$book->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx;
		$count_result =$db->query("select count(*) total from {$tx}books $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,author,price from {$tx}books $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'><a class=\"btn btn-success\" href=\"create-book\">New Book</a></th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Author</th><th>Price</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Author</th><th>Price</th></tr>";
		}
		while($book=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='clearfix' style='display:flex;'>";
				$action_buttons.= action_button(["id"=>$book->id, "name"=>"Details", "value"=>"Details", "class"=>"info", "url"=>"details-book"]);
				$action_buttons.= action_button(["id"=>$book->id, "name"=>"Edit", "value"=>"Edit", "class"=>"primary", "url"=>"edit-book"]);
				$action_buttons.= action_button(["id"=>$book->id, "name"=>"Delete", "value"=>"Delete", "class"=>"danger", "url"=>"books"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$book->id</td><td>$book->name</td><td>$book->author</td><td>$book->price</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx;
		$result =$db->query("select id,name,author,price from {$tx}books where id={$id}");
		$book=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Book Details</th></tr>";
		$html.="<tr><th>Id</th><td>$book->id</td></tr>";
		$html.="<tr><th>Name</th><td>$book->name</td></tr>";
		$html.="<tr><th>Author</th><td>$book->author</td></tr>";
		$html.="<tr><th>Price</th><td>$book->price</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
