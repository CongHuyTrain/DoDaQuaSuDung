<?php
/*=============================
    VNPAY SANDBOX CONFIG
==============================*/

date_default_timezone_set("Asia/Ho_Chi_Minh");

/* Website */
$vnp_TmnCode = "OBX1RB6R";

/* Secret Key */
$vnp_HashSecret = "T6NOZXYK038T0ZPLU1T6M2Q94FGBHGEE";

/* Sandbox URL */
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";

/* Return URL */
$vnp_Returnurl =
"http://localhost/DoDaQuaSuDung/payment/vnpay_return.php";

/*=============================
    MOMO QR
==============================*/

$momo = [

    "name" => "Lê Công Huy",

    "phone" => "0353166811",

    "bank" => "MoMo",

    // ảnh QR của bạn
    "qr" => "../assets/images/momo-qr.jpg"

];