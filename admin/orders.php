<?php
session_start();
require_once "../config/db.php";

$result = $conn->query("
    SELECT
        o.id,
        o.total_price,
        o.status,
        o.created_at,
        u1.fullname AS buyer_name,
        u2.fullname AS seller_name,
        p.title,
        p.image
    FROM orders o
    LEFT JOIN users u1 ON o.buyer_id=u1.id
    LEFT JOIN users u2 ON o.seller_id=u2.id
    LEFT JOIN order_details od ON o.id=od.order_id
    LEFT JOIN products p ON od.product_id=p.id
    ORDER BY o.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>Quản lý đơn hàng</title>
        <link rel="stylesheet" href="../assets/css/admin.css">
        <style>
body{
margin:0;
font-family:Arial;
background:#eef2f7;
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
.container{
width:100%;
margin:0;
padding:0;
}
table{
width:100%;
background:white;
border-collapse:collapse;
box-shadow:0 10px 25px rgba(0,0,0,.08);
}
th{
background:#2563eb;
color:white;
padding:14px;
}
td{
padding:12px;
text-align:center;
border-bottom:1px solid #eee;
}
img{
width:70px;
height:70px;
object-fit:cover;
border-radius:8px;
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
.btn{
padding:8px 15px;
background:#475569;
color:white;
border-radius:8px;
text-decoration:none;
}
    </style>
    </head>
<body>
    <div class="wrapper">
        <?php include "sidebar.php"; ?>
    <div class="content">
        <h1>Quản lý đơn hàng</h1>
    <div class="container">
<table>
    <tr>
    <th>ID</th>
    <th>Ảnh</th>
    <th>Sản phẩm</th>
    <th>Người mua</th>
    <th>Người bán</th>
    <th>Tổng tiền</th>
    <th>Trạng thái</th>
    <th>Ngày đặt</th>
    <th>Chi tiết</th>
    </tr>
    <?php while($o=$result->fetch_assoc()){ ?>
    <tr>
    <td>#<?= $o["id"] ?></td>
    <td>
    <img src="../<?= htmlspecialchars($o["image"]) ?>">
    </td>
    <td>
    <?= htmlspecialchars($o["title"]) ?>
    </td>
    <td>
    <?= htmlspecialchars($o["buyer_name"]) ?>
    </td>
    <td>
    <?= htmlspecialchars($o["seller_name"]) ?>
    </td>
    <td>
    <?= number_format($o["total_price"],0,",",".") ?>đ
    </td>
    <td>
    <span class="<?= $o["status"] ?>">
    <?= strtoupper($o["status"]) ?>
    </span>
    </td>
    <td>
    <?= $o["created_at"] ?>
    </td>
    <td>
    <a
    class="btn"
    href="../pages/transactions.php">
    Xem
    </a>
    </td>
    </tr>
    <?php } ?>
    </table>
    </div>
    </div>
    </div>
    </body>
</html>