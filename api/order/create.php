<?php
// api/order/create.php

session_start();
header("Content-Type: application/json; charset=UTF-8");

require_once "../../config/db.php";

// =========================
// Kiểm tra đăng nhập
// =========================

if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Bạn chưa đăng nhập."
    ]);
    exit;
}

$buyer_id = (int)$_SESSION["user_id"];

// =========================
// Lấy dữ liệu
// =========================

$product_id = isset($_POST["product_id"]) ? (int)$_POST["product_id"] : 0;

$receiver_name = trim($_POST["receiver_name"] ?? "");

$receiver_phone = trim($_POST["receiver_phone"] ?? "");

$receiver_address = trim($_POST["receiver_address"] ?? "");

$note = trim($_POST["note"] ?? "");

if (
    $product_id <= 0 ||
    $receiver_name == "" ||
    $receiver_phone == "" ||
    $receiver_address == ""
) {

    echo json_encode([
        "success" => false,
        "message" => "Vui lòng nhập đầy đủ thông tin."
    ]);

    exit;
}

// =========================
// Lấy sản phẩm
// =========================

$sql = "

SELECT

id,

user_id,

price,

status

FROM products

WHERE id=?

LIMIT 1

";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i",$product_id);

$stmt->execute();

$product = $stmt->get_result()->fetch_assoc();

if(!$product){

    echo json_encode([
        "success"=>false,
        "message"=>"Không tìm thấy sản phẩm."
    ]);

    exit;
}

if($product["status"]!="active"){

    echo json_encode([
        "success"=>false,
        "message"=>"Sản phẩm không còn khả dụng."
    ]);

    exit;
}

if($product["user_id"]==$buyer_id){

    echo json_encode([
        "success"=>false,
        "message"=>"Không thể mua sản phẩm của chính mình."
    ]);

    exit;
}

$seller_id = $product["user_id"];

// =========================
// Transaction
// =========================

$conn->begin_transaction();

try{

    // orders

    $sql="

    INSERT INTO orders(

    buyer_id,

    seller_id,

    total_amount,

    status,

    note,

    receiver_name,

    receiver_phone,

    receiver_address

    )

    VALUES(

    ?,?,?,?,?,?,?,?

    )

    ";

    $stmt=$conn->prepare($sql);

    $status="pending";

    $stmt->bind_param(

    "iidsssss",

    $buyer_id,

    $seller_id,

    $product["price"],

    $status,

    $note,

    $receiver_name,

    $receiver_phone,

    $receiver_address

    );

    $stmt->execute();

    $order_id=$conn->insert_id;

    // order_details

    $sql="

    INSERT INTO order_details(

    order_id,

    product_id,

    quantity,

    price

    )

    VALUES(

    ?,?,?,?

    )

    ";

    $stmt=$conn->prepare($sql);

    $qty=1;

    $stmt->bind_param(

    "iiid",

    $order_id,

    $product_id,

    $qty,

    $product["price"]

    );

    $stmt->execute();

    // khóa sản phẩm

    $sql="

    UPDATE products

    SET status='pending'

    WHERE id=?

    ";

    $stmt=$conn->prepare($sql);

    $stmt->bind_param("i",$product_id);

    $stmt->execute();

    $conn->commit();

    echo json_encode([

        "success"=>true,

        "message"=>"Đặt mua thành công.",

        "order_id"=>$order_id

    ]);

}
catch(Exception $e){

    $conn->rollback();

    echo json_encode([

        "success"=>false,

        "message"=>$e->getMessage()

    ]);

}

$conn->close();

?>