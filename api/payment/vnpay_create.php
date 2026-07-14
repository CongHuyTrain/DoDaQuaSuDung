<?php
session_start();

require_once "../config/db.php";
require_once "config.php";

if (!isset($_SESSION["user_id"])) {
    die("Bạn chưa đăng nhập.");
}

$user_id = (int)$_SESSION["user_id"];

/*
=====================================
Lấy toàn bộ giỏ hàng
=====================================
*/

$sql = "
SELECT
c.quantity,
p.price
FROM cart c
INNER JOIN products p
ON c.product_id=p.id
WHERE c.user_id=?
AND p.status='active'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$user_id);
$stmt->execute();

$rs = $stmt->get_result();

$total = 0;

while($row = $rs->fetch_assoc()){

    $total += $row["price"] * $row["quantity"];

}

if($total<=0){

    die("Giỏ hàng đang trống.");

}

/*
=====================================
Mã giao dịch
=====================================
*/

$vnp_TxnRef =
date("YmdHis").rand(1000,9999);

$vnp_OrderInfo =
"Thanh toan gio hang";

$vnp_OrderType = "billpayment";

$vnp_Amount = $total * 100;

$vnp_Locale = "vn";

$vnp_BankCode = "";

$vnp_IpAddr = $_SERVER["REMOTE_ADDR"];

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

header("Location: ".$paymentUrl);

exit;