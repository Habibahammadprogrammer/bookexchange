<?php
require_once "includes/config.php";
echo " Database  $dbname connected successfully!";
session_start();
if(!isset($_SESSION["user_id"])){
    header("Location:login.php");
    exit();
}

?>
