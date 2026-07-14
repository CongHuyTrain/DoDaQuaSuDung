<?php

require_once "../config/db.php";

require_once "config_vnpay.php";

$vnp_SecureHash=$_GET["vnp_SecureHash"];

$data=$_GET;

unset($data["vnp_SecureHash"]);

unset($data["vnp_SecureHashType"]);

ksort($data);

$hashData="";

foreach($data as $k=>$v){

$hashData.=$k."=".$v."&";

}

$hashData=rtrim($hashData,"&");

$secureHash=hash_hmac(

"sha512",

$hashData,

$vnp_HashSecret

);

if($secureHash!=$vnp_SecureHash){

die("Sai chữ ký.");

}

$order_id=(int)$_GET["vnp_TxnRef"];

$response=$_GET["vnp_ResponseCode"];

if($response=="00"){

$stmt=$conn->prepare("

UPDATE orders

SET

status='paid',

payment_method='vnpay',

payment_status='paid',

paid_at=NOW(),

transaction_no=?

WHERE id=?

");

$txn=$_GET["vnp_TransactionNo"];

$stmt->bind_param(

"si",

$txn,

$order_id

);

$stmt->execute();

echo "<script>

alert('Thanh toán thành công');

location='../pages/my-orders.php';

</script>";

}else{

$stmt=$conn->prepare("

UPDATE orders

SET

status='cancelled',

payment_status='failed'

WHERE id=?

");

$stmt->bind_param("i",$order_id);

$stmt->execute();

echo "<script>

alert('Thanh toán thất bại');

location='../pages/my-orders.php';

</script>";

}