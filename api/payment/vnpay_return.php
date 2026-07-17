<?php
session_start();

require_once "../../config/db.php";
require_once "config.php";
require_once "../order/create_order.php";

if (!isset($_SESSION["user_id"])) {
    die("Bạn chưa đăng nhập.");
}

$user_id = (int)$_SESSION["user_id"];
$txnRef = $_GET["vnp_TxnRef"] ?? "";



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

vnpay_log("RETURN raw GET", $_GET);
vnpay_log("RETURN computedHash", $secureHash);
vnpay_log("RETURN receivedHash", $vnp_SecureHash);

if ($secureHash !== $vnp_SecureHash) {
    vnpay_log("RETURN result", "SIGNATURE MISMATCH -> fail");
    header("Location: ../../pages/payment-fail.php?order=" . urlencode($txnRef));
    exit;
}

if (empty($_SESSION["vnp_txn_ref"]) || $txnRef !== $_SESSION["vnp_txn_ref"]) {
    vnpay_log("RETURN result", "TXN REF MISMATCH session=" . ($_SESSION["vnp_txn_ref"] ?? "(empty)") . " got=" . $txnRef);
    header("Location: ../../pages/payment-fail.php?order=" . urlencode($txnRef));
    exit;
}

if (($_GET["vnp_ResponseCode"] ?? "") !== "00") {
    vnpay_log("RETURN result", "ResponseCode=" . ($_GET["vnp_ResponseCode"] ?? "?") . " -> fail");
    unset($_SESSION["vnp_txn_ref"]);
    header("Location: ../../pages/payment-fail.php?order=" . urlencode($txnRef));
    exit;
}



$sql = "
SELECT
    ci.product_id,
    ci.quantity,
    ci.price,
    p.user_id seller_id
FROM cart c
INNER JOIN cart_items ci ON ci.cart_id = c.id
INNER JOIN products p ON p.id = ci.product_id
WHERE c.user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$user_id);
$stmt->execute();

$rs = $stmt->get_result();

if ($rs->num_rows == 0) {
    header("Location: ../../pages/payment-fail.php?order=" . urlencode($txnRef));
    exit;
}

$conn->begin_transaction();

try {

    $order_ids = createOrder(
        $conn,
        $user_id,
        "vnpay",
        "paid"
    );

    $clearStmt = $conn->prepare("
        DELETE ci FROM cart_items ci
        INNER JOIN cart c ON c.id = ci.cart_id
        WHERE c.user_id = ?
    ");
    $clearStmt->bind_param("i", $user_id);
    $clearStmt->execute();

    $conn->commit();

    unset($_SESSION["vnp_txn_ref"]);

    $orderParam = is_array($order_ids) ? implode(",", $order_ids) : $order_ids;
    header("Location: ../../pages/payment-success.php?order=" . urlencode($orderParam));
    exit;

} catch (Exception $e) {

    $conn->rollback();
    header("Location: ../../pages/payment-fail.php?order=" . urlencode($txnRef));
    exit;

}