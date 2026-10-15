<?php
class UserApi extends Api{
	public function __construct(){
		if(!$this->authorized()){          
			http_response_code(401);//Unauthorized
			 die("401 Unauthorized");
		}
	}
	function index(){
		echo json_encode(["users"=>User::all()]);
	}
	function pagination($data){
		$page=$data->page;
		$perpage=$data->perpage;
		echo json_encode(["users"=>User::pagination($page,$perpage),"total_records"=>User::count()]);
	}
	function search($data){
		echo json_encode(["users"=>User::search($data->name)]);
	}
	function find($data){
		echo json_encode(["user"=>User::find($data->id)]);
	}
	function delete($data){
		User::delete($data->id);
		echo json_encode(["success" => "yes"]);
	}
	function save($data,$file=[]){
		global $now;
		$user=new User();
		$user->name=$data->name;
		$user->email=$data->email;
		$user->branch=isset($data->branch)?$data->branch:null;
		$user->password=password_hash($data->password,PASSWORD_BCRYPT);
		$user->created_at=$now;
		$user->updated_at=$now;

		$user->save();
		echo json_encode(["success" => "yes"]);
	}
	function update($data,$file=[]){
		global $now;
		$user=new User();
		$user->id=$data->id;
		$user->name=$data->name;
		$user->email=$data->email;
		if(isset($data->password) && $data->password !== ''){
			$user->password=password_hash($data->password,PASSWORD_BCRYPT);
		}else{
			$existing=User::find($data->id);
			$user->password=$existing?$existing->password:'';
		}
		$user->updated_at=$now;

		$user->update();
		echo json_encode(["success" => "yes"]);
	}
}
?>