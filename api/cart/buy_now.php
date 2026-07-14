<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

require_once "../../config/db.php";

if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Bạn chưa đăng nhập."
    ]);
    exit;
}

$user_id = $_SESSION["user_id"];
$product_id = isset($_POST["product_id"]) ? (int)$_POST["product_id"] : 0;

if ($product_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Sản phẩm không hợp lệ."
    ]);
    exit;
}

/* Kiểm tra sản phẩm */

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

if($product["user_id"]==$user_id){

    echo json_encode([
        "success"=>false,
        "message"=>"Bạn không thể mua sản phẩm của chính mình."
    ]);
    exit;

}

$conn->begin_transaction();

try{

    /* Xóa toàn bộ giỏ hiện tại */

    $stmt=$conn->prepare("
        DELETE
        FROM cart_items
        WHERE cart_id IN(
            SELECT id
            FROM cart
            WHERE user_id=?
        )
    ");

    $stmt->bind_param("i",$user_id);
    $stmt->execute();

    $stmt=$conn->prepare("
        DELETE FROM cart
        WHERE user_id=?
    ");

    $stmt->bind_param("i",$user_id);
    $stmt->execute();

    /* Tạo cart mới */

    $stmt=$conn->prepare("
        INSERT INTO cart(user_id)
        VALUES(?)
    ");

    $stmt->bind_param("i",$user_id);
    $stmt->execute();

    $cart_id=$conn->insert_id;

    /* Thêm sản phẩm */

    $qty=1;

    $stmt=$conn->prepare("
        INSERT INTO cart_items(
            cart_id,
            product_id,
            quantity
        )
        VALUES(
            ?,?,?
        )
    ");

    $stmt->bind_param(
        "iii",
        $cart_id,
        $product_id,
        $qty
    );

    $stmt->execute();

    $conn->commit();

    echo json_encode([
        "success"=>true
    ]);

}catch(Exception $e){

    $conn->rollback();

    echo json_encode([
        "success"=>false,
        "message"=>$e->getMessage()
    ]);

}

$conn->close();