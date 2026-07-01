<?php
include("../config/database.php");
include("../includes/auth.php");

$user_id = $_SESSION['user_id'];

$status = isset($_GET['status']) ? $_GET['status'] : '';

$sql = "
SELECT
    orders.*,
    products.title,
    products.price,
    users.fullname,
    users.phone,
    users.avatar,
    (
        SELECT image_url
        FROM product_images
        WHERE product_images.product_id = products.id
        LIMIT 1
    ) AS image

FROM orders

JOIN products
ON products.id = orders.product_id

JOIN users
ON users.id = orders.buyer_id

WHERE orders.seller_id = '$user_id'
";

if($status!=""){
    $sql .= " AND orders.status='$status'";
}

$sql .= " ORDER BY orders.created_at DESC";

$result=mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>

<meta charset="UTF-8">

<title>Quản lý giao dịch</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<link rel="stylesheet"
href="../assets/css/orders.css">

</head>

<body>

<?php include("../includes/header.php"); ?>

<div class="container py-5">

<div class="row">

<?php include("../includes/sidebar.php"); ?>

<div class="col-lg-9">

<h2 class="mb-4">

<i class="fa-solid fa-store text-warning"></i>

Quản lý giao dịch

</h2>
<div class="mb-4">

<a href="transactions.php"
class="btn btn-outline-warning">

Tất cả

</a>

<a href="?status=pending"
class="btn btn-outline-secondary">

Chờ xác nhận

</a>

<a href="?status=accepted"
class="btn btn-outline-primary">

Đã xác nhận

</a>

<a href="?status=completed"
class="btn btn-outline-success">

Hoàn thành

</a>

<a href="?status=cancelled"
class="btn btn-outline-danger">

Đã hủy

</a>

</div>
<?php

while($row=mysqli_fetch_assoc($result)){

?>

<div class="card shadow-sm border-0 rounded-4 mb-4">

<div class="card-body">

<div class="row align-items-center">

<div class="col-md-2">

<img

src="../uploads/<?= $row['image']?>"

class="img-fluid rounded">

</div>

<div class="col-md-3">

<h5>

<?= $row['title']?>

</h5>

<p class="text-secondary mb-1">

Người mua

</p>

<strong>

<?= $row['fullname']?>

</strong>

<br>

<?= $row['phone']?>

</div>

<div class="col-md-2">

<h5 class="text-danger">

<?= number_format($row['price'])?>

đ

</h5>

</div>

<div class="col-md-2">
    <?php

switch($row['status']){

case "pending":

echo '<span class="badge bg-warning">

Chờ xác nhận

</span>';

break;

case "accepted":

echo '<span class="badge bg-primary">

Đã xác nhận

</span>';

break;

case "completed":

echo '<span class="badge bg-success">

Hoàn thành

</span>';

break;

case "cancelled":

echo '<span class="badge bg-danger">

Đã hủy

</span>';

break;

case "rejected":

echo '<span class="badge bg-dark">

Từ chối

</span>';

break;

}

?>

</div>

<div class="col-md-3 text-end">
    <?php

if($row['status']=="pending"){

?>

<form
action="../api/order/accept.php"
method="POST"
class="d-inline">

<input
type="hidden"
name="order_id"
value="<?= $row['id']?>">

<button
class="btn btn-success">

<i class="fa fa-check"></i>

Xác nhận

</button>

</form>

<form
action="../api/order/reject.php"
method="POST"
class="d-inline">

<input
type="hidden"
name="order_id"
value="<?= $row['id']?>">

<button
class="btn btn-outline-danger">

<i class="fa fa-xmark"></i>

Từ chối

</button>

</form>

<?php

}
elseif($row['status']=="accepted"){

?>

<form
action="../api/order/complete.php"
method="POST">

<input
type="hidden"
name="order_id"
value="<?= $row['id']?>">

<button
class="btn btn-primary">

<i class="fa-solid fa-truck"></i>

Hoàn tất

</button>

</form>

<?php

}
elseif($row['status']=="completed"){

?>

<button
class="btn btn-success">

<i class="fa fa-circle-check"></i>

Đã bán

</button>

<?php

}
elseif($row['status']=="rejected"){

?>

<button
class="btn btn-dark">

Đã từ chối

</button>

<?php

}
elseif($row['status']=="cancelled"){

?>

<button
class="btn btn-secondary">

Người mua đã hủy

</button>

<?php

}

?>

</div>

</div>

</div>

</div>

<?php

}

?>

</div>
</div>

</div>

</div>

</body>

</html>
