<?php
class CompanyController extends Controller{
	public function __construct(){
		$this->module="Company";
	}
	public function index(){
		$this->view();
	}
	public function create(){
		$this->view();
	}
public function save($data,$file){
	global $now;
	if(isset($data->create)){
	$errors=[];
/*
	if(!preg_match("/^[\s\S]+$/",$_POST["txtName"])){
		$errors["name"]="Invalid name";
	}
	if(!is_valid_mobile($data->mobile)){
		$errors["mobile"]="Invalid mobile";
	}
	if(!preg_match("/^[\s\S]+$/",$_POST["txtBin"])){
		$errors["bin"]="Invalid bin";
	}
	if(!is_valid_email($data->email)){
		$errors["email"]="Invalid email";
	}
	if(!is_valid_website($data->website)){
		$errors["website"]="Invalid website";
	}
	if(!preg_match("/^[\s\S]+$/",$_POST["txtCity"])){
		$errors["city"]="Invalid city";
	}
	if(!preg_match("/^[\s\S]+$/",$_POST["txtArea"])){
		$errors["area"]="Invalid area";
	}
	if(!preg_match("/^[\s\S]+$/",$_POST["txtStreetAddress"])){
		$errors["street_address"]="Invalid street_address";
	}
	if(!preg_match("/^[\s\S]+$/",$_POST["txtPostCode"])){
		$errors["post_code"]="Invalid post_code";
	}
	if(!preg_match("/^[\s\S]+$/",$data->inactive)){
		$errors["inactive"]="Invalid inactive";
	}
	if(!preg_match("/^[\s\S]+$/",$data->logo)){
		$errors["logo"]="Invalid logo";
	}

*/
		if(count($errors)==0){
			$company=new Company();
		$company->name=$data->name;
		$company->mobile=$data->mobile;
		$company->bin=$data->bin;
		$company->email=$data->email;
		$company->website=$data->website;
		$company->city=$data->city;
		$company->area=$data->area;
		$company->street_address=$data->street_address;
		$company->post_code=$data->post_code;
		$company->inactive=isset($data->inactive)?1:0;
		$company->logo=File::upload($file->logo, "img",$data->id);

			$company->save();
		redirect();
		}else{
			 print_r($errors);
		}
	}
}
public function edit($id){
		$record=Company::find($id);
		if(!$record){ redirect(); return; }
		$this->view($record);
	}
public function update($data,$file){
	global $now;
	if(isset($data->update)){
	$errors=[];
/*
	if(!preg_match("/^[\s\S]+$/",$_POST["txtName"])){
		$errors["name"]="Invalid name";
	}
	if(!is_valid_mobile($data->mobile)){
		$errors["mobile"]="Invalid mobile";
	}
	if(!preg_match("/^[\s\S]+$/",$_POST["txtBin"])){
		$errors["bin"]="Invalid bin";
	}
	if(!is_valid_email($data->email)){
		$errors["email"]="Invalid email";
	}
	if(!is_valid_website($data->website)){
		$errors["website"]="Invalid website";
	}
	if(!preg_match("/^[\s\S]+$/",$_POST["txtCity"])){
		$errors["city"]="Invalid city";
	}
	if(!preg_match("/^[\s\S]+$/",$_POST["txtArea"])){
		$errors["area"]="Invalid area";
	}
	if(!preg_match("/^[\s\S]+$/",$_POST["txtStreetAddress"])){
		$errors["street_address"]="Invalid street_address";
	}
	if(!preg_match("/^[\s\S]+$/",$_POST["txtPostCode"])){
		$errors["post_code"]="Invalid post_code";
	}
	if(!preg_match("/^[\s\S]+$/",$data->inactive)){
		$errors["inactive"]="Invalid inactive";
	}
	if(!preg_match("/^[\s\S]+$/",$data->logo)){
		$errors["logo"]="Invalid logo";
	}

*/
		if(count($errors)==0){
			$company=new Company();
			$company->id=$data->id;
		$company->name=$data->name;
		$company->mobile=$data->mobile;
		$company->bin=$data->bin;
		$company->email=$data->email;
		$company->website=$data->website;
		$company->city=$data->city;
		$company->area=$data->area;
		$company->street_address=$data->street_address;
		$company->post_code=$data->post_code;
		$company->inactive=isset($data->inactive)?1:0;
		if($file->logo->name!=""){
			$company->logo=File::upload($file->logo, "img",$data->id);
		}else{
			$company->logo=Company::find($data->id)->logo;
		}

		$company->update();
		redirect();
		}else{
			 print_r($errors);
		}
	}
}
	public function confirm($id){
		$this->view();
	}
	public function delete($id){
		Company::delete($id);
		redirect();
	}
	public function show($id){
		$record=Company::find($id);
		if($record){ $this->view($record); }else{ redirect(); }
	}
}
?>
