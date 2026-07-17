<?php


date_default_timezone_set("Asia/Ho_Chi_Minh");

$vnp_TmnCode = "OBX1RB6R";

$vnp_HashSecret = "T6NOZXYK038T0ZPLU1T6M2Q94FGBHGEE";

$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";



$isHttps =
    (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ||
    (!empty($_SERVER["HTTP_X_FORWARDED_PROTO"]) && strtolower($_SERVER["HTTP_X_FORWARDED_PROTO"]) === "https") ||
    (!empty($_SERVER["HTTP_X_FORWARDED_SSL"]) && strtolower($_SERVER["HTTP_X_FORWARDED_SSL"]) === "on") ||
    (($_SERVER["SERVER_PORT"] ?? "") === "443");

$protocol = $isHttps ? "https" : "http";

$host = $_SERVER["HTTP_X_FORWARDED_HOST"] ?? $_SERVER["HTTP_HOST"] ?? "localhost";

$vnp_Returnurl = $protocol . "://" . $host . "/DoDaQuaSuDung/api/payment/vnpay_return.php";


$momo = [

    "name" => "Lê Công Huy",

    "phone" => "0353166811",

    "bank" => "MoMo",

    "qr" => "../assets/images/momo-qr.jpg"

];


define("VNPAY_DEBUG", true);

function vnpay_log($label, $data) {
    if (!VNPAY_DEBUG) return;
    $line = "[" . date("Y-m-d H:i:s") . "] " . $label . ": " . (is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_UNICODE)) . PHP_EOL;
    file_put_contents(__DIR__ . "/vnpay_debug.log", $line, FILE_APPEND);
}