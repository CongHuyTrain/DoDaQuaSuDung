<?php
require_once "inc/auth.php";

if (!isset($_GET["id"])) {
    die("Thiếu mã đơn hàng.");
}

$order_id = (int)$_GET["id"];

$sql = "
SELECT
    o.*,
    p.id AS product_id,
    p.title,
    p.image,
    u1.fullname AS buyer_name,
    u2.fullname AS seller_name
FROM orders o
LEFT JOIN order_details od ON o.id = od.order_id
LEFT JOIN products p ON od.product_id = p.id
LEFT JOIN users u1 ON o.buyer_id = u1.id
LEFT JOIN users u2 ON o.seller_id = u2.id
WHERE o.id = ?
LIMIT 1
";
// Đổi các INNER JOIN cũ -> LEFT JOIN: nếu người mua/bán hoặc sản phẩm liên quan
// đã bị xóa khỏi hệ thống, trang vẫn hiển thị được đơn hàng thay vì "Không tìm thấy".

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Không tìm thấy đơn hàng.");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chi tiết đơn hàng – Đồ Cũ VN Admin</title>
<link rel="stylesheet" href="../assets/css/admin.css">
<style>
.order-card{ display:flex; gap:28px; flex-wrap:wrap; }
.order-image{ width:240px; flex-shrink:0; }
.order-image img{ width:100%; border-radius:var(--radius-md); box-shadow:var(--shadow-card); }
.order-info{ flex:1; min-width:260px; }
.order-price{ font-size:28px; font-weight:800; color:var(--ember-dark); margin:6px 0 18px; }
.order-line{ margin:10px 0; font-size:14px; }
.order-line b{ color:var(--charcoal); font-weight:600; }
</style>
</head>
<body>

<div class="admin-wrapper">

    <?php include "sidebar.php"; ?>

    <main class="admin-main">

        <div class="admin-topbar">
            <div>
                <h1>Chi tiết đơn hàng #<?= (int)$order["id"] ?></h1>
            </div>
            <a href="orders.php" class="btn btn-outline">← Quay lại</a>
        </div>

        <div class="panel order-card">

            <div class="order-image">
                <?php if (!empty($order["image"])): ?>
                    <img src="../<?= e($order["image"]) ?>" alt="">
                <?php else: ?>
                    <div class="thumb" style="width:100%;height:240px;display:flex;align-items:center;justify-content:center;font-size:40px;">📦</div>
                <?php endif; ?>
            </div>

            <div class="order-info">
                <h2 style="margin-top:0;"><?= $order["title"] !== null ? e($order["title"]) : "<em>(sản phẩm đã bị xóa)</em>" ?></h2>
                <div class="order-price"><?= money($order["total_amount"]) ?></div>

                <div class="order-line"><b>Người mua:</b> <?= $order["buyer_name"] !== null ? e($order["buyer_name"]) : "<em>(tài khoản đã xóa)</em>" ?></div>
                <div class="order-line"><b>Người bán:</b> <?= $order["seller_name"] !== null ? e($order["seller_name"]) : "<em>(tài khoản đã xóa)</em>" ?></div>
                <div class="order-line"><b>Người nhận:</b> <?= e($order["receiver_name"]) ?></div>
                <div class="order-line"><b>SĐT:</b> <?= e($order["receiver_phone"]) ?></div>
                <div class="order-line"><b>Địa chỉ:</b> <?= e($order["receiver_address"]) ?></div>
                <div class="order-line"><b>Ghi chú:</b><br><?= nl2br(e($order["note"])) ?></div>
                <div class="order-line"><b>Ngày đặt:</b> <?= e($order["created_at"]) ?></div>
                <div class="order-line"><b>Thanh toán:</b> <?= e($order["payment_method"]) ?></div>
                <div class="order-line"><b>Trạng thái thanh toán:</b> <?= e(strtoupper($order["payment_status"] ?? "")) ?></div>

                <div class="order-line">
                    <?php [$label, $cls] = statusBadge($order["status"]); ?>
                    <span class="badge <?= $cls ?>"><?= e($label) ?></span>
                </div>

                <div style="margin-top:20px;" class="action-group">
                <?php if ($order["status"] === "pending"): ?>
                    <a class="btn btn-success"
                       href="../api/order/accept.php?id=<?= (int)$order["id"] ?>"
                       onclick="return confirmAction('Chấp nhận đơn hàng?')">
                        Chấp nhận
                    </a>
                    <a class="btn btn-danger"
                       href="../api/order/reject.php?id=<?= (int)$order["id"] ?>"
                       onclick="return confirmAction('Từ chối đơn hàng?')">
                        Từ chối
                    </a>
                <?php endif; ?>

                <?php if ($order["status"] === "accepted"): ?>
                    <a class="btn btn-primary"
                       href="../api/order/complete.php?id=<?= (int)$order["id"] ?>"
                       onclick="return confirmAction('Đã giao hàng thành công?')">
                        Hoàn thành
                    </a>
                <?php endif; ?>
                </div>
            </div>

        </div>

    </main>
</div>

<script src="../assets/js/admin.js"></script>
</body>
</html>