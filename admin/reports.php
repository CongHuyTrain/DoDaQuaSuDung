<?php
require_once "inc/auth.php";

$totalUsers = $conn->query("SELECT COUNT(*) total FROM users")->fetch_assoc()["total"];
$totalProducts = $conn->query("SELECT COUNT(*) total FROM products")->fetch_assoc()["total"];
$totalOrders = $conn->query("SELECT COUNT(*) total FROM orders")->fetch_assoc()["total"];
$totalRevenue = $conn->query("
    SELECT IFNULL(SUM(total_amount),0) total
    FROM orders
    WHERE status='completed'
")->fetch_assoc()["total"];

$activeProducts = $conn->query("SELECT COUNT(*) total FROM products WHERE status='active'")->fetch_assoc()["total"];
$soldProducts = $conn->query("SELECT COUNT(*) total FROM products WHERE status='sold'")->fetch_assoc()["total"];
$pendingProducts = $conn->query("SELECT COUNT(*) total FROM products WHERE status='pending'")->fetch_assoc()["total"];
$blockedUsers = $conn->query("SELECT COUNT(*) total FROM users WHERE status='blocked'")->fetch_assoc()["total"];

$latestOrders = $conn->query("
    SELECT
        o.id,
        u.fullname,
        o.total_amount,
        o.status,
        o.created_at
    FROM orders o
    LEFT JOIN users u ON o.buyer_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Thống kê hệ thống – Đồ Cũ VN Admin</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">

    <?php include "sidebar.php"; ?>

    <main class="admin-main">

        <div class="admin-topbar">
            <div>
                <h1>📈 Thống kê hệ thống</h1>
                <div class="subtitle">Tổng quan số liệu toàn hệ thống</div>
            </div>
        </div>

        <div class="stat-grid">
            <div class="stat-card"><div class="stat-icon moss">👥</div><div><h3>Người dùng</h3><div class="stat-value"><?= $totalUsers ?></div></div></div>
            <div class="stat-card"><div class="stat-icon ember">📦</div><div><h3>Sản phẩm</h3><div class="stat-value"><?= $totalProducts ?></div></div></div>
            <div class="stat-card"><div class="stat-icon gold">🧾</div><div><h3>Đơn hàng</h3><div class="stat-value"><?= $totalOrders ?></div></div></div>
            <div class="stat-card"><div class="stat-icon ink">💰</div><div><h3>Doanh thu</h3><div class="stat-value"><?= money($totalRevenue) ?></div></div></div>
            <div class="stat-card"><div class="stat-icon moss">✅</div><div><h3>Đang bán</h3><div class="stat-value"><?= $activeProducts ?></div></div></div>
            <div class="stat-card"><div class="stat-icon ink">🏷️</div><div><h3>Đã bán</h3><div class="stat-value"><?= $soldProducts ?></div></div></div>
            <div class="stat-card"><div class="stat-icon gold">⏳</div><div><h3>Chờ duyệt</h3><div class="stat-value"><?= $pendingProducts ?></div></div></div>
            <div class="stat-card"><div class="stat-icon ember">🔒</div><div><h3>Tài khoản bị khóa</h3><div class="stat-value"><?= $blockedUsers ?></div></div></div>
        </div>

        <div class="panel">
            <h2 class="panel-title">10 đơn hàng gần nhất</h2>
            <div class="table-scroll">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người mua</th>
                        <th>Tổng tiền</th>
                        <th class="center">Trạng thái</th>
                        <th>Ngày tạo</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($latestOrders->num_rows === 0): ?>
                        <tr class="empty-row"><td colspan="5">Chưa có đơn hàng nào.</td></tr>
                    <?php else: while ($o = $latestOrders->fetch_assoc()):
                        [$label, $cls] = statusBadge($o["status"]);
                    ?>
                        <tr>
                            <td>#<?= (int)$o["id"] ?></td>
                            <td><?= $o["fullname"] !== null ? e($o["fullname"]) : "<em>(đã xóa)</em>" ?></td>
                            <td><?= money($o["total_amount"]) ?></td>
                            <td class="center"><span class="badge <?= $cls ?>"><?= e($label) ?></span></td>
                            <td><?= e($o["created_at"]) ?></td>
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