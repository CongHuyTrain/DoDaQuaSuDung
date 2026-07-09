<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

$product_id = isset($_GET["product_id"]) ? (int)$_GET["product_id"] : 0;

if ($product_id <= 0) {
    die("Sản phẩm không hợp lệ.");
}

$sql = "
SELECT
    p.*,
    c.name AS category_name,
    u.fullname
FROM products p
LEFT JOIN categories c
ON p.category_id = c.id
LEFT JOIN users u
ON p.user_id = u.id
WHERE p.id = ?
LIMIT 1
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i",$product_id);

$stmt->execute();

$product = $stmt->get_result()->fetch_assoc();

if(!$product){
    die("Không tìm thấy sản phẩm.");
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>

<meta charset="UTF-8">

<title>Đặt mua sản phẩm</title>

<meta name="viewport" content="width=device-width,initial-scale=1">

<link rel="stylesheet" href="../assets/css/style.css?v=1783406030">

<style>

body{

background:#f5f7fb;

font-family:Arial;

}

.order-wrap{

max-width:1100px;

margin:40px auto;

display:grid;

grid-template-columns:420px 1fr;

gap:35px;

}

.card{

background:#fff;

border-radius:14px;

padding:25px;

box-shadow:0 8px 25px rgba(0,0,0,.06);

}

.product-img{

width:100%;

height:320px;

object-fit:cover;

border-radius:12px;

background:#eee;

}

.price{

font-size:34px;

font-weight:bold;

color:#ff5722;

margin:15px 0;

}

.item{

margin-bottom:18px;

}

.item label{

display:block;

margin-bottom:8px;

font-weight:600;

}

.item input,

.item textarea{

width:100%;

padding:12px;

border:1px solid #ddd;

border-radius:8px;

font-size:15px;

}

.item textarea{

height:120px;

resize:none;

}

.btn{

width:100%;

padding:15px;

border:none;

background:#2563eb;

color:#fff;

font-size:17px;

font-weight:bold;

border-radius:10px;

cursor:pointer;

}

.btn:hover{

background:#1d4ed8;

}

.info{

margin-top:20px;

color:#555;

line-height:1.8;

}

.badge{

display:inline-block;

padding:5px 12px;

background:#22c55e;

color:#fff;

border-radius:30px;

margin-bottom:12px;

font-size:13px;

}

</style>

</head>

<body>

<div class="order-wrap">

<div class="card">

<?php
$imgPath = !empty($product["image"]) ? '../' . ltrim($product["image"], '/') : '';
?>
<img
src="<?= htmlspecialchars($imgPath) ?>"
class="product-img"
onerror="this.style.background='#eee'; this.src='';">


<div class="badge">

<?= htmlspecialchars($product["condition_item"]) ?>

</div>

<h2>

<?= htmlspecialchars($product["title"]) ?>

</h2>

<div class="price">

<?= number_format($product["price"],0,",",".") ?> đ

</div>

<div class="info">

<b>Danh mục:</b>

<?= htmlspecialchars($product["category_name"]) ?>

<br><br>

<b>Người bán:</b>

<?= htmlspecialchars($product["fullname"]) ?>

<br><br>

<b>Địa điểm:</b>

<?= htmlspecialchars($product["location"]) ?>

<br><br>

<?= nl2br(htmlspecialchars($product["description"])) ?>

</div>

</div>

<div class="card">

<h2 style="margin-bottom:25px;">

Xác nhận mua hàng

</h2>

<form id="orderForm">

<input

type="hidden"

name="product_id"

value="<?= $product["id"] ?>">

<div class="item">

<label>

Họ và tên

</label>

<input

type="text"

name="receiver_name"

required>

</div>

<div class="item">

<label>

Số điện thoại

</label>

<input

type="text"

name="receiver_phone"

required>

</div>

<div class="item">

<label>

Địa chỉ nhận hàng

</label>

<textarea

name="receiver_address"

required></textarea>

</div>

<div class="item">

<label>

Ghi chú

</label>

<textarea

name="note"></textarea>

</div>

<button

class="btn"

type="submit">

🛒 Xác nhận đặt mua

</button>

</form>

</div>

</div>

<script>
document.getElementById("orderForm").addEventListener("submit",async function(e){

    e.preventDefault();

    const form=new FormData(this);

    const res=await fetch("../api/order/create.php",{

        method:"POST",

        body:form

    });

    const data=await res.json();

    alert(data.message);

    if(data.success){

        location.href="my-orders.php";

    }

});
</script>
</body>

</html>