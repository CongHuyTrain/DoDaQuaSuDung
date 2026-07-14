<?php

session_start();

require_once "../config/db.php";

require_once "config_vnpay.php";

if(!isset($_SESSION["user_id"])){

die("Chưa đăng nhập.");

}

$order_id=(int)($_GET["order_id"]??0);

if($order_id<=0){

die("Order không hợp lệ");

}

$stmt=$conn->prepare("

SELECT *

FROM orders

WHERE id=?

LIMIT 1

");

$stmt->bind_param("i",$order_id);

$stmt->execute();

$order=$stmt->get_result()->fetch_assoc();

if(!$order){

die("Không tìm thấy đơn.");

}

$vnp_TxnRef=$order["id"];

$vnp_OrderInfo="Thanh toan don hang #".$order["id"];

$vnp_OrderType="billpayment";

$vnp_Amount=$order["total_amount"]*100;

$vnp_Locale="vn";

$vnp_BankCode="";

$vnp_IpAddr=$_SERVER["REMOTE_ADDR"];

$inputData=array(

"vnp_Version"=>"2.1.0",

"vnp_TmnCode"=>$vnp_TmnCode,

"vnp_Amount"=>$vnp_Amount,

"vnp_Command"=>"pay",

"vnp_CreateDate"=>date("YmdHis"),

"vnp_CurrCode"=>"VND",

"vnp_IpAddr"=>$vnp_IpAddr,

"vnp_Locale"=>$vnp_Locale,

"vnp_OrderInfo"=>$vnp_OrderInfo,

"vnp_OrderType"=>$vnp_OrderType,

"vnp_ReturnUrl"=>$vnp_ReturnUrl,

"vnp_TxnRef"=>$vnp_TxnRef

);

ksort($inputData);

$query="";

$hashdata="";

foreach($inputData as $key=>$value){

$hashdata.=$key."=".$value."&";

$query.=urlencode($key)."=".urlencode($value)."&";

}

$hashdata=rtrim($hashdata,"&");

$query=rtrim($query,"&");

$vnpSecureHash=hash_hmac(

"sha512",

$hashdata,

$vnp_HashSecret

);

$paymentUrl=$vnp_Url."?".$query."&vnp_SecureHash=".$vnpSecureHash;

header("Location: ".$paymentUrl);

exit;