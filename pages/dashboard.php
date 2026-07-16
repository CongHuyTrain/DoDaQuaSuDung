<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài khoản của tôi – Đồ Cũ VN</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* ---- Trang tài khoản (dùng chung theme của assets/css/style.css) ---- */
        .db-hero{
            background:linear-gradient(135deg,#2563eb,#1d4ed8);
            color:#fff; padding:44px 0 60px; text-align:center;
        }
        .db-avatar{
            width:64px; height:64px; border-radius:50%;
            background:rgba(255,255,255,.18); border:2px solid rgba(255,255,255,.5);
            display:flex; align-items:center; justify-content:center;
            font-size:1.5rem; font-weight:800; margin:0 auto 12px;
        }
        .db-hero h1{ font-size:1.4rem; font-weight:800; margin:0 0 4px; }
        .db-hero p{ font-size:0.9rem; opacity:.9; margin:0; }

        .db-wrap{ padding:0 0 60px; margin-top:-34px; }

        .db-card{
            background:#fff; border:1px solid #e2e8f0; border-radius:14px;
            padding:24px; margin-bottom:20px;
        }
        .db-card h3{ margin:0 0 14px; font-size:1rem; font-weight:800; }
        .db-info-row{
            display:flex; justify-content:space-between; padding:10px 0;
            border-bottom:1px solid #f1f5f9; font-size:0.92rem;
        }
        .db-info-row:last-child{ border-bottom:none; }
        .db-info-row span:first-child{ color:#64748b; }
        .db-info-row span:last-child{ font-weight:600; color:#1e293b; }

        .db-links{ display:grid; grid-template-columns:repeat(2,1fr); gap:14px; }
        .db-link{
            display:flex; align-items:center; gap:12px;
            padding:16px; border:1px solid #e2e8f0; border-radius:12px;
            text-decoration:none; color:inherit; transition:.15s;
        }
        .db-link:hover{ border-color:#2563eb; background:#eff6ff; }
        .db-link .icon{ font-size:1.4rem; }
        .db-link .lbl{ font-weight:700; font-size:0.92rem; }

        .db-logout{
            display:inline-flex; align-items:center; gap:6px;
            color:#dc2626; font-weight:700; text-decoration:none;
            font-size:0.9rem;
        }
        .db-logout:hover{ text-decoration:underline; }

        @media (max-width:600px){
            .db-links{ grid-template-columns:1fr; }
        }
    </style>
</head>
<body>

<header>
    <div class="header-inner">
        <a class="logo" href="../index.html">Đồ Cũ<span>VN</span></a>
        <nav class="header-nav" style="margin-left:auto;">
            <a href="../index.html" class="btn btn-outline">Trang chủ</a>
            <a href="../products.html" class="btn btn-outline">Sản phẩm</a>
            <a href="logout.php" class="btn btn-primary">Đăng xuất</a>
        </nav>
    </div>
</header>

<section class="db-hero">
    <div class="db-avatar"><?php echo htmlspecialchars(mb_strtoupper(mb_substr($_SESSION['fullname'] ?? $_SESSION['username'], 0, 1))); ?></div>
    <h1>Chào mừng, <?php echo htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username']); ?> 👋</h1>
    <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
</section>

<div class="container db-wrap">

    <div class="db-card">
        <h3>👤 Thông tin tài khoản</h3>
        <div class="db-info-row"><span>Tên đăng nhập</span><span><?php echo htmlspecialchars($_SESSION['username']); ?></span></div>
        <div class="db-info-row"><span>Họ tên</span><span><?php echo htmlspecialchars($_SESSION['fullname'] ?? '—'); ?></span></div>
        <div class="db-info-row"><span>Email</span><span><?php echo htmlspecialchars($_SESSION['email']); ?></span></div>
        <div class="db-info-row"><span>Vai trò</span><span><?php echo htmlspecialchars(ucfirst($_SESSION['role'] ?? 'user')); ?></span></div>
    </div>

    <div class="db-card">
        <h3>🔗 Truy cập nhanh</h3>
        <div class="db-links">
            <a href="../my-products.html" class="db-link">
                <span class="icon">📦</span>
                <span class="lbl">Sản phẩm của tôi</span>
            </a>
            <a href="../add-product.html" class="db-link">
                <span class="icon">➕</span>
                <span class="lbl">Đăng sản phẩm mới</span>
            </a>
            <a href="cart.php" class="db-link">
                <span class="icon">🛒</span>
                <span class="lbl">Giỏ hàng</span>
            </a>
            <a href="my-orders.php" class="db-link">
                <span class="icon">🧾</span>
                <span class="lbl">Đơn hàng của tôi</span>
            </a>
        </div>
    </div>

    <a href="logout.php" class="db-logout">👉 Đăng xuất tài khoản</a>

</div>

</body>
</html>