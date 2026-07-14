<?php

session_start();

header("Content-Type: application/json");

require_once "../../config/db.php";

if(!isset($_SESSION["user_id"])){

exit(json_encode([
"success"=>false,
"message"=>"Chưa đăng nhập"
]));

}

$user=(int)$_SESSION["user_id"];

$id=(int)($_POST["cart_id"]??0);

$stmt=$conn->prepare("
DELETE FROM cart
WHERE id=?
AND user_id=?
");

$stmt->bind_param("ii",$id,$user);

$stmt->execute();

echo json_encode([
"success"=>true,
"message"=>"Đã xóa khỏi giỏ hàng"
]);