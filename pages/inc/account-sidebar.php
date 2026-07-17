<?php
/**
 * Sidebar tài khoản dùng chung (kiểu Shopee).
 * Cần có sẵn trước khi include:
 *   $user          -> mảng thông tin user (từ bảng users)
 *   $unreadCount   -> số thông báo chưa đọc (int)
 *   $activePage    -> 'profile' | 'password' | 'notifications' | 'products' | 'orders' | 'cart'
 */
$avatarRelPath = $user['avatar'] ?: 'uploads/avatar/default.png';
$avatarFsPath  = __DIR__ . '/../../' . $avatarRelPath;
$avatarSrc     = is_file($avatarFsPath) ? '../' . $avatarRelPath : null;
$initial       = mb_strtoupper(mb_substr($user['fullname'] ?: $user['username'], 0, 1));
?>
<aside class="acc-sidebar">
    <div class="acc-sidebar-user">
        <div class="acc-sidebar-avatar">
            <?php if ($avatarSrc): ?>
                <img src="<?php echo htmlspecialchars($avatarSrc); ?>" alt="avatar">
            <?php else: ?>
                <span><?php echo htmlspecialchars($initial); ?></span>
            <?php endif; ?>
        </div>
        <div class="acc-sidebar-username">
            <?php echo htmlspecialchars($user['fullname'] ?: $user['username']); ?>
        </div>
        <a href="dashboard.php" class="acc-sidebar-edit">✏️ Sửa hồ sơ</a>
    </div>

    <nav class="acc-sidebar-nav">
        <a href="notifications.php" class="acc-nav-item <?php echo $activePage === 'notifications' ? 'active' : ''; ?>">
            <span class="acc-nav-icon">🔔</span>
            <span>Thông báo</span>
            <?php if ($unreadCount > 0): ?>
                <span class="acc-nav-badge"><?php echo $unreadCount > 9 ? '9+' : $unreadCount; ?></span>
            <?php endif; ?>
        </a>

        <div class="acc-nav-group <?php echo in_array($activePage, ['profile','password']) ? 'open' : ''; ?>">
            <div class="acc-nav-item acc-nav-parent">
                <span class="acc-nav-icon">👤</span>
                <span>Tài Khoản Của Tôi</span>
            </div>
            <div class="acc-nav-sub">
                <a href="dashboard.php" class="<?php echo $activePage === 'profile' ? 'active' : ''; ?>">Hồ sơ</a>
                <a href="dashboard.php?tab=password" class="<?php echo $activePage === 'password' ? 'active' : ''; ?>">Đổi mật khẩu</a>
            </div>
        </div>

        <a href="../my-products.html" class="acc-nav-item <?php echo $activePage === 'products' ? 'active' : ''; ?>">
            <span class="acc-nav-icon">📦</span>
            <span>Sản phẩm của tôi</span>
        </a>

        <a href="my-orders.php" class="acc-nav-item <?php echo $activePage === 'orders' ? 'active' : ''; ?>">
            <span class="acc-nav-icon">🧾</span>
            <span>Đơn mua</span>
        </a>

        <a href="cart.php" class="acc-nav-item <?php echo $activePage === 'cart' ? 'active' : ''; ?>">
            <span class="acc-nav-icon">🛒</span>
            <span>Giỏ hàng</span>
        </a>

        <a href="logout.php" class="acc-nav-item acc-nav-logout">
            <span class="acc-nav-icon">🚪</span>
            <span>Đăng xuất</span>
        </a>
    </nav>
</aside>