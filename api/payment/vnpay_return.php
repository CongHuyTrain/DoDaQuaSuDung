<?php
session_start();

require_once "../config/db.php";
require_once "config.php";

if (!isset($_SESSION["user_id"])) {
    die("Bạn chưa đăng nhập.");
}

$vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';

$data = $_GET;
unset($data['vnp_SecureHash']);
unset($data['vnp_SecureHashType']);

ksort($data);

$hashData = "";

foreach ($data as $key => $value) {
    $hashData .= urlencode($key) . "=" . urlencode($value) . "&";
}

$hashData = rtrim($hashData, "&");

$secureHash = hash_hmac(
    "sha512",
    $hashData,
    $vnp_HashSecret
);

if ($secureHash != $vnp_SecureHash) {
    die("Sai checksum.");
}

if ($_GET["vnp_ResponseCode"] != "00") {

    die("Thanh toán thất bại.");

}

/*
=================================
Lấy cart của user
=================================
*/

$user_id = $_SESSION["user_id"];

$sql = "
SELECT
c.product_id,
c.quantity,
p.user_id seller_id,
p.price
FROM cart c
JOIN products p
ON c.product_id=p.id
WHERE c.user_id=?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$user_id);
$stmt->execute();

$rs = $stmt->get_result();

if($rs->num_rows==0){

    die("Giỏ hàng rỗng.");

}

$conn->begin_transaction();

try{

$total = 0;

$items = [];

while($r=$rs->fetch_assoc()){

    $total += $r["price"]*$r["quantity"];

    $items[] = $r;

}

/*
=================================
Seller
=================================
*/

$seller = $items[0]["seller_id"];

/*
=================================
Orders
=================================
*/

$status = "pending";

$payment_method = "vnpay";

$payment_status = "paid";

$stmt = $conn->prepare("
INSERT INTO orders
(
buyer_id,
seller_id,
total_amount,
status,
payment_method,
payment_status
)
VALUES
(
?,?,?,?,?,?
)
");

$stmt->bind_param(
"iidsss",
$user_id,
$seller,
$total,
$status,
$payment_method,
$payment_status
);

$stmt->execute();

$order_id = $conn->insert_id;

/*
=================================
Order Details
=================================
*/

foreach($items as $item){

$stmt = $conn->prepare("
INSERT INTO order_details
(
order_id,
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
$order_id,
$item["product_id"],
$item["quantity"],
$item["price"]
);

$stmt->execute();

/*
Product
*/

$stmt=$conn->prepare("
UPDATE products
SET status='pending'
WHERE id=?
");

$stmt->bind_param(
"i",
$item["product_id"]
);

$stmt->execute();

}

/*
Delete Cart
*/

$stmt=$conn->prepare("
DELETE FROM cart
WHERE user_id=?
");

$stmt->bind_param(
"i",
$user_id
);

$stmt->execute();

$conn->commit();

header("Location: ../pages/my-orders.php");

exit;

}catch(Exception $e){

$conn->rollback();

die($e->getMessage());

}