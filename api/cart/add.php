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

$user_id = (int)$_SESSION["user_id"];
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
    status
FROM products
WHERE id = ?
LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();

$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo json_encode([
        "success" => false,
        "message" => "Không tìm thấy sản phẩm."
    ]);
    exit;
}

if ($product["status"] != "active") {
    echo json_encode([
        "success" => false,
        "message" => "Sản phẩm không còn khả dụng."
    ]);
    exit;
}

if ($product["user_id"] == $user_id) {
    echo json_encode([
        "success" => false,
        "message" => "Không thể thêm sản phẩm của chính mình."
    ]);
    exit;
}

/* Lấy cart */

$stmt = $conn->prepare("
SELECT id
FROM cart
WHERE user_id=?
LIMIT 1
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$cart = $stmt->get_result()->fetch_assoc();

if(!$cart){

    $stmt = $conn->prepare("
    INSERT INTO cart(user_id)
    VALUES(?)
    ");

    $stmt->bind_param("i",$user_id);
    $stmt->execute();

    $cart_id = $conn->insert_id;

}else{

    $cart_id = $cart["id"];

}

/* Kiểm tra đã có chưa */

$stmt = $conn->prepare("
SELECT
id,
quantity
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

$item = $stmt->get_result()->fetch_assoc();

if($item){

    $stmt = $conn->prepare("
    UPDATE cart_items
    SET quantity=quantity+1
    WHERE id=?
    ");

    $stmt->bind_param(
    "i",
    $item["id"]
    );

    $stmt->execute();

}else{

    $qty = 1;

    $stmt = $conn->prepare("
    INSERT INTO cart_items
    (
        cart_id,
        product_id,
        quantity
    )
    VALUES
    (
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

}

/* Đếm số lượng */

$stmt = $conn->prepare("
SELECT
IFNULL(SUM(quantity),0) total
FROM cart_items
WHERE cart_id=?
");

$stmt->bind_param("i",$cart_id);

$stmt->execute();

$total = $stmt->get_result()->fetch_assoc()["total"];

echo json_encode([

    "success"=>true,

    "message"=>"Đã thêm vào giỏ hàng.",

    "count"=>(int)$total

],JSON_UNESCAPED_UNICODE);

$conn->close();