<?php


date_default_timezone_set("Asia/Ho_Chi_Minh");

$vnp_TmnCode = "OBX1RB6R";

$vnp_HashSecret = "T6NOZXYK038T0ZPLU1T6M2Q94FGBHGEE";

$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";


$protocol = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
$host = $_SERVER["HTTP_HOST"] ?? "localhost";
$vnp_Returnurl = $protocol . "://" . $host . "/DoDaQuaSuDung/api/payment/vnpay_return.php";


$momo = [

    "name" => "Lê Công Huy",

    "phone" => "0353166811",

    "bank" => "MoMo",

    "qr" => "../assets/images/momo-qr.jpg"

];