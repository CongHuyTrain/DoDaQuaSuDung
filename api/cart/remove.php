<?php

session_start();

header("Content-Type: application/json");

require_once "../../config/db.php";

if(!isset($_SESSION["user_id"])){

    echo json_encode([
        "success"=>false,
        "message"=>"Chưa đăng nhập"
    ]);

    exit;

}

$user_id=(int)$_SESSION["user_id"];

$product_id=(int)($_POST["product_id"]??0);

/*
----------------------------------
Tự tra cart_id của chính user đang đăng nhập
(không tin cart_id do client gửi lên)
----------------------------------
*/

$stmt=$conn->prepare("
SELECT id
FROM cart
WHERE user_id=?
LIMIT 1
");

$stmt->bind_param("i",$user_id);

$stmt->execute();

$cart=$stmt->get_result()->fetch_assoc();

if(!$cart){

    echo json_encode([
        "success"=>false,
        "message"=>"Không tìm thấy giỏ hàng."
    ]);

    exit;

}

$stmt=$conn->prepare("
DELETE FROM cart_items
WHERE cart_id=?
AND product_id=?
");

$stmt->bind_param("ii",$cart["id"],$product_id);

$stmt->execute();

echo json_encode([
"success"=>true,
"message"=>"Đã xóa khỏi giỏ hàng"
]);

$conn->close();