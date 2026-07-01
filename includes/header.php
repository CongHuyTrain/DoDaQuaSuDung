<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Đồ Đã Qua Sử Dụng</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">

<div class="container">

<a class="navbar-brand fw-bold text-warning" href="../index.php">

<i class="fa-solid fa-recycle"></i>

Đồ Cũ

</a>

<form class="d-flex w-50">

<input

class="form-control"

type="search"

placeholder="Tìm sản phẩm...">

</form>

<div>

<a href="../pages/my-orders.php" class="btn btn-light">

<i class="fa-solid fa-bag-shopping"></i>

Đơn mua

</a>

<a href="../pages/transactions.php" class="btn btn-light">

<i class="fa-solid fa-store"></i>

Đơn bán

</a>

<a href="../logout.php" class="btn btn-warning text-white">

<i class="fa-solid fa-right-from-bracket"></i>

Đăng xuất

</a>

</div>

</div>

</nav>