<?php
session_start();

require_once "../../config/db.php";
require_once "config.php";

if (!isset($_SESSION["user_id"])) {
    die("Bạn chưa đăng nhập.");
}

$user_id = (int)$_SESSION["user_id"];

/*
=====================================
Lấy toàn bộ giỏ hàng

Lưu ý: schema thật là cart (1 dòng / user) -> cart_items (từng sản phẩm,
có cart_id + product_id) -> products. Bản cũ JOIN thẳng "cart c ...
ON c.product_id=p.id" nhưng bảng cart KHÔNG có cột product_id nên luôn
lỗi SQL ngay từ bước này - đây là nguyên nhân chính khiến thanh toán
không chạy được.
=====================================
*/

$sql = "
SELECT
    ci.product_id,
    ci.quantity,
    p.price
FROM cart c
INNER JOIN cart_items ci ON ci.cart_id = c.id
INNER JOIN products p ON p.id = ci.product_id
WHERE c.user_id = ?
AND p.status = 'active'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$rs = $stmt->get_result();

$total = 0;
$hasItem = false;

while ($row = $rs->fetch_assoc()) {
    $hasItem = true;
    $total += $row["price"] * $row["quantity"];
}

if (!$hasItem || $total <= 0) {
    die("Giỏ hàng đang trống hoặc sản phẩm không còn được bán.");
}



$vnp_TxnRef =
date("YmdHis").rand(1000,9999);

$_SESSION["vnp_txn_ref"] = $vnp_TxnRef;

$vnp_OrderInfo =
"Thanh toan gio hang";

$vnp_OrderType = "billpayment";

// ép về số nguyên, tránh lỗi lệch số thập phân do phép nhân float
$vnp_Amount = (int) round($total * 100);

$vnp_Locale = "vn";

$vnp_BankCode = "";

/*
Sau ngrok, REMOTE_ADDR thường là 127.0.0.1 hoặc ::1 (IP nội bộ của máy
chạy ngrok agent), không phải IP thật của client. VNPay đôi khi từ chối
giá trị IPv6/loopback không hợp lệ trong vnp_IpAddr. Lấy IP thật từ
X-Forwarded-For nếu có, và fallback về 127.0.0.1 (IPv4) nếu không lấy
được gì hợp lệ.
*/
$vnp_IpAddr = $_SERVER["REMOTE_ADDR"] ?? "127.0.0.1";
if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
    $forwardedList = explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"]);
    $vnp_IpAddr = trim($forwardedList[0]);
}
if (!filter_var($vnp_IpAddr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    $vnp_IpAddr = "127.0.0.1";
}

$vnp_CreateDate = date("YmdHis");

$vnp_ExpireDate =
date(
"YmdHis",
strtotime("+15 minutes")
);

$inputData = [

"vnp_Version"=>"2.1.0",

"vnp_TmnCode"=>$vnp_TmnCode,

"vnp_Amount"=>$vnp_Amount,

"vnp_Command"=>"pay",

"vnp_CreateDate"=>$vnp_CreateDate,

"vnp_CurrCode"=>"VND",

"vnp_IpAddr"=>$vnp_IpAddr,

"vnp_Locale"=>$vnp_Locale,

"vnp_OrderInfo"=>$vnp_OrderInfo,

"vnp_OrderType"=>$vnp_OrderType,

"vnp_ReturnUrl"=>$vnp_Returnurl,

"vnp_TxnRef"=>$vnp_TxnRef,

"vnp_ExpireDate"=>$vnp_ExpireDate

];

ksort($inputData);

$query = "";

$hashdata = "";

foreach($inputData as $key=>$value){

$hashdata .=
urlencode($key)."=".
urlencode($value)."&";

$query .=
urlencode($key)."=".
urlencode($value)."&";

}

$hashdata = rtrim($hashdata,"&");

$query = rtrim($query,"&");

$vnpSecureHash = hash_hmac(

"sha512",

$hashdata,

$vnp_HashSecret

);

$paymentUrl =
$vnp_Url
."?"
.$query
."&vnp_SecureHash="
.$vnpSecureHash;

vnpay_log("CREATE returnUrl", $vnp_Returnurl);
vnpay_log("CREATE txnRef", $vnp_TxnRef);
vnpay_log("CREATE amount", $vnp_Amount);
vnpay_log("CREATE paymentUrl", $paymentUrl);

header("Location: ".$paymentUrl);

exit;