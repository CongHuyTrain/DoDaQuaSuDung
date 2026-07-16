<?php

session_start();

header("Content-Type: application/json");

require_once "../../config/db.php";

if(!isset($_SESSION["user_id"])){

    echo json_encode([
        "success"=>false,
        "message"=>"Bạn chưa đăng nhập."
    ]);

    exit;

}

$user_id=(int)$_SESSION["user_id"];

$product_id=(int)($_POST["product_id"] ?? 0);

$qty=(int)($_POST["quantity"] ?? 1);

if($qty<1){

    $qty=1;

}

/*
----------------------------------
Tự tra cart_id của chính user đang đăng nhập
(không tin cart_id do client tự gửi lên, tránh lỗi
và tránh lỗ hổng sửa giỏ hàng của người khác)
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

$cart_id=$cart["id"];

$stmt = $conn->prepare("
UPDATE cart_items
SET quantity=?
WHERE cart_id=?
AND product_id=?
");

if(!$stmt){
    die($conn->error);
}

$stmt->bind_param(
    "iii",
    $qty,
    $cart_id,
    $product_id
);

$stmt->execute();

echo json_encode([
"success"=>true
]);

$conn->close();