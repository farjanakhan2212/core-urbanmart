<?php

   //Database connection settings for UrbanMart Grocery.
   //The defaults below are for local XAMPP development
   //(MySQL/MariaDB user "root", empty password, database wdpf63_farjana).
   //
   //Hosting override (gitignored - never commit credentials):
   //   configs/db.hosting.php
   //       $DB_HOST     = "sqlXXX.yourhost.com";
   //       $DB_USER     = "your_db_user";
   //       $DB_PASSWORD = "your_db_password";
   //       $DB_DATABASE = "your_db_name";
   //It is only applied on a real (non-local) host, so production
   //credentials can never break local XAMPP development.
   //
   //configs/db.php is an explicit always-applied override for advanced local use.

   $DB_HOST="localhost";
   $DB_USER="root";
   $DB_PASSWORD="";
   $DB_DATABASE="wdpf63_farjana";

   //Detect a local development host. CLI (no SERVER_NAME) counts as local.
   $__um_host = strtolower(isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"]
                : (isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : ""));
   $__um_is_local = ($__um_host === "")
       || in_array($__um_host, ["localhost", "127.0.0.1", "::1"], true)
       || (substr($__um_host, -6) === ".local")
       || (substr($__um_host, -5) === ".test");

   if (!$__um_is_local && file_exists(__DIR__ . "/db.hosting.php")) {
       include __DIR__ . "/db.hosting.php";
   }
   if (file_exists(__DIR__ . "/db.php")) {
       include __DIR__ . "/db.php";
   }
   unset($__um_host, $__um_is_local);

   //Legacy constants (kept for compatibility with existing modules)
   define("SERVER",$DB_HOST);
   define("USER",$DB_USER);
   define("DATABASE",$DB_DATABASE);
   define("PASSWORD",$DB_PASSWORD);

   //Connect. From PHP 8.1 mysqli throws mysqli_sql_exception on failure, so the
   //connection is wrapped: a private log entry is written and the visitor only
   //sees a generic message (never credentials, error codes or a stack trace).
   $db=null;
   try {
       $db=new mysqli(SERVER,USER,PASSWORD,DATABASE);
       if ($db->connect_error) {
           throw new RuntimeException($db->connect_error);
       }
       $db->set_charset("utf8mb4");
   } catch (\Throwable $e) {
       error_log("UrbanMart DB connection failed: " . $e->getMessage());
       http_response_code(500);
       die("Database connection failed. Please try again later.");
   }
   $tx="core_";

?>
