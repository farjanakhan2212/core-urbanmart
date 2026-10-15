<?php

if(isset($_GET["class"])){
    $class=$_GET["class"]."Api";
      
    if(class_exists($class)){
  
        $method = (isset($_GET["method"]) && $_GET["method"] !== '') ? $_GET["method"] : "index";

        $obj=new $class();     
        $res_method = $_SERVER['REQUEST_METHOD'];
        
  
  
        $params=[];
        if(method_exists($obj,$method)){
         
          if($res_method=="POST"){
            
            if(!empty($_POST)){
              $params=$_POST;            
            }else{
              $jsonRequest=file_get_contents("php://input");
              $params=json_decode($jsonRequest,true);        
              if($params==null) { 
                  http_response_code(400); // Bad Request
                 echo "Invalid JSON data";
              }  
            }  
             
  
}else if($res_method=="GET"){ 
           
            //$query_string=$_SERVER['QUERY_STRING'];
                              
            //-----json---
            $jsonRequest=file_get_contents("php://input");
            $params=json_decode($jsonRequest,true); 

            //--querystring---
            if($params==null){
              $params=$_GET;
              unset($params["class"], $params["method"]);
            }

          }else if($res_method=="PUT"){ 

            //--querystring---
            if($params==null){
              $params=$_GET;
              unset($params["class"], $params["method"]);
            }
            echo "//DBG ".json_encode($_GET)." params=".json_encode($params).PHP_EOL;

          }else if($res_method=="PUT"){
                      
            $jsonRequest=file_get_contents("php://input");
            $params=json_decode($jsonRequest,true);          
            if($params==null) {         
             _parsePut();              
             $params=$_PUT; 
             if($params==null){            
                  http_response_code(400); // Bad Request
                  echo "Invalid JSON data";
              }
             
            }
  
        
  
          }else if($res_method=="DELETE"){
  
              $jsonRequest=file_get_contents("php://input");
              $params=json_decode($jsonRequest,true);          
              if($params==null) { 
                  _parseDelete();        
                  $params=$_DELETE; 
                  if($params==null){            
                      http_response_code(400); // Bad Request
                      echo "Invalid JSON data";
                  }
              } 
  
          }else if($res_method=="PATCH"){
  
              $jsonRequest=file_get_contents("php://input");
              $params=json_decode($jsonRequest,true);          
              if($params==null) { 
                  _parsePatch();           
                  $params=$_PATCH; 
                  if($params==null){            
                      http_response_code(400); // Bad Request
                      echo "Invalid JSON data";
                  }
              } 
              
          }
           if(isset($_FILES)){
            $params["file"]=$_FILES;
           }
           //call_user_func_array($method,[$params,$_FILES]);

           $obj_params=json_decode(json_encode($params),FALSE);
           
           call_user_func_array(array($obj,$method),[$obj_params]);
}else{
          //echo "Method $method is not exists";
          $params = $_GET;
          unset($params["class"], $params["method"]);
          if(isset($_FILES)){
           $params["file"]=$_FILES;
          }

         $obj_params=json_decode(json_encode($params),FALSE);
          
         call_user_func_array(array($obj,"index"),[$obj_params]);
        }
        
  
    }else{
       echo $class." class not exits.";
    }
      
  
  
  }else{
    echo "Default Class";
  }

?>