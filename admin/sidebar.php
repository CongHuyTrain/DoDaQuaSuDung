<?php
$current = basename($_SERVER["PHP_SELF"]);
?>

<div class="sidebar">

    <h2>🛒 Đồ Cũ VN</h2>

    <a href="dashboard.php" class="<?= $current=="dashboard.php" ? "active" : "" ?>">
         Dashboard
    </a>

    <a href="users.php" class="<?= $current=="users.php" ? "active" : "" ?>">
         Người dùng
    </a>

    <a href="products.php" class="<?= $current=="products.php" ? "active" : "" ?>">
         Sản phẩm
    </a>

    <a href="orders.php" class="<?= $current=="orders.php" ? "active" : "" ?>">
         Đơn hàng
    </a>

    <a href="reports.php" class="<?= $current=="reports.php" ? "active" : "" ?>">
         Báo cáo
    </a>

    <hr>

    <a href="../pages/logout.php" class="logout">
         Đăng xuất
    </a>

</div>

<style>
.sidebar{
    width:240px;
    min-height:100vh;
    background:#1e293b;
    position:fixed;
    left:0;
    top:0;
    padding:25px;
    box-sizing:border-box;
}

.sidebar h2{
    color:#fff;
    margin-bottom:35px;
    text-align:center;
}

.sidebar a{
    display:block;
    padding:13px 15px;
    margin-bottom:10px;
    color:#fff;
    text-decoration:none;
    border-radius:8px;
    transition:.25s;
    font-size:15px;
}

.sidebar a:hover{
    background:#334155;
}

.sidebar a.active{
    background:#2563eb;
}

.sidebar .logout{
    color:#ffb4b4;
}

.sidebar hr{
    border:0;
    border-top:1px solid #475569;
    margin:20px 0;
}
</style>