<?php ob_start();
require_once("core.php"); 

if(!isset($_SESSION["uid"])) { header("location:" . $base_url . "/"); exit; }
   $uid=$_SESSION["uid"]; 

//Authenticated pages must never be cached (protects against Back navigation after logout).
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
?>

<?php section("master.php"); ?>
 

 
