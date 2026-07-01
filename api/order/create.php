<?php

include("../../config/database.php");
include("../../includes/auth.php");

if($_SERVER["REQUEST_METHOD"]!="POST"){
    exit("Sai phương thức");
}

$product_id=(int)$_POST["product_id"];

$buyer=$_SESSION["user_id"];