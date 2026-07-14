<?php
$order=$_GET["order"]??"";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thanh toán thất bại</title>

<style>

body{

font-family:Arial;

background:#fff6f6;

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

color:#dc2626;

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

<h1>❌ Thanh toán thất bại</h1>

<p>Mã đơn hàng:</p>

<h2>#<?= htmlspecialchars($order) ?></h2>

<a href="my-orders.php">

Quay lại đơn hàng

</a>

</div>

</body>

</html>