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
$quantity = isset($_POST["quantity"]) ? max(1, (int)$_POST["quantity"]) : 1;

if ($product_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Sản phẩm không hợp lệ."
    ]);
    exit;
}

/*==============================
=      Kiểm tra sản phẩm       =
==============================*/

$sql = "
SELECT
    id,
    user_id,
    price,
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
        "message" => "Sản phẩm đã được bán hoặc tạm khóa."
    ]);
    exit;
}

if ($product["user_id"] == $user_id) {
    echo json_encode([
        "success" => false,
        "message" => "Bạn không thể mua sản phẩm của chính mình."
    ]);
    exit;
}

/*==============================
=       Lấy giỏ hàng           =
==============================*/

$sql = "
SELECT id
FROM cart
WHERE user_id = ?
LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$cart = $stmt->get_result()->fetch_assoc();

if (!$cart) {

    $stmt = $conn->prepare("
        INSERT INTO cart(user_id)
        VALUES(?)
    ");

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $cart_id = $conn->insert_id;

} else {

    $cart_id = $cart["id"];

}

/*==============================
=   Kiểm tra cart_items        =
==============================*/

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

    $newQty = $item["quantity"] + $quantity;

    $stmt = $conn->prepare("
    UPDATE cart_items
    SET quantity=?
    WHERE id=?
    ");

    $stmt->bind_param(
        "ii",
        $newQty,
        $item["id"]
    );

    $stmt->execute();

}else{

    $stmt = $conn->prepare("
    INSERT INTO cart_items(
    cart_id,
    product_id,
    quantity,
    price
    )
    VALUES(
    ?,?,?,?
    )
    ");

    $stmt->bind_param(
        "iiid",
        $cart_id,
        $product_id,
        $quantity,
        $product["price"]
    );

    $stmt->execute();

}

/*==============================
=    Update thời gian cart     =
==============================*/

$conn->query("
UPDATE cart
SET updated_at=NOW()
WHERE id=".$cart_id);

/*==============================
=     Đếm số sản phẩm          =
==============================*/

$rs = $conn->query("
SELECT
SUM(quantity) total
FROM cart_items
WHERE cart_id=".$cart_id);

$total = (int)$rs->fetch_assoc()["total"];

echo json_encode([
    "success"=>true,
    "message"=>"Đã thêm vào giỏ hàng.",
    "cart_count"=>$total
]);

$conn->close();