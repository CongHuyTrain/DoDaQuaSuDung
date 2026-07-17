<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

require_once '../config/db.php';

$user_id = (int) $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    session_destroy();
    header("Location: login.html");
    exit;
}

// Đánh dấu tất cả đã đọc khi người dùng bấm nút
if (isset($_GET['mark_all'])) {
    $mark = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $mark->bind_param("i", $user_id);
    $mark->execute();
    $mark->close();
    header("Location: notifications.php");
    exit;
}

$list = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
$list->bind_param("i", $user_id);
$list->execute();
$notifications = $list->get_result()->fetch_all(MYSQLI_ASSOC);
$list->close();

$unreadCount = 0;
if ($r = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = $user_id AND is_read = 0")) {
    $unreadCount = (int) ($r->fetch_assoc()['cnt'] ?? 0);
}

$activePage = 'notifications';

function timeAgoVi($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'Vừa xong';
    if ($diff < 3600) return floor($diff / 60) . ' phút trước';
    if ($diff < 86400) return floor($diff / 3600) . ' giờ trước';
    if ($diff < 2592000) return floor($diff / 86400) . ' ngày trước';
    return date('d/m/Y', strtotime($datetime));
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo – Đồ Cũ VN</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body{ background:#f5f6fa; }
        .acc-wrap{
            max-width:1120px; margin:24px auto 60px; padding:0 16px;
            display:grid; grid-template-columns:240px 1fr; gap:24px; align-items:start;
        }
        .acc-sidebar{ background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:20px 0; position:sticky; top:20px; }
        .acc-sidebar-user{ display:flex; flex-direction:column; align-items:center; text-align:center; padding:0 16px 18px; border-bottom:1px solid #f1f5f9; margin-bottom:8px; }
        .acc-sidebar-avatar{ width:64px; height:64px; border-radius:50%; overflow:hidden; background:linear-gradient(135deg,#2563eb,#1d4ed8); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:1.4rem; margin-bottom:10px; }
        .acc-sidebar-avatar img{ width:100%; height:100%; object-fit:cover; }
        .acc-sidebar-username{ font-weight:800; font-size:0.95rem; color:#1e293b; max-width:190px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .acc-sidebar-edit{ font-size:0.8rem; color:#64748b; text-decoration:none; margin-top:4px; }
        .acc-sidebar-edit:hover{ color:#2563eb; }
        .acc-nav-item{ display:flex; align-items:center; gap:10px; padding:11px 20px; color:#334155; text-decoration:none; font-size:0.92rem; font-weight:600; }
        .acc-nav-item:hover{ background:#f8fafc; color:#2563eb; }
        .acc-nav-item.active{ color:#2563eb; background:#eff6ff; border-right:3px solid #2563eb; }
        .acc-nav-icon{ font-size:1.05rem; width:20px; text-align:center; }
        .acc-nav-badge{ margin-left:auto; background:#dc2626; color:#fff; font-size:0.72rem; font-weight:800; border-radius:999px; padding:2px 7px; }
        .acc-nav-parent{ cursor:default; font-weight:700; color:#1e293b; }
        .acc-nav-sub{ display:flex; flex-direction:column; }
        .acc-nav-sub a{ padding:9px 20px 9px 50px; color:#64748b; text-decoration:none; font-size:0.88rem; font-weight:600; }
        .acc-nav-sub a:hover{ color:#2563eb; }
        .acc-nav-logout{ color:#dc2626 !important; }
        .acc-nav-logout:hover{ background:#fef2f2; }

        .acc-card{ background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:28px 32px; }
        .acc-card-head{ border-bottom:1px solid #f1f5f9; padding-bottom:16px; margin-bottom:8px; display:flex; align-items:center; justify-content:space-between; gap:12px; }
        .acc-card-head h2{ margin:0 0 4px; font-size:1.15rem; font-weight:800; color:#1e293b; }
        .acc-card-head p{ margin:0; font-size:0.85rem; color:#64748b; }
        .acc-mark-all{ background:#fff; border:1px solid #cbd5e1; color:#334155; padding:8px 16px; border-radius:8px; font-size:0.85rem; font-weight:700; text-decoration:none; white-space:nowrap; }
        .acc-mark-all:hover{ border-color:#2563eb; color:#2563eb; }

        .noti-item{ display:flex; gap:14px; padding:16px 4px; border-bottom:1px solid #f1f5f9; }
        .noti-item:last-child{ border-bottom:none; }
        .noti-item.unread{ background:#f8fafc; margin:0 -12px; padding:16px 12px; border-radius:10px; }
        .noti-icon{ font-size:1.3rem; flex-shrink:0; }
        .noti-title{ font-weight:700; color:#1e293b; font-size:0.92rem; margin-bottom:3px; }
        .noti-content{ color:#64748b; font-size:0.86rem; line-height:1.5; }
        .noti-time{ color:#94a3b8; font-size:0.78rem; margin-top:6px; }
        .noti-empty{ text-align:center; padding:60px 0; color:#94a3b8; }
        .noti-empty .big{ font-size:2.4rem; margin-bottom:10px; }

        @media (max-width:900px){
            .acc-wrap{ grid-template-columns:1fr; }
            .acc-sidebar{ position:static; }
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

<div class="acc-wrap">

    <?php include __DIR__ . '/inc/account-sidebar.php'; ?>

    <div class="acc-content">
        <div class="acc-card">
            <div class="acc-card-head">
                <div>
                    <h2>Thông Báo</h2>
                    <p><?php echo $unreadCount; ?> thông báo chưa đọc</p>
                </div>
                <?php if ($unreadCount > 0): ?>
                    <a href="notifications.php?mark_all=1" class="acc-mark-all">Đánh dấu đã đọc tất cả</a>
                <?php endif; ?>
            </div>

            <?php if (empty($notifications)): ?>
                <div class="noti-empty">
                    <div class="big">🔔</div>
                    <p>Bạn chưa có thông báo nào</p>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $n): ?>
                    <div class="noti-item <?php echo !$n['is_read'] ? 'unread' : ''; ?>">
                        <div class="noti-icon"><?php echo $n['is_read'] ? '📭' : '📬'; ?></div>
                        <div>
                            <div class="noti-title"><?php echo htmlspecialchars($n['title']); ?></div>
                            <?php if (!empty($n['content'])): ?>
                                <div class="noti-content"><?php echo htmlspecialchars($n['content']); ?></div>
                            <?php endif; ?>
                            <div class="noti-time"><?php echo timeAgoVi($n['created_at']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>