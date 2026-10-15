<?php ob_start();
require_once("core.php");

if (isset($_SESSION["uid"])) {
    header("location:" . $base_url . "/home");
    exit;
}

$page_title = "Create Account";

$reg_errors = [];
$reg_old = [];

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION["csrf_token"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $submittedToken = isset($_POST["_token"]) ? (string)$_POST["_token"] : '';
    if ($submittedToken === '' || !hash_equals($_SESSION["csrf_token"], $submittedToken)) {
        $_SESSION["flash"]["error"] = "Your session expired. Please try again.";
        header("location:" . $base_url . "/register.php");
        exit;
    }

    $data = [
        "username"          => isset($_POST["username"]) ? trim($_POST["username"]) : '',
        "email"             => isset($_POST["email"]) ? trim($_POST["email"]) : '',
        "password"          => isset($_POST["password"]) ? $_POST["password"] : '',
        "confirm_password"  => isset($_POST["confirm_password"]) ? $_POST["confirm_password"] : '',
        "terms"             => isset($_POST["terms"]) ? $_POST["terms"] : '',
    ];

    $result = grocery_register_user($data);

    if ($result["success"]) {
        $stmt = $db->prepare("select u.id,u.name,u.password,u.email from {$tx}users u where u.id=?");
        $stmt->bind_param("i", $result["user"]["id"]);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_object();
        $stmt->close();

        if ($user) {
            session_regenerate_id(true);
            $_SESSION["uid"] = $user->id;
            $_SESSION["uname"] = $user->name;
            $_SESSION["uphoto"] = "";
            $_SESSION["email"] = $user->email;
            $_SESSION["mobile"] = "";
            $_SESSION["role_id"] = "";
            $_SESSION["urole"] = "";
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
            header("location:" . $base_url . "/home");
            exit;
        }
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        $_SESSION["flash"]["success"] = $result["message"];
        header("location:" . $base_url . "/index.php");
        exit;
    }

    $reg_errors = $result["errors"];
    $reg_old = [
        "username" => $data["username"],
        "email"     => $data["email"],
    ];
}

section("public/public_register.php");
?>