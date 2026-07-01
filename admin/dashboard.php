<?php
include("../config/database.php");
include("../includes/auth.php");

// Kiểm tra quyền admin
if ($_SESSION['role_id'] != 1) {
    header("Location: ../index.php");
    exit();
}

// Thống kê
$user = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM users"));
$product = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM products"));
$order = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM orders"));
$report = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM reports"));
?>

<!DOCTYPE html>
<html lang="vi">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Admin Dashboard</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<link
rel="stylesheet"
href="../assets/css/admin.css">

</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main">

<h2 class="mb-4">

Dashboard

</h2>

<div class="row g-4">

<div class="col-md-3">

<div class="card shadow-sm p-4">

<h5><i class="fa fa-users text-primary"></i> Người dùng</h5>

<h2><?= $user[0] ?></h2>

</div>

</div>

<div class="col-md-3">

<div class="card shadow-sm p-4">

<h5><i class="fa fa-box text-success"></i> Sản phẩm</h5>

<h2><?= $product[0] ?></h2>

</div>

</div>

<div class="col-md-3">

<div class="card shadow-sm p-4">

<h5><i class="fa fa-cart-shopping text-warning"></i> Đơn hàng</h5>

<h2><?= $order[0] ?></h2>

</div>

</div>

<div class="col-md-3">

<div class="card shadow-sm p-4">

<h5><i class="fa fa-flag text-danger"></i> Báo cáo</h5>

<h2><?= $report[0] ?></h2>

</div>

</div>

</div>

<div class="card mt-5 shadow-sm p-4">

<h5 class="mb-4">

Thống kê hệ thống

</h5>

<canvas id="chart"></canvas>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

new Chart(document.getElementById('chart'),{

type:'bar',

data:{

labels:['Users','Products','Orders','Reports'],

datasets:[{

label:'Số lượng',

data:[
<?= $user[0] ?>,
<?= $product[0] ?>,
<?= $order[0] ?>,
<?= $report[0] ?>
]

}]

}

});

</script>

</body>

</html>