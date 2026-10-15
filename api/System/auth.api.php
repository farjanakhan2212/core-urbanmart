<?php

class AuthApi extends Api{

    function __construct(){       
        
      
    }
  //----Mange-----
    function index(){  
              
      //echo "Index";
     // view("system");
    }
        
   
    function valid($data){

        $jwt=new JWT();
        if($jwt->is_valid($data->token)){
            echo "valid";
        }else{
            echo "invalid";
        }
    }

    function login($data){

        global $db;
        global $tx;

            $username=trim(isset($data->username) ? (string)$data->username : '');
            $password=isset($data->password) ? (string)$data->password : '';

            if ($username === '' || $password === '') {
                http_response_code(400);
                echo json_encode(["success"=>0,"message"=>"Username and password are required"]);
                return;
            }

            $stmt=$db->prepare("select u.id,u.name,u.email,u.password from {$tx}users u where (u.name=? OR u.email=?)");
            if (!$stmt) {
                echo json_encode(["success"=>0,"message"=>"Invalid username/email or password"]);
                return;
            }
            $stmt->bind_param("ss", $username, $username);
            $stmt->execute();
            $user=$stmt->get_result()->fetch_object();
            $stmt->close();

            if($user && password_verify($password,$user->password)){

                        $jwt=new JWT();

                        $jwt->min=5;

                        $payload=[
                            "id"=>$user->id,
                            "name"=>$user->name,
                            "email"=>$user->email,
                            "ip"=>get_ip(),
                            "iss"=>"jwt.server",
                            "aud"=>"intels.co"
                        ];

                        $token= $jwt->generate($payload);
                        
                        echo json_encode(["success"=>1,"token"=>$token]);
                }else{
                        echo json_encode(["success"=>0,"message"=>"Invalid username/email or password"]);
                }     

        

    }


}