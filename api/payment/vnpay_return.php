<?php
session_start();

require_once "../config/db.php";
require_once "config.php";
require_once "../api/order/create_order.php";

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

$order_ids = createOrder(

$conn,

$_SESSION["user_id"],

"vnpay",

"paid"

);

$conn->commit();

header("Location: ../pages/my-orders.php");

exit;

}
catch(Exception $e){

$conn->rollback();

die($e->getMessage());

}