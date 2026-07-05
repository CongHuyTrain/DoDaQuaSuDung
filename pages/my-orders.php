<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
require_once "../config/db.php";
$user_id = $_SESSION["user_id"];
$sql = "
SELECT
o.id,
o.status,
o.total_amount,
o.created_at,
p.id AS product_id,
p.title,
p.image,
u.fullname AS seller_name
FROM orders o
INNER JOIN order_details od
ON o.id=od.order_id
INNER JOIN products p
ON od.product_id=p.id
INNER JOIN users u
ON o.seller_id=u.id
WHERE o.buyer_id=?
ORDER BY o.created_at DESC
";
$stmt=$conn->prepare($sql);
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result=$stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đơn hàng của tôi</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body{
background:#f5f7fb;
font-family:Arial;
}

.container{
width:1200px;
margin:auto;
padding:40px 0;
}

.title{
font-size:32px;
font-weight:bold;
margin-bottom:25px;
}

.card{
background:#fff;
border-radius:14px;
padding:20px;
margin-bottom:18px;
display:flex;
gap:20px;
align-items:center;
box-shadow:0 8px 20px rgba(0,0,0,.06);
}

.card img{
width:150px;
height:150px;
object-fit:cover;
border-radius:10px;
}

.info{
flex:1;
}

.info h3{
margin-bottom:10px;
}

.price{
font-size:22px;
color:#ff5722;
font-weight:bold;
margin:10px 0;
}

.badge{
display:inline-block;
padding:5px 14px;
border-radius:30px;
color:#fff;
font-size:13px;
}

.pending{
background:#f59e0b;
}

.accepted{
background:#2563eb;
}

.completed{
background:#16a34a;
}

.cancelled{
background:#dc2626;
}

.action{
display:flex;
flex-direction:column;
gap:10px;
}
.btn{
padding:10px 18px;
border:none;
border-radius:8px;
cursor:pointer;
font-weight:bold;
text-decoration:none;
text-align:center;
}

.view{
background:#2563eb;
color:#fff;
}

.cancel{
background:#ef4444;
color:#fff;
}

.empty{
text-align:center;
padding:80px;
background:#fff;
border-radius:14px;
}

</style>
</head>
<body>
<div class="container">
<div class="title"> Đơn hàng của tôi </div>
<?php
if($result->num_rows==0){
?>
<div class="empty">
<h2>Bạn chưa có đơn hàng nào.</h2>
<br>
<a href="../products.html" class="btn view">
Mua ngay
</a>
</div>
<?php
}else{

while($row=$result->fetch_assoc()){
$status=$row["status"];
$class=$status;
if($status=="confirmed"){
$class="accepted";
}

?>

<div class="card">
<img src="<?= htmlspecialchars($row["image"]) ?>">
<div class="info">
<h3>
<?= htmlspecialchars($row["title"]) ?>
</h3>
<div>
Người bán:
<b>
<?= htmlspecialchars($row["seller_name"]) ?>
</b>
</div>
<div class="price">
<?= number_format($row["total_amount"],0,",",".") ?>
đ
</div>
<div>
Ngày đặt:
<?= $row["created_at"] ?>
</div>
<br>
<span class="badge <?= $class ?>">
<?= strtoupper($status) ?>
</span>
</div>
<div class="action">
<a
href="../product-detail.html?id=<?= $row["product_id"] ?>"
class="btn view">
Xem
</a>
<?php

if($status=="pending"){
?>
<a
href="../api/order/cancel.php?id=<?= $row["id"] ?>"
class="btn cancel"
onclick="return confirm('Hủy đơn hàng?')">
Hủy
</a>
<?php
}
?>
</div>
</div>
<?php
}
}
?>
</div>
</body>
</html>