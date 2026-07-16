<?php
$current = basename($_SERVER["PHP_SELF"]);

function navActive($file, $current)
{
    return $file === $current ? "active" : "";
}
?>
<button class="sidebar-toggle" id="admin-sidebar-toggle" onclick="toggleAdminSidebar()" aria-label="Mở menu quản trị">☰</button>

<aside class="sidebar" id="admin-sidebar">

    <div class="sidebar-logo">Đồ Cũ<span>VN</span></div>
    <div class="sidebar-section-label">Quản trị</div>

    <a href="dashboard.php" class="admin-link <?= navActive("dashboard.php", $current) ?>">
        <span class="icon">📊</span> Dashboard
    </a>

    <a href="users.php" class="admin-link <?= navActive("users.php", $current) ?>">
        <span class="icon">👥</span> Người dùng
    </a>

    <a href="products.php" class="admin-link <?= navActive("products.php", $current) ?>">
        <span class="icon">📦</span> Sản phẩm
    </a>

    <a href="orders.php" class="admin-link <?= navActive("orders.php", $current) ?>">
        <span class="icon">🧾</span> Đơn hàng
    </a>

    <a href="reports.php" class="admin-link <?= navActive("reports.php", $current) ?>">
        <span class="icon">📈</span> Báo cáo
    </a>

    <div class="sidebar-spacer"></div>

    <a href="../index.html" class="sidebar-backsite">← Về trang chính</a>
    <hr>
    <a href="../pages/logout.php" class="admin-link logout">
        <span class="icon">🚪</span> Đăng xuất
    </a>

</aside>