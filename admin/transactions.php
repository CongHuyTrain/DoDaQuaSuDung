<?php
session_start();
require_once "../config/db.php";

if (!isset($_GET["id"])) {
    die("Thiếu mã đơn hàng.");
}

$order_id = (int)$_GET["id"];

$sql = "
SELECT
o.*,
p.id AS product_id,
p.title,
p.image,
u1.fullname AS buyer_name,
u2.fullname AS seller_name
FROM orders o
INNER JOIN order_details od ON o.id=od.order_id
INNER JOIN products p ON od.product_id=p.id
INNER JOIN users u1 ON o.buyer_id=u1.id
INNER JOIN users u2 ON o.seller_id=u2.id
WHERE o.id=?
LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if(!$order){
    die("Không tìm thấy đơn hàng.");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chi tiết đơn hàng</title>

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

.card{
    background:#fff;
    padding:25px;
    border-radius:12px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
    display:flex;
    gap:30px;
}

.image{
    width:260px;
}

.image img{
    width:100%;
    border-radius:10px;
}

.info{
    flex:1;
}

.info h2{
    margin-top:0;
}

.line{
    margin:12px 0;
    font-size:16px;
}

.price{
    font-size:30px;
    color:#ff5722;
    font-weight:bold;
}

.badge{
    display:inline-block;
    padding:8px 16px;
    border-radius:20px;
    color:#fff;
    font-weight:bold;
}

.pending{background:#f59e0b;}
.accepted{background:#16a34a;}
.completed{background:#2563eb;}
.cancelled{background:#dc2626;}
.rejected{background:#991b1b;}

.btn{
    display:inline-block;
    text-decoration:none;
    color:#fff;
    padding:10px 18px;
    border-radius:8px;
    margin-right:10px;
    margin-top:20px;
    font-weight:bold;
}

.back{background:#64748b;}
.accept{background:#16a34a;}
.reject{background:#dc2626;}
.complete{background:#2563eb;}

</style>
</head>

<body>

<div class="wrapper">

<?php include "sidebar.php"; ?>

<div class="content">

<h1>Chi tiết đơn hàng</h1>

<a href="orders.php" class="btn back">← Quay lại</a>

<div class="card">

<div class="image">

<img src="../<?= htmlspecialchars($order["image"]) ?>">

</div>

<div class="info">

<h2><?= htmlspecialchars($order["title"]) ?></h2>

<div class="price">
<?= number_format($order["total_amount"],0,",",".") ?>đ
</div>

<div class="line">
<b>Người mua:</b>
<?= htmlspecialchars($order["buyer_name"]) ?>
</div>

<div class="line">
<b>Người bán:</b>
<?= htmlspecialchars($order["seller_name"]) ?>
</div>

<div class="line">
<b>Người nhận:</b>
<?= htmlspecialchars($order["receiver_name"]) ?>
</div>

<div class="line">
<b>SĐT:</b>
<?= htmlspecialchars($order["receiver_phone"]) ?>
</div>

<div class="line">
<b>Địa chỉ:</b>
<?= htmlspecialchars($order["receiver_address"]) ?>
</div>

<div class="line">
<b>Ghi chú:</b><br>
<?= nl2br(htmlspecialchars($order["note"])) ?>
</div>

<div class="line">
<b>Ngày đặt:</b>
<?= $order["created_at"] ?>
</div>

<div class="line">
<b>Thanh toán:</b>
<?= htmlspecialchars($order["payment_method"]) ?>
</div>

<div class="line">
<b>Trạng thái thanh toán:</b>
<?= strtoupper($order["payment_status"]) ?>
</div>

<div class="line">
<span class="badge <?= $order["status"] ?>">
<?= strtoupper($order["status"]) ?>
</span>
</div>

<?php if($order["status"]=="pending"){ ?>

<a
class="btn accept"
href="../api/order/accept.php?id=<?= $order["id"] ?>"
onclick="return confirm('Chấp nhận đơn hàng?')">
Chấp nhận
</a>

<a
class="btn reject"
href="../api/order/reject.php?id=<?= $order["id"] ?>"
onclick="return confirm('Từ chối đơn hàng?')">
Từ chối
</a>

<?php } ?>

<?php if($order["status"]=="accepted"){ ?>

<a
class="btn complete"
href="../api/order/complete.php?id=<?= $order["id"] ?>"
onclick="return confirm('Đã giao hàng thành công?')">
Hoàn thành
</a>

<?php } ?>

</div>

</div>

</div>

</div>

</body>
</html>