<?php

session_start();

header("Content-Type: application/json");

require_once "../../config/db.php";

if(!isset($_SESSION["user_id"])){

exit(json_encode([
"success"=>false
]));

}

$user=(int)$_SESSION["user_id"];

$id=(int)($_POST["cart_id"]??0);

$qty=(int)($_POST["quantity"]??1);

if($qty<1){

$qty=1;

}

$stmt=$conn->prepare("
UPDATE cart
SET quantity=?
WHERE id=?
AND user_id=?
");

$stmt->bind_param(

"iii",

$qty,

$id,

$user

);

$stmt->execute();

echo json_encode([
"success"=>true
]);