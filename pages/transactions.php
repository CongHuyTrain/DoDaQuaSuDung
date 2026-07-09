<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
require_once "../config/db.php";
$seller_id = $_SESSION["user_id"];

$sql = "
SELECT
o.id,
o.status,
o.total_amount,
o.note,
o.receiver_name,
o.receiver_phone,
o.receiver_address,
o.created_at,
p.id AS product_id,
p.title,
p.image,
u.fullname AS buyer_name
FROM orders o
INNER JOIN order_details od
ON o.id=od.order_id
INNER JOIN products p
ON od.product_id=p.id
INNER JOIN users u
ON o.buyer_id=u.id
WHERE o.seller_id=?
ORDER BY o.created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$seller_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý giao dịch</title>
<link rel="stylesheet" href="../assets/css/style.css?v=1783406030">
<style>

body{
background:#f3f5f9;
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
margin-bottom:30px;
}

.card{
background:#fff;
border-radius:14px;
padding:20px;
margin-bottom:20px;
display:flex;
gap:20px;
box-shadow:0 8px 24px rgba(0,0,0,.06);
}

.card img{
width:170px;
height:170px;
object-fit:cover;
border-radius:12px;
}

.info{
flex:1;
}

.info h3{
margin-bottom:10px;
}

.price{
font-size:26px;
font-weight:bold;
color:#ff5722;
margin:12px 0;
}

.line{
margin:6px 0;
color:#555;
}

.badge{
display:inline-block;
padding:6px 14px;
border-radius:20px;
font-size:13px;
font-weight:bold;
color:#fff;
margin-top:10px;
}

.pending{
background:#f59e0b;
}

.confirmed{
background:#2563eb;
}
.completed{
background:#16a34a;
}

.cancelled{
background:#ef4444;
}

.rejected{
background:#dc2626;
}

.action{
width:210px;
display:flex;
flex-direction:column;
gap:10px;
justify-content:center;
}

.btn{
padding:12px;
border-radius:8px;
text-decoration:none;
text-align:center;
font-weight:bold;
color:#fff;
}

.accept{
background:#16a34a;
}

.reject{
background:#dc2626;
}

.complete{
background:#2563eb;
}

.view{
background:#6b7280;
}

.empty{
background:#fff;
padding:80px;
border-radius:12px;
text-align:center;
}

</style>
</head>
<body>
<div class="container">
<div class="title">
Quản lý giao dịch
</div>
<?php
if($result->num_rows==0){
?>
<div class="empty">

<h2>
Chưa có giao dịch nào.
</h2>
</div>

<?php
}else{
while($row=$result->fetch_assoc()){

?>

<div class="card">
<img src="<?= htmlspecialchars(!empty($row["image"]) ? '../' . ltrim($row["image"], '/') : '') ?>" onerror="this.style.background='#eee'; this.src='';">
<div class="info">
<h3>
<?= htmlspecialchars($row["title"]) ?>
</h3>
<div class="price">

<?= number_format($row["total_amount"],0,",",".") ?> đ
</div>

<div class="line">
<b>Người mua:</b>

<?= htmlspecialchars($row["buyer_name"]) ?>
</div>

<div class="line">
<b>Người nhận:</b>
<?= htmlspecialchars($row["receiver_name"]) ?>

</div>
<div class="line">

<b>SĐT:</b>
<?= htmlspecialchars($row["receiver_phone"]) ?>
</div>
<div class="line">

<b>Địa chỉ:</b>
<?= htmlspecialchars($row["receiver_address"]) ?>
</div>
<div class="line">
<b>Ghi chú:</b>
<?= nl2br(htmlspecialchars($row["note"])) ?>

</div>
<div class="line">
<b>Ngày đặt:</b>
<?= $row["created_at"] ?>
</div>
<span class="badge <?= $row["status"] ?>">
<?= strtoupper($row["status"]) ?>
</span>
</div>
<div class="action">
<a
class="btn view"
href="../product-detail.html?id=<?= $row["product_id"] ?>">
Xem sản phẩm
</a>
<?php
if($row["status"]=="pending"){
?>
<a
class="btn accept"
href="../api/order/accept.php?id=<?= $row["id"] ?>"
onclick="return confirm('Xác nhận bán?')">
 Chấp nhận
</a>
<a
class="btn reject"
href="../api/order/reject.php?id=<?= $row["id"] ?>"
onclick="return confirm('Từ chối đơn hàng?')">
 Từ chối
</a>
<?php
}
?>
<?php
if($row["status"]=="confirmed"){
?>
<a
class="btn complete"
href="../api/order/complete.php?id=<?= $row["id"] ?>"
onclick="return confirm('Đã giao hàng thành công?')">
🎉 Hoàn thành
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