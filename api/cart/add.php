<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");

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

$qty=max(1,(int)($_POST["quantity"] ?? 1));

/*
----------------------------------
Kiểm tra sản phẩm
----------------------------------
*/

$stmt=$conn->prepare("
SELECT id,price,status
FROM products
WHERE id=?
LIMIT 1
");

$stmt->bind_param("i",$product_id);

$stmt->execute();

$product=$stmt->get_result()->fetch_assoc();

if(!$product){

    echo json_encode([
        "success"=>false,
        "message"=>"Sản phẩm không tồn tại."
    ]);

    exit;

}

if($product["status"]!="active"){

    echo json_encode([
        "success"=>false,
        "message"=>"Sản phẩm không khả dụng."
    ]);

    exit;

}

$price=$product["price"];

/*
----------------------------------
Lấy cart của user
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

    $stmt=$conn->prepare("
    INSERT INTO cart(user_id)
    VALUES(?)
    ");

    $stmt->bind_param("i",$user_id);

    $stmt->execute();

    $cart_id=$conn->insert_id;

}else{

    $cart_id=$cart["id"];

}

/*
----------------------------------
Đã có trong cart chưa
----------------------------------
*/

$stmt=$conn->prepare("
SELECT id,quantity
FROM cart_items
WHERE cart_id=?
AND product_id=?
LIMIT 1
");

$stmt->bind_param(
"ii",
$cart_id,
$product_id
);

$stmt->execute();

$item=$stmt->get_result()->fetch_assoc();

if($item){

    // Sản phẩm đã có trong giỏ -> cộng dồn số lượng, cập nhật giá mới nhất
    $newQty=$item["quantity"]+$qty;

    $stmt=$conn->prepare("
    UPDATE cart_items
    SET quantity=?, price=?
    WHERE id=?
    ");

    $stmt->bind_param(
    "idi",
    $newQty,
    $price,
    $item["id"]
    );

    $stmt->execute();

}else{

    // Sản phẩm chưa có trong giỏ -> thêm mới, kèm giá lấy từ bảng products
    $stmt=$conn->prepare("
    INSERT INTO cart_items
    (
    cart_id,
    product_id,
    quantity,
    price
    )
    VALUES
    (
    ?,?,?,?
    )
    ");

    $stmt->bind_param(
    "iiid",
    $cart_id,
    $product_id,
    $qty,
    $price
    );

    $stmt->execute();

}

/*
----------------------------------
Đếm tổng số lượng sản phẩm trong giỏ (để cập nhật badge)
----------------------------------
*/

$stmt=$conn->prepare("
SELECT COALESCE(SUM(quantity),0) AS total
FROM cart_items
WHERE cart_id=?
");

$stmt->bind_param("i",$cart_id);

$stmt->execute();

$countRow=$stmt->get_result()->fetch_assoc();

$cartCount=(int)$countRow["total"];

echo json_encode([

"success"=>true,

"message"=>"Đã thêm vào giỏ.",

"count"=>$cartCount

]);

$conn->close();