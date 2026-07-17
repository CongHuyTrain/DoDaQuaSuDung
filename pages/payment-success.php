<?php
$order = $_GET["order"] ?? "";
// Giỏ hàng có thể có sản phẩm từ nhiều người bán -> createOrder() có thể
// tạo ra nhiều đơn cùng lúc, order sẽ là danh sách id cách nhau dấu phẩy.
$orderIds = array_filter(array_map("trim", explode(",", $order)));
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

<?php if (!empty($orderIds)): ?>
<p><?= count($orderIds) > 1 ? "Mã đơn hàng:" : "Mã đơn hàng:" ?></p>

<h2>
<?php foreach ($orderIds as $i => $id): ?>
#<?= htmlspecialchars($id) ?><?= $i < count($orderIds) - 1 ? ", " : "" ?>
<?php endforeach; ?>
</h2>
<?php endif; ?>

<a href="my-orders.php">

Xem đơn hàng

</a>

</div>

</body>

</html>