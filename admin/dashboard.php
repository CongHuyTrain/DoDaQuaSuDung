<?php
session_start();
require_once "../config/db.php";

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["role"] != "admin"
){
    header("Location: ../pages/login.html");
    exit;
}

$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()["total"];

$totalProducts = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()["total"];

$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()["total"];

$totalRevenue = $conn->query("
SELECT IFNULL(SUM(total_price),0) AS total
FROM orders
WHERE status='completed'
")->fetch_assoc()["total"];

$newUsers = $conn->query("
SELECT id,fullname,email,created_at
FROM users
ORDER BY created_at DESC
LIMIT 5
");

$newOrders = $conn->query("
SELECT
o.id,
o.total_price,
o.status,
u.fullname AS buyer
FROM orders o
JOIN users u
ON o.buyer_id=u.id
ORDER BY o.created_at DESC
LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>

<link rel="stylesheet" href="../assets/css/admin.css">

<style>
body{
margin:0;
background:#eef2f7;
font-family:Arial;
}
.wrapper{
display:flex;
}
.content{
margin-left:240px;
width:calc(100% - 240px);
padding:30px;
box-sizing:border-box;
}
.cards{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:20px;
margin-bottom:30px;
}
.card{
background:#fff;
padding:20px;
border-radius:12px;
box-shadow:0 5px 15px rgba(0,0,0,.08);
}
.card h3{
margin:0;
color:#666;
font-size:15px;
}
.card h1{
margin:12px 0 0;
color:#2563eb;
}
.table-box{
background:#fff;
padding:20px;
border-radius:12px;
margin-bottom:30px;
box-shadow:0 5px 15px rgba(0,0,0,.08);
}
table{
width:100%;
border-collapse:collapse;
}
th{
background:#2563eb;
color:#fff;
padding:12px;
}
td{
padding:12px;
border-bottom:1px solid #eee;
text-align:center;
}
.badge{
padding:4px 10px;
border-radius:20px;
color:#fff;
font-size:12px;
}
.pending{background:#f59e0b;}
.accepted{background:#2563eb;}
.completed{background:#16a34a;}
.cancelled{background:#dc2626;}
.rejected{background:#6b7280;}
</style>

</head>
<body>

<div class="wrapper">

<?php include "sidebar.php"; ?>

<div class="content">

<div class="cards">

<div class="card">
<h3>Người dùng</h3>
<h1><?= $totalUsers ?></h1>
</div>

<div class="card">
<h3>Sản phẩm</h3>
<h1><?= $totalProducts ?></h1>
</div>

<div class="card">
<h3>Đơn hàng</h3>
<h1><?= $totalOrders ?></h1>
</div>

<div class="card">
<h3>Doanh thu</h3>
<h1><?= number_format($totalRevenue,0,",",".") ?>đ</h1>
</div>

</div>

<div class="table-box">

<h2>Người dùng mới</h2>

<table>

<tr>
<th>ID</th>
<th>Họ tên</th>
<th>Email</th>
<th>Ngày tạo</th>
</tr>

<?php while($u=$newUsers->fetch_assoc()){ ?>

<tr>

<td><?= $u["id"] ?></td>
<td><?= htmlspecialchars($u["fullname"]) ?></td>
<td><?= htmlspecialchars($u["email"]) ?></td>
<td><?= $u["created_at"] ?></td>

</tr>

<?php } ?>

</table>

</div>

<div class="table-box">

<h2>Đơn hàng mới</h2>

<table>

<tr>
<th>ID</th>
<th>Người mua</th>
<th>Tổng tiền</th>
<th>Trạng thái</th>
</tr>

<?php while($o=$newOrders->fetch_assoc()){ ?>

<tr>

<td>#<?= $o["id"] ?></td>

<td><?= htmlspecialchars($o["buyer"]) ?></td>

    <td><?= number_format($o["total_price"],0,",",".") ?>đ</td>

    <td>
    <span class="badge <?= $o["status"] ?>">
    <?= strtoupper($o["status"]) ?>
    </span>
    </td>

    </tr>

    <?php } ?>
        </table>
        </div>
        </div>
        </div>
    </body>
</html>