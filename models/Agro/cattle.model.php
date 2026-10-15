<?php
class Cattle extends Model implements JsonSerializable{
	public $id;
	public $name;
	public $region;
	public $dob;
	public $color;
	public $description;
	public $photo;
	public $gender;
	public $cattle_category_id;

	public function __construct(){
	}
	public function set($id,$name,$region,$dob,$color,$description,$photo,$gender,$cattle_category_id){
		$this->id=$id;
		$this->name=$name;
		$this->region=$region;
		$this->dob=$dob;
		$this->color=$color;
		$this->description=$description;
		$this->photo=$photo;
		$this->gender=$gender;
		$this->cattle_category_id=$cattle_category_id;

	}
	public function save(){
		global $db,$tx;
		$db->query("insert into {$tx}cattles(name,region,dob,color,description,photo,gender,cattle_category_id)values('$this->name','$this->region','$this->dob','$this->color','$this->description','$this->photo','$this->gender','$this->cattle_category_id')");
		return $db->insert_id;
	}
	public function update(){
		global $db,$tx;
		$db->query("update {$tx}cattles set name='$this->name',region='$this->region',dob='$this->dob',color='$this->color',description='$this->description',photo='$this->photo',gender='$this->gender',cattle_category_id='$this->cattle_category_id' where id='$this->id'");
	}
	public static function delete($id){
		global $db,$tx;
		$db->query("delete from {$tx}cattles where id={$id}");
	}
	public function jsonSerialize(): mixed {
		return get_object_vars($this);
	}
	public static function all(){
		global $db,$tx;
		$result=$db->query("select id,name,region,dob,color,description,photo,gender,cattle_category_id from {$tx}cattles");
		$data=[];
		while($cattle=$result->fetch_object()){
			$data[]=$cattle;
		}
			return $data;
	}
	public static function pagination($page=1,$perpage=10,$criteria=""){
		global $db,$tx;
		$top=($page-1)*$perpage;
		$result=$db->query("select id,name,region,dob,color,description,photo,gender,cattle_category_id from {$tx}cattles $criteria limit $top,$perpage");
		$data=[];
		while($cattle=$result->fetch_object()){
			$data[]=$cattle;
		}
			return $data;
	}
	public static function count($criteria=""){
		global $db,$tx;
		$result =$db->query("select count(*) from {$tx}cattles $criteria");
		list($count)=$result->fetch_row();
			return $count;
	}
	public static function find($id){
		global $db,$tx;
		$result =$db->query("select id,name,region,dob,color,description,photo,gender,cattle_category_id from {$tx}cattles where id='$id'");
		$cattle=$result->fetch_object();
			return $cattle;
	}
	static function get_last_id(){
		global $db,$tx;
		$result =$db->query("select max(id) last_id from {$tx}cattles");
		$cattle =$result->fetch_object();
		return $cattle->last_id;
	}
	public function json(){
		return json_encode($this);
	}
	public function __toString(){
		return "		Id:$this->id<br> 
		Name:$this->name<br> 
		Region:$this->region<br> 
		Dob:$this->dob<br> 
		Color:$this->color<br> 
		Description:$this->description<br> 
		Photo:$this->photo<br> 
		Gender:$this->gender<br> 
		Cattle Category Id:$this->cattle_category_id<br> 
";
	}

	//-------------HTML----------//

	static function html_select($name="cmbCattle"){
		global $db,$tx;
		$html="<select id='$name' name='$name'> ";
		$result =$db->query("select id,name from {$tx}cattles");
		while($cattle=$result->fetch_object()){
			$html.="<option value ='$cattle->id'>$cattle->name</option>";
		}
		$html.="</select>";
		return $html;
	}
	static function html_table($page = 1,$perpage = 10,$criteria="",$action=true){
		global $db,$tx,$base_url;
		$count_result =$db->query("select count(*) total from {$tx}cattles $criteria ");
		list($total_rows)=$count_result->fetch_row();
		$total_pages = ceil($total_rows /$perpage);
		$top = ($page - 1)*$perpage;
		$result=$db->query("select id,name,region,dob,color,description,photo,gender,cattle_category_id from {$tx}cattles $criteria limit $top,$perpage");
		$html="<table class='table'>";
			$html.="<tr><th colspan='3'>".Html::link(["class"=>"btn btn-success","route"=>"cattle/create","text"=>"New Cattle"])."</th></tr>";
		if($action){
			$html.="<tr><th>Id</th><th>Name</th><th>Region</th><th>Dob</th><th>Color</th><th>Description</th><th>Photo</th><th>Gender</th><th>Cattle Category Id</th><th>Action</th></tr>";
		}else{
			$html.="<tr><th>Id</th><th>Name</th><th>Region</th><th>Dob</th><th>Color</th><th>Description</th><th>Photo</th><th>Gender</th><th>Cattle Category Id</th></tr>";
		}
		while($cattle=$result->fetch_object()){
			$action_buttons = "";
			if($action){
				$action_buttons = "<td><div class='btn-group' style='display:flex;'>";
				$action_buttons.= Event::button(["name"=>"show", "value"=>"Show", "class"=>"btn btn-info", "route"=>"cattle/show/$cattle->id"]);
				$action_buttons.= Event::button(["name"=>"edit", "value"=>"Edit", "class"=>"btn btn-primary", "route"=>"cattle/edit/$cattle->id"]);
				$action_buttons.= Event::button(["name"=>"delete", "value"=>"Delete", "class"=>"btn btn-danger", "route"=>"cattle/confirm/$cattle->id"]);
				$action_buttons.= "</div></td>";
			}
			$html.="<tr><td>$cattle->id</td><td>$cattle->name</td><td>$cattle->region</td><td>$cattle->dob</td><td>$cattle->color</td><td>$cattle->description</td><td><img src='$base_url/img/$cattle->photo' width='100' /></td><td>$cattle->gender</td><td>$cattle->cattle_category_id</td> $action_buttons</tr>";
		}
		$html.="</table>";
		$html.= pagination($page,$total_pages);
		return $html;
	}
	static function html_row_details($id){
		global $db,$tx,$base_url;
		$result =$db->query("select id,name,region,dob,color,description,photo,gender,cattle_category_id from {$tx}cattles where id={$id}");
		$cattle=$result->fetch_object();
		$html="<table class='table'>";
		$html.="<tr><th colspan=\"2\">Cattle Show</th></tr>";
		$html.="<tr><th>Id</th><td>$cattle->id</td></tr>";
		$html.="<tr><th>Name</th><td>$cattle->name</td></tr>";
		$html.="<tr><th>Region</th><td>$cattle->region</td></tr>";
		$html.="<tr><th>Dob</th><td>$cattle->dob</td></tr>";
		$html.="<tr><th>Color</th><td>$cattle->color</td></tr>";
		$html.="<tr><th>Description</th><td>$cattle->description</td></tr>";
		$html.="<tr><th>Photo</th><td><img src='$base_url/img/$cattle->photo' width='100' /></td></tr>";
		$html.="<tr><th>Gender</th><td>$cattle->gender</td></tr>";
		$html.="<tr><th>Cattle Category Id</th><td>$cattle->cattle_category_id</td></tr>";

		$html.="</table>";
		return $html;
	}
}
?>
