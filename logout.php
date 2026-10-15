<?php session_start();

require_once("configs/app.config.php");


 unset($_SESSION["uid"]);
 unset($_SESSION["uname"]);
 unset($_SESSION["uphoto"]);
 unset($_SESSION["email"]);
 unset($_SESSION["mobile"]);
 unset($_SESSION["role_id"]);
 unset($_SESSION["urole"]);

 setcookie("um_remember", "", time()-3600, "/");

 //Expire the session cookie in the browser as well.
 if (ini_get("session.use_cookies")) {
     $params = session_get_cookie_params();
     setcookie(session_name(), "", time()-3600, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
 }

 session_destroy();

 //Stop the browser from serving a cached authenticated page on Back.
 header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
 header("Pragma: no-cache");
 header("Expires: 0");

 header("location:$base_url");
 exit;