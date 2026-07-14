<?php
$order = $_GET["order"] ?? "";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thanh toán thành công</title>

<style>

body{

font-family:Arial;

background:#f6fff6;

display:flex;

justify-content:center;

align-items:center;

height:100vh;

}

.box{

background:#fff;

padding:40px;

border-radius:15px;

text-align:center;

box-shadow:0 0 20px rgba(0,0,0,.1);

}

h1{

color:#16a34a;

}

a{

display:inline-block;

margin-top:20px;

padding:12px 24px;

background:#2563eb;

color:#fff;

text-decoration:none;

border-radius:8px;

}

</style>

</head>

<body>

<div class="box">

<h1>✅ Thanh toán thành công</h1>

<p>Mã đơn hàng:</p>

<h2>#<?= htmlspecialchars($order) ?></h2>

<a href="my-orders.php">

Xem đơn hàng

</a>

</div>

</body>

</html>