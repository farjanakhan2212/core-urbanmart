<?php
class Form{

  public static function path(){
    global $template;
    return $path="views/components/$template/Form";
  }

   public static function open($arg){
    global $base_url;
      $arg["route"]=isset($arg["route"])?$arg["route"]:"";          
     
      include(Form::path()."/open.php");     
    }
    
    public static function close(){      
        include(Form::path()."/close.php");        
    }
    
   
   public static function select($arg){
     global $db,$tx, $base_url;
    
     $arg["value"]=isset($arg["value"])?$arg["value"]:""; 
     $id=isset($arg["value_column"])?$arg["value_column"]:"id";  
     $name=isset($arg["display_column"])?$arg["display_column"]:"name";  
  
     $ucname=ucfirst($arg["name"]);
     //echo "select $id,$name from {$tx}{$config["table"]}";
     
     
     include(Form::path()."/select.php");
   
    
   
   }

 
     
  public static function button($arg){
          $arg["class"]=!isset($arg["class"])?"btn btn-info":$arg["class"];
         
          include(Form::path()."/button.php");
       
  }
    
       public static function input($arg){    
        global $db,$tx, $base_url;
        $id=isset($arg["value_column"])?$arg["value_column"]:"id";  
        $name=isset($arg["display_column"])?$arg["display_column"]:"name";  
     
        $arg["required"]=isset($arg["required"])?$arg["required"]:"";
        $arg["placeholder"]=isset($arg["placeholder"])?$arg["placeholder"]:"";
        $arg["value"]=isset($arg["value"])?$arg["value"]:"";     
        $arg["type"]=isset($arg["type"])?$arg["type"]:"text"; 
        $arg["checked"]=isset($arg["checked"])?$arg["checked"]:""; 
    
        $arg["class"]=isset($arg["class"])?$arg["class"]:"form-control";
        $css_class=$arg["class"]; 
    
        include(Form::path()."/input.php");
       
      
       }
    
      public static function textarea($arg){
        global $base_url;
        $arg["required"]=isset($arg["required"])?$arg["required"]:"";
        $arg["placeholder"]=isset($arg["placeholder"])?$arg["placeholder"]:"";
        $arg["value"]=isset($arg["value"])?$arg["value"]:""; 
       
        
        include(Form::path()."/textarea.php");
    
      
    
     }
    
     public static function text($arg){
      $arg["required"]=isset($arg["required"])?$arg["required"]:"";
      $arg["placeholder"]=isset($arg["placeholder"])?$arg["placeholder"]:"";
      $arg["value"]=isset($arg["value"])?$arg["value"]:"";  
      $arg["checked"]=isset($arg["checked"])?$arg["checked"]:""; 
      $arg["class"]=isset($arg["class"])?$arg["class"]:"form-control"; 

     
       include(Form::path()."/text.php");
     }

     public static function password($arg){
      $arg["required"]=isset($arg["required"])?$arg["required"]:"";
      $arg["placeholder"]=isset($arg["placeholder"])?$arg["placeholder"]:"";
      $arg["value"]=isset($arg["value"])?$arg["value"]:"";     
      $arg["checked"]=isset($arg["checked"])?$arg["checked"]:""; 
      $arg["class"]=isset($arg["class"])?$arg["class"]:"form-control"; 
       include(Form::path()."/password.php");
     }

     public static function radio($arg){
      $arg["required"]=isset($arg["required"])?$arg["required"]:"";
      $arg["placeholder"]=isset($arg["placeholder"])?$arg["placeholder"]:"";
      $arg["value"]=isset($arg["value"])?$arg["value"]:"";     
      $arg["checked"]=isset($arg["checked"])?$arg["checked"]:""; 
      $arg["class"]=isset($arg["class"])?$arg["class"]:"form-control"; 
       include(Form::path()."/radio.php");
     }

     public static function file($arg){
      global $base_url;
      $arg["required"]=isset($arg["required"])?$arg["required"]:"";
      $arg["placeholder"]=isset($arg["placeholder"])?$arg["placeholder"]:"";
      $arg["value"]=isset($arg["value"])?$arg["value"]:"";     
      $arg["checked"]=isset($arg["checked"])?$arg["checked"]:""; 
      $arg["class"]=isset($arg["class"])?$arg["class"]:"form-control"; 
       include(Form::path()."/file.php");
     }


     public static function checkbox($arg){
      $arg["required"]=isset($arg["required"])?$arg["required"]:"";
      $arg["placeholder"]=isset($arg["placeholder"])?$arg["placeholder"]:"";
      $arg["value"]=isset($arg["value"])?$arg["value"]:"";     
      $arg["checked"]=isset($arg["checked"])?$arg["checked"]:""; 
      $arg["class"]=isset($arg["class"])?$arg["class"]:"form-control"; 
       include(Form::path()."/checkbox.php");
     }

     public static function date($arg){
      $arg["required"]=isset($arg["required"])?$arg["required"]:"";
      $arg["placeholder"]=isset($arg["placeholder"])?$arg["placeholder"]:"";
      $arg["value"]=isset($arg["value"])?$arg["value"]:"";     
      $arg["checked"]=isset($arg["checked"])?$arg["checked"]:""; 
      $arg["class"]=isset($arg["class"])?$arg["class"]:"form-control"; 
       include(Form::path()."/date.php");
     }

     public static function datetime($arg){
      $arg["required"]=isset($arg["required"])?$arg["required"]:"";
      $arg["placeholder"]=isset($arg["placeholder"])?$arg["placeholder"]:"";
      $arg["value"]=isset($arg["value"])?$arg["value"]:"";     
      $arg["checked"]=isset($arg["checked"])?$arg["checked"]:""; 
      $arg["class"]=isset($arg["class"])?$arg["class"]:"form-control"; 
       include(Form::path()."/datetime.php");
     }

     public static function time($arg){
      $arg["required"]=isset($arg["required"])?$arg["required"]:"";
      $arg["placeholder"]=isset($arg["placeholder"])?$arg["placeholder"]:"";
      $arg["value"]=isset($arg["value"])?$arg["value"]:"";     
      $arg["checked"]=isset($arg["checked"])?$arg["checked"]:""; 
      $arg["class"]=isset($arg["class"])?$arg["class"]:"form-control"; 
       include(Form::path()."/time.php");
     }


     public static function submit($arg){
      $arg["required"]=isset($arg["required"])?$arg["required"]:"";
      $arg["placeholder"]=isset($arg["placeholder"])?$arg["placeholder"]:"";
      $arg["value"]=isset($arg["value"])?$arg["value"]:"";     
      $arg["checked"]=isset($arg["checked"])?$arg["checked"]:""; 
      $arg["class"]=isset($arg["class"])?$arg["class"]:"form-control"; 
       include(Form::path()."/submit.php");
     }

     public static function reset($arg){
      $arg["required"]=isset($arg["required"])?$arg["required"]:"";
      $arg["placeholder"]=isset($arg["placeholder"])?$arg["placeholder"]:"";
      $arg["value"]=isset($arg["value"])?$arg["value"]:"";     
      $arg["checked"]=isset($arg["checked"])?$arg["checked"]:""; 
      $arg["class"]=isset($arg["class"])?$arg["class"]:"form-control"; 
       include(Form::path()."/reset.php");
     }

     public static function month($arg){
      $arg["required"]=isset($arg["required"])?$arg["required"]:"";
      $arg["placeholder"]=isset($arg["placeholder"])?$arg["placeholder"]:"";
      $arg["value"]=isset($arg["value"])?$arg["value"]:"";     
      $arg["checked"]=isset($arg["checked"])?$arg["checked"]:""; 
      $arg["class"]=isset($arg["class"])?$arg["class"]:"form-control"; 
       include(Form::path()."/month.php");
     }

     public static function color($arg){
      $arg["required"]=isset($arg["required"])?$arg["required"]:"";
      $arg["placeholder"]=isset($arg["placeholder"])?$arg["placeholder"]:"";
      $arg["value"]=isset($arg["value"])?$arg["value"]:"";     
      $arg["checked"]=isset($arg["checked"])?$arg["checked"]:""; 
      $arg["class"]=isset($arg["class"])?$arg["class"]:"form-control"; 
       include(Form::path()."/color.php");
     }
    
     public static function number($arg){
      $arg["required"]=isset($arg["required"])?$arg["required"]:"";
      $arg["placeholder"]=isset($arg["placeholder"])?$arg["placeholder"]:"";
      $arg["value"]=isset($arg["value"])?$arg["value"]:"";     
      $arg["checked"]=isset($arg["checked"])?$arg["checked"]:""; 
      $arg["class"]=isset($arg["class"])?$arg["class"]:"form-control"; 
       include(Form::path()."/number.php");
     }

     public static function email($arg){
      $arg["required"]=isset($arg["required"])?$arg["required"]:"";
      $arg["placeholder"]=isset($arg["placeholder"])?$arg["placeholder"]:"";
      $arg["value"]=isset($arg["value"])?$arg["value"]:"";     
      $arg["checked"]=isset($arg["checked"])?$arg["checked"]:""; 
      $arg["class"]=isset($arg["class"])?$arg["class"]:"form-control"; 
       include(Form::path()."/email.php");
     }
       
       public static function PrintDate($day="cmbDay",$month="cmbMonth",$year="cmbYear"){
    
        $html="";
        $html.="<select name='$day'>";
        for($d=1;$d<=30;$d++){
            $d=str_pad($d,2, '0', STR_PAD_LEFT); 
    
            if($d==str_pad(date("d"),2,'0',STR_PAD_LEFT)){
              $html.="<option value='$d' selected>$d</option>";
            }else{
              $html.="<option value='$d'>$d</option>";
            }
            
     
        }
        $html.="</select>";
     
        $html.="<select name='$month'>";
         $months=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
        for($d=1;$d<=12;$d++){
            $d=str_pad($d,2,'0',STR_PAD_LEFT); 
            if($d==str_pad(date("m"),2,'0',STR_PAD_LEFT)){
              $html.="<option value='$d' selected>{$months[$d-1]}</option>";
            }else{
              $html.="<option value='$d'>{$months[$d-1]}</option>";
            }
     
        }
        $html.="</select>";
        $html.="<select name='$year'>";
        
       for($d=date("Y")-60;$d<=date("Y")+3;$d++){    
    
           if(date("Y")==$d){
             $html.="<option value='$d' selected>$d</option>";
           }else{
             $html.="<option value='$d'>$d</option>";
           }
    
       }
       $html.="</select>";
        return $html;
     }
     
     public static function PrintTime($hour="cmbHour",$min="cmbMin",$ampm="cmbAMPM"){
     
        $html="<select name='$hour'>";
        for($h=1;$h<=12;$h++){
           $h=str_pad($h,2, '0', STR_PAD_LEFT); 
           $html.="<option value='$h'>$h</option>";
        }
        $html.="</select>";
     
        $html.="<select name='$min'>";
        for($h=1;$h<=60;$h++){
            $h=str_pad($h,2, '0', STR_PAD_LEFT); 
           $html.="<option value='$h'>$h</option>";
        }
        $html.="</select>";
     
        $html.="<select name='$ampm'>";
        $html.="<option value='AM'>AM</option>";
        $html.="<option value='PM'>PM</option>";
        $html.="</select>";
       
         return $html;
     
     
     }
    
}

?>