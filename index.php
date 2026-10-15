<?php require_once("core.php");

if (isset($_SESSION["uid"])) {
    header("location:" . $base_url . "/home");
    exit;
}

include("auth.php");
section("public/public_master.php");
?>