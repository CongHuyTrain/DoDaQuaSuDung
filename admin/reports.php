<?php
// admin/reports.php

session_start();
require_once "../config/db.php";

// Thống kê
$totalUsers = $conn->query("SELECT COUNT(*) total FROM users")->fetch_assoc()['total'];

$totalProducts = $conn->query("SELECT COUNT(*) total FROM products")->fetch_assoc()['total'];

$totalOrders = $conn->query("SELECT COUNT(*) total FROM orders")->fetch_assoc()['total'];

$totalRevenue = $conn->query("
SELECT IFNULL(SUM(total_amount),0) total
FROM orders
WHERE status='completed'
")->fetch_assoc()['total'];

$activeProducts = $conn->query("
SELECT COUNT(*) total
FROM products
WHERE status='active'
")->fetch_assoc()['total'];

$soldProducts = $conn->query("
SELECT COUNT(*) total
FROM products
WHERE status='sold'
")->fetch_assoc()['total'];

$pendingProducts = $conn->query("
SELECT COUNT(*) total
FROM products
WHERE status='pending'
")->fetch_assoc()['total'];

$blockedUsers = $conn->query("
SELECT COUNT(*) total
FROM users
WHERE status='blocked'
")->fetch_assoc()['total'];

$latestOrders = $conn->query("
SELECT
o.id,
u.fullname,
o.total_amount,
o.status,
o.created_at
FROM orders o
LEFT JOIN users u
ON o.buyer_id=u.id
ORDER BY o.created_at DESC
LIMIT 10
");

?>

<!DOCTYPE html>
<html lang="vi">

<head>

<meta charset="UTF-8">

<title>Thống kê hệ thống</title>

<link rel="stylesheet" href="../assets/css/admin.css">

<style>

body{
margin:0;
font-family:Arial;
background:#eef2f7;
}

.container{
width:1400px;
margin:auto;
padding:40px;
}

.grid{

display:grid;

grid-template-columns:repeat(4,1fr);

gap:20px;

margin-bottom:35px;

}

.card{

background:white;

padding:25px;

border-radius:15px;

box-shadow:0 8px 25px rgba(0,0,0,.08);

}

.card h4{

margin:0;
color:#666;

}

.card h1{

margin-top:18px;

font-size:36px;

color:#2563eb;

}

table{

width:100%;

background:white;

border-collapse:collapse;

box-shadow:0 8px 25px rgba(0,0,0,.08);

}

th{

background:#2563eb;

color:white;

padding:14px;

}

td{

padding:12px;

border-bottom:1px solid #eee;

text-align:center;

}

.pending{
color:#f59e0b;
font-weight:bold;
}

.confirmed{
color:#2563eb;
font-weight:bold;
}

.completed{
color:#16a34a;
font-weight:bold;
}

.cancelled{
color:#dc2626;
font-weight:bold;
}

.rejected{
color:#dc2626;
font-weight:bold;
}

</style>

</head>

<body>

<div class="container">

<h1>Thống kê hệ thống</h1>

<div class="grid">

<div class="card">
<h4>Người dùng</h4>
<h1><?= $totalUsers ?></h1>
</div>

<div class="card">
<h4>Sản phẩm</h4>
<h1><?= $totalProducts ?></h1>
</div>

<div class="card">
<h4>Đơn hàng</h4>
<h1><?= $totalOrders ?></h1>
</div>

<div class="card">
<h4>Doanh thu</h4>
<h1><?= number_format($totalRevenue,0,",",".") ?>đ</h1>
</div>

<div class="card">
<h4>Sản phẩm đang bán</h4>
<h1><?= $activeProducts ?></h1>
</div>

<div class="card">
<h4>Sản phẩm đã bán</h4>
<h1><?= $soldProducts ?></h1>
</div>

<div class="card">
<h4>Chờ duyệt</h4>
<h1><?= $pendingProducts ?></h1>
</div>

<div class="card">
<h4>Tài khoản bị khóa</h4>
<h1><?= $blockedUsers ?></h1>
</div>

</div>

<h2>10 đơn hàng gần nhất</h2>

<table>

<tr>

<th>ID</th>

<th>Người mua</th>

<th>Tổng tiền</th>

<th>Trạng thái</th>

<th>Ngày tạo</th>

</tr>

<?php while($o=$latestOrders->fetch_assoc()){ ?>

<tr>

<td>#<?= $o["id"] ?></td>

<td><?= htmlspecialchars($o["fullname"]) ?></td>

<td><?= number_format($o["total_amount"],0,",",".") ?>đ</td>

<td>

<span class="<?= $o["status"] ?>">

<?= strtoupper($o["status"]) ?>

</span>

</td>

<td><?= $o["created_at"] ?></td>

</tr>

<?php } ?>

</table>

</div>

</body>

</html>