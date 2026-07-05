<?php
    session_start();

    require_once "../config/db.php";

    if(!isset($_SESSION["admin_id"])) header("Location: login.php");

    $totalUsers = $conn->query("SELECT COUNT(*) total FROM users")->fetch_assoc()['total'];

    $totalProducts = $conn->query("SELECT COUNT(*) total FROM products")->fetch_assoc()['total'];

    $totalOrders = $conn->query("SELECT COUNT(*) total FROM orders")->fetch_assoc()['total'];

    $totalRevenue = $conn->query("
        SELECT IFNULL(SUM(total_amount),0) total
        FROM orders
        WHERE status='completed'
    ")->fetch_assoc()['total'];

    $newUsers = $conn->query("
        SELECT *
        FROM users
        ORDER BY created_at DESC
        LIMIT 5
    ");

    $newOrders = $conn->query("
        SELECT
        o.id,
        o.total_amount,
        o.status,
        u.fullname buyer
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

.sidebar{
width:240px;
background:#1e293b;
min-height:100vh;
color:white;
padding:25px;
}
.sidebar h2{
margin-bottom:30px;
}
.sidebar a{
display:block;
padding:12px;
margin:8px 0;
text-decoration:none;
color:white;
border-radius:8px;
}
.sidebar a:hover{
background:#334155;
}
.content{
flex:1;
padding:35px;
}
.cards{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:20px;
margin-bottom:35px;
}
.card{
background:white;
padding:25px;
border-radius:15px;
box-shadow:0 8px 20px rgba(0,0,0,.06);
}
.card h3{
margin:0;
color:#666;
font-size:15px;
}
.card h1{
margin-top:15px;
font-size:38px;
color:#2563eb;
}
.table{
background:white;
border-radius:15px;
padding:20px;
margin-bottom:30px;
box-shadow:0 8px 20px rgba(0,0,0,.06);
}
table{
width:100%;
border-collapse:collapse;
}
th{
background:#2563eb;
color:white;
padding:12px;
}
td{
padding:12px;
border-bottom:1px solid #eee;
}
.badge{
padding:5px 12px;
border-radius:20px;
color:white;
font-size:12px;
}
.pending{
background:#f59e0b;
}
.completed{
background:#16a34a;
}
.confirmed{
background:#2563eb;
}
.cancelled{
background:#dc2626;
}

        </style>
    </head>
<body>

<div class="wrapper">
<div class="sidebar">
    <h2>ADMIN</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="users.php">Người dùng</a>
    <a href="products.php">Sản phẩm</a>
    <a href="orders.php">Đơn hàng</a>
    <a href="reports.php">Báo cáo</a>
</div>

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
<div class="table">
    <h2>Người dùng mới</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Họ tên</th>
        <th>Email</th>
    </tr>
<?php while($u=$newUsers->fetch_assoc()){ ?>
    <tr>
        <td><?= $u["id"] ?></td>
        <td><?= $u["fullname"] ?></td>
        <td><?= $u["email"] ?></td>
    </tr>
        <?php } ?>
        </table>
        </div>
        <div class="table">
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
    <td><?= $o["buyer"] ?></td>
    <td><?= number_format($o["total_amount"],0,",",".") ?>đ</td>
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