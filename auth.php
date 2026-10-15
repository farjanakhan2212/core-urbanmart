<?php
  require_once("configs/db.config.php");
  require_once("configs/app.config.php");

  //Ensure a CSRF token exists for the login form.
  if (empty($_SESSION["csrf_token"])) {
      $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
  }

  //Auto-login via a valid "Remember Me" cookie when not already logged in
  //and no login form was submitted.
  if (empty($_SESSION["uid"]) && $_SERVER["REQUEST_METHOD"] !== "POST" && isset($_COOKIE["um_remember"])) {
      $parts = explode(":", $_COOKIE["um_remember"], 2);
      if (count($parts) === 2) {
          list($rid, $rtoken) = $parts;
          $stmt = $db->prepare("select u.id,u.name,u.password,u.email from {$tx}users u where u.id=? and u.remember_token=?");
          $stmt->bind_param("is", $rid, $rtoken);
          $stmt->execute();
          $user = $stmt->get_result()->fetch_object();
          $stmt->close();
          if ($user) {
              session_regenerate_id(true);
              $_SESSION["uid"]=$user->id;
              $_SESSION["uname"]=$user->name;
              $_SESSION["uphoto"]="";
              $_SESSION["email"]=$user->email;
              $_SESSION["mobile"]="";
              $_SESSION["role_id"]="";
              $_SESSION["urole"]="";
              $_SESSION["csrf_token"]=bin2hex(random_bytes(32));
              header("location:" . $base_url . "/home");
              exit;
          }
      }
  }

  //Login form handling. Gated on the POST method (not on the submit button
  //name) so authentication still runs even if a browser omits the submitter's
  //name/value from the posted data.
  if ($_SERVER["REQUEST_METHOD"] === "POST") {

     //Validate CSRF token (active login attempt).
     $submittedToken = isset($_POST["_token"]) ? (string)$_POST["_token"] : '';
     if ($submittedToken === '' || !hash_equals($_SESSION["csrf_token"], $submittedToken)) {
        $_SESSION["flash"]["error"]="Your session expired. Please refresh the page and try again.";
        header("location:" . $base_url);
        exit;
     }

     $username=trim($_POST["username"]);
     $password=trim($_POST["password"]);
     if ($username === '' || $password === '') {
        $_SESSION["login_old_username"]=$username;
        $_SESSION["flash"]["error"]="Username and password are required";
        header("location:" . $base_url);
        exit;
     }
     //Login is accepted with either the username (name) or the email column.
     $stmt=$db->prepare("select u.id,u.name,u.password,u.email from {$tx}users u where (u.name=? OR u.email=?)");
     $stmt->bind_param("ss",$username,$username);
     $stmt->execute();
     $user=$stmt->get_result()->fetch_object();
     $stmt->close();

      if($user && password_verify($password,$user->password)){

        session_regenerate_id(true);
        $_SESSION["uid"]=$user->id;
        $_SESSION["uname"]=$user->name;
        $_SESSION["uphoto"]="";
        $_SESSION["email"]=$user->email;
        $_SESSION["mobile"]="";
        $_SESSION["role_id"]="";
        $_SESSION["urole"]="";
        $_SESSION["csrf_token"]=bin2hex(random_bytes(32));

        //Persistent "Remember Me" login using the existing remember_token column.
        $remember = isset($_POST["remember_me"]) && (int)$_POST["remember_me"] === 1;
        setcookie("um_remember", '', time()-3600, "/");
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $upd = $db->prepare("update {$tx}users set remember_token=? where id=?");
            $upd->bind_param("si", $token, $user->id);
            $upd->execute();
            $upd->close();
            setcookie("um_remember", $user->id . ":" . $token, time()+ (86400*30), "/", "", false, true);
        }

        header("location:" . $base_url . "/home");
        exit;
      }else{
        $_SESSION["login_old_username"]=$username;
        $_SESSION["flash"]["error"]="Incorrect username or password";
        header("location:" . $base_url);
        exit;
      }

  }

?>
