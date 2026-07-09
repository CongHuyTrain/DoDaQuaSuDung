<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}
require_once "../config/db.php";
$user_id = $_SESSION["user_id"];
$sql = "
SELECT
o.id,
o.status,
o.total_amount,
o.created_at,
p.id AS product_id,
p.title,
p.image,
u.fullname AS seller_name
FROM orders o
INNER JOIN order_details od
ON o.id=od.order_id
INNER JOIN products p
ON od.product_id=p.id
INNER JOIN users u
ON o.seller_id=u.id
WHERE o.buyer_id=?
ORDER BY o.created_at DESC
";
$stmt=$conn->prepare($sql);
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result=$stmt->get_result();

$statusLabel = [
    "pending"   => "Chờ xác nhận",
    "accepted"  => "Đã xác nhận",
    "confirmed" => "Đã xác nhận",
    "completed" => "Hoàn tất",
    "cancelled" => "Đã hủy",
    "rejected"  => "Đã từ chối",
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đơn hàng của tôi – Đồ Cũ VN</title>
<link rel="stylesheet" href="../assets/css/style.css?v=1783406030">
</head>
<body>

<header>
    <div class="header-inner">
        <a class="logo" href="../index.html">Đồ Cũ<span>VN</span></a>
        <div class="header-search">
            <input type="text" id="header-search-input" placeholder="Tìm kiếm sản phẩm..."
                   onkeydown="if(event.key==='Enter') goSearch()">
            <button class="btn btn-primary" onclick="goSearch()">Tìm</button>
        </div>
        <nav class="header-nav">
            <a href="../index.html" class="btn btn-outline">Trang chủ</a>
            <a href="../products.html" class="btn btn-outline">Sản phẩm</a>

            <a href="login.html" class="btn btn-primary" id="nav-guest" style="display:none;">Đăng nhập</a>

            <div class="nav-user-area" id="nav-user">
                <a href="my-orders.php" class="nav-cart-btn" id="nav-cart" title="Đơn hàng của tôi">
                    🛒
                    <span class="nav-cart-badge" id="nav-cart-count" style="display:none;">0</span>
                </a>
                <span class="nav-username" id="nav-username">👤 <?= htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username'] ?? '') ?></span>
                <a href="logout.php" class="btn btn-outline" id="nav-logout">Đăng xuất</a>
            </div>
        </nav>
    </div>
</header>

<div class="container">
    <div class="orders-page-title">🧾 Đơn hàng của tôi</div>

    <?php if ($result->num_rows == 0): ?>
        <div class="empty">
            <div class="empty-icon">📭</div>
            <p>Bạn chưa có đơn hàng nào.</p>
            <a href="../products.html" class="btn btn-primary" style="display:inline-block; margin-top:16px;">Mua ngay</a>
        </div>
    <?php else: ?>
        <div class="order-list">
        <?php while ($row = $result->fetch_assoc()):
            $status = $row["status"];
            $class  = $status === "confirmed" ? "accepted" : $status;
            $label  = $statusLabel[$status] ?? ucfirst($status);
            $imgSrc = !empty($row["image"]) ? '../' . ltrim($row["image"], '/') : '';
        ?>
            <div class="order-card">
                <img class="order-img" src="<?= htmlspecialchars($imgSrc) ?>"
                     onerror="this.style.background='#f1f5f9'; this.src='';">
                <div class="order-body">
                    <div class="order-title"><?= htmlspecialchars($row["title"]) ?></div>
                    <div class="order-seller">Người bán: <strong><?= htmlspecialchars($row["seller_name"]) ?></strong></div>
                    <div class="order-price"><?= number_format($row["total_amount"], 0, ",", ".") ?> đ</div>
                    <div class="order-date">Ngày đặt: <?= date("d/m/Y H:i", strtotime($row["created_at"])) ?></div>
                    <span class="order-status <?= $class ?>"><?= htmlspecialchars($label) ?></span>
                </div>
                <div class="order-actions">
                    <a href="../product-detail.html?id=<?= $row["product_id"] ?>" class="btn btn-outline">Xem sản phẩm</a>
                    <?php if ($status === "pending"): ?>
                        <a href="../api/order/cancel.php?id=<?= $row["id"] ?>" class="btn btn-danger"
                           onclick="return confirm('Hủy đơn hàng này?')">Hủy đơn</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    function goSearch() {
        const q = document.getElementById('header-search-input').value.trim();
        if (q) location.href = `../search.html?q=${encodeURIComponent(q)}`;
    }
</script>
</body>
</html>