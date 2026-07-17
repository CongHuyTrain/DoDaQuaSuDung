<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

require_once "../../config/db.php";
require_once "../order/create_order.php";

if(!isset($_SESSION["user_id"])){

    echo json_encode([
        "success"=>false,
        "message"=>"Bạn chưa đăng nhập."
    ]);

    exit;

}

$user_id=(int)$_SESSION["user_id"];

$input = json_decode(file_get_contents("php://input"), true);

$payment_method = $input["payment_method"] ?? "cod";

$allowed_methods = ["cod","vnpay","momo"];

if(!in_array($payment_method, $allowed_methods)){
    $payment_method = "cod";
}

$product_ids = $input["product_ids"] ?? [];

$product_ids = array_values(array_unique(array_map("intval", (array)$product_ids)));

if(empty($product_ids)){

    echo json_encode([
        "success"=>false,
        "message"=>"Vui lòng chọn sản phẩm cần mua."
    ]);

    exit;

}

$payment_status = "pending";

try{

    $order_ids = createOrder(
        $conn,
        $user_id,
        $payment_method,
        $payment_status,
        $product_ids
    );

    echo json_encode([
        "success"=>true,
        "message"=>"Đặt hàng thành công!",
        "order_ids"=>$order_ids
    ]);

}catch(Exception $e){

    echo json_encode([
        "success"=>false,
        "message"=>$e->getMessage()
    ]);

}

$conn->close();