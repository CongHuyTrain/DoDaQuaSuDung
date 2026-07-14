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

$sql = "
SELECT
    c.id AS cart_id,
    ci.id AS cart_item_id,
    ci.quantity,

    p.id AS product_id,
    p.title,
    p.price,
    p.image,
    p.location,
    p.condition_item,
    p.status,

    u.fullname AS seller_name

FROM cart c

INNER JOIN cart_items ci
ON c.id = ci.cart_id

INNER JOIN products p
ON ci.product_id = p.id

LEFT JOIN users u
ON p.user_id = u.id

WHERE c.user_id = ?

ORDER BY ci.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$user_id);
$stmt->execute();

$rs = $stmt->get_result();

$items = [];
$total = 0;

while($row = $rs->fetch_assoc()){

    $row["subtotal"] = $row["price"] * $row["quantity"];
    $row["price_formatted"] = number_format($row["price"],0,",",".")." đ";
    $row["subtotal_formatted"] = number_format($row["subtotal"],0,",",".")." đ";

    $total += $row["subtotal"];

    $items[] = $row;
}

echo json_encode([
    "success"=>true,
    "items"=>$items,
    "total"=>$total,
    "total_formatted"=>number_format($total,0,",",".")." đ"
],JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();