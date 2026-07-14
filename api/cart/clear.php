<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

require_once "../../config/db.php";

if(!isset($_SESSION["user_id"])){

    echo json_encode([
        "success"=>false
    ]);
    exit;
}

$user_id=$_SESSION["user_id"];

$sql="
DELETE ci
FROM cart_items ci
JOIN cart c
ON ci.cart_id=c.id
WHERE c.user_id=?
";

$stmt=$conn->prepare($sql);

$stmt->bind_param("i",$user_id);

$stmt->execute();

echo json_encode([
    "success"=>true,
    "message"=>"Đã xóa toàn bộ giỏ hàng."
]);

$conn->close();