<?php
require_once "inc/auth.php";

$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()["total"];
$totalProducts = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()["total"];
$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()["total"];

$result = $conn->query("
    SELECT IFNULL(SUM(total_amount),0) AS total
    FROM orders
    WHERE status='completed'
");
if (!$result) {
    die($conn->error);
}
$totalRevenue = $result->fetch_assoc()["total"];

$newUsers = $conn->query("
    SELECT id, fullname, email, created_at
    FROM users
    ORDER BY created_at DESC
    LIMIT 5
");

$newOrders = $conn->query("
    SELECT
        o.id,
        o.total_amount,
        o.status,
        u.fullname AS buyer
    FROM orders o
    LEFT JOIN users u ON o.buyer_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard – Đồ Cũ VN Admin</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">

    <?php include "sidebar.php"; ?>

    <main class="admin-main">

        <div class="admin-topbar">
            <div>
                <h1>📊 Dashboard</h1>
                <div class="subtitle">Tổng quan hoạt động của Đồ Cũ VN</div>
            </div>
        </div>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon moss">👥</div>
                <div>
                    <h3>Người dùng</h3>
                    <div class="stat-value"><?= $totalUsers ?></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon ember">📦</div>
                <div>
                    <h3>Sản phẩm</h3>
                    <div class="stat-value"><?= $totalProducts ?></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon gold">🧾</div>
                <div>
                    <h3>Đơn hàng</h3>
                    <div class="stat-value"><?= $totalOrders ?></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon ink">💰</div>
                <div>
                    <h3>Doanh thu</h3>
                    <div class="stat-value"><?= money($totalRevenue) ?></div>
                </div>
            </div>
        </div>

        <div class="panel">
            <h2 class="panel-title">Người dùng mới</h2>
            <div class="table-scroll">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Ngày tạo</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($newUsers->num_rows === 0): ?>
                        <tr class="empty-row"><td colspan="4">Chưa có người dùng nào.</td></tr>
                    <?php else: while ($u = $newUsers->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= (int)$u["id"] ?></td>
                            <td><?= e($u["fullname"]) ?></td>
                            <td><?= e($u["email"]) ?></td>
                            <td><?= e($u["created_at"]) ?></td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <h2 class="panel-title">Đơn hàng mới</h2>
            <div class="table-scroll">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người mua</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($newOrders->num_rows === 0): ?>
                        <tr class="empty-row"><td colspan="4">Chưa có đơn hàng nào.</td></tr>
                    <?php else: while ($o = $newOrders->fetch_assoc()):
                        [$label, $cls] = statusBadge($o["status"]); ?>
                        <tr>
                            <td>#<?= (int)$o["id"] ?></td>
                            <td><?= $o["buyer"] !== null ? e($o["buyer"]) : "<em>(tài khoản đã xóa)</em>" ?></td>
                            <td><?= money($o["total_amount"]) ?></td>
                            <td><span class="badge <?= $cls ?>"><?= e($label) ?></span></td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<script src="../assets/js/admin.js"></script>
</body>
</html>