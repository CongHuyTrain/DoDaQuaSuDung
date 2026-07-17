<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
require_once "../config/db.php";
$buyer_id = $_SESSION["user_id"];



$sql = "
SELECT
o.id AS order_id,
o.status,
o.total_amount,
o.note,
o.receiver_name,
o.receiver_phone,
o.receiver_address,
o.payment_method,
o.payment_status,
o.created_at,
od.product_id,
od.quantity,
od.price,
p.title,
p.image,
u.fullname AS seller_name
FROM orders o
INNER JOIN order_details od
ON o.id = od.order_id
INNER JOIN products p
ON od.product_id = p.id
INNER JOIN users u
ON o.seller_id = u.id
WHERE o.buyer_id = ?
ORDER BY o.created_at DESC, o.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $buyer_id);
$stmt->execute();
$result = $stmt->get_result();


$orders = [];

while ($row = $result->fetch_assoc()) {

    $oid = $row["order_id"];

    if (!isset($orders[$oid])) {
        $orders[$oid] = [
            "id"                => $row["order_id"],
            "status"            => $row["status"],
            "total_amount"      => $row["total_amount"],
            "note"              => $row["note"],
            "receiver_name"     => $row["receiver_name"],
            "receiver_phone"    => $row["receiver_phone"],
            "receiver_address"  => $row["receiver_address"],
            "payment_method"    => $row["payment_method"],
            "payment_status"    => $row["payment_status"],
            "created_at"        => $row["created_at"],
            "seller_name"       => $row["seller_name"],
            "items"             => []
        ];
    }

    $orders[$oid]["items"][] = [
        "product_id" => $row["product_id"],
        "title"      => $row["title"],
        "image"      => $row["image"],
        "quantity"   => $row["quantity"],
        "price"      => $row["price"]
    ];

}

$statusLabel = [
    "pending"   => "Chờ xác nhận",
    "accepted"  => "Đã xác nhận",
    "completed" => "Hoàn thành",
    "cancelled" => "Đã hủy",
    "rejected"  => "Đã từ chối"
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Đơn hàng của tôi – Đồ Cũ VN</title>
<link rel="stylesheet" href="../assets/css/style.css">

<style>
body{ background:#f3f5f9; }
.orders-wrap{ padding:24px 0 60px; }
.orders-title{ font-size:1.4rem; font-weight:800; margin:6px 0 20px; }

.order-card{
    background:#fff; border:1px solid #e2e8f0; border-radius:14px;
    padding:20px; margin-bottom:20px;
}
.order-head{
    display:flex; justify-content:space-between; align-items:center;
    padding-bottom:14px; margin-bottom:14px; border-bottom:1px solid #f1f5f9;
    flex-wrap:wrap; gap:10px;
}
.order-head .oid{ font-weight:800; color:#1e293b; }
.order-head .seller{ color:#64748b; font-size:0.88rem; }

.badge{
    display:inline-block; padding:6px 14px; border-radius:20px;
    font-size:0.78rem; font-weight:700; color:#fff;
}
.badge.pending{ background:#f59e0b; }
.badge.accepted{ background:#2563eb; }
.badge.completed{ background:#16a34a; }
.badge.cancelled{ background:#94a3b8; }
.badge.rejected{ background:#dc2626; }

.order-item{
    display:flex; gap:14px; align-items:center; padding:10px 0;
}
.order-item img{
    width:60px; height:60px; object-fit:cover; border-radius:10px;
    border:1px solid #e2e8f0;
}
.order-item .name{ font-weight:700; color:#1e293b; font-size:0.92rem; }
.order-item .qty{ color:#64748b; font-size:0.85rem; }
.order-item .sub{ margin-left:auto; font-weight:800; color:#f97316; }

.order-info{
    margin-top:12px; padding-top:12px; border-top:1px solid #f1f5f9;
    font-size:0.86rem; color:#64748b; line-height:1.7;
}

.order-foot{
    display:flex; justify-content:space-between; align-items:center;
    margin-top:14px; padding-top:14px; border-top:1px solid #f1f5f9;
    flex-wrap:wrap; gap:10px;
}
.order-foot .total{ font-weight:800; color:#1e293b; }
.order-foot .total span{ color:#f97316; font-size:1.1rem; }

.btn-cancel{
    background:#fef2f2; color:#dc2626; border:none;
    padding:9px 16px; border-radius:8px; cursor:pointer;
    font-size:0.85rem; font-weight:700;
}
.btn-cancel:hover{ background:#fee2e2; }

.empty{
    padding:80px 20px; text-align:center; color:#94a3b8;
    background:#fff; border-radius:14px; font-size:0.95rem;
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
            <a href="cart.php" class="btn btn-outline">Giỏ hàng</a>
            <a href="logout.php" class="btn btn-primary">Đăng xuất</a>
        </nav>
    </div>
</header>

<div class="container orders-wrap">

    <h1 class="orders-title">📦 Đơn hàng của tôi</h1>

    <?php if (empty($orders)): ?>

        <div class="empty">Bạn chưa có đơn hàng nào.</div>

    <?php else: ?>

        <?php foreach ($orders as $order): ?>

            <div class="order-card">

                <div class="order-head">
                    <div>
                        <div class="oid">Đơn hàng #<?= $order["id"] ?></div>
                        <div class="seller">Người bán: <?= htmlspecialchars($order["seller_name"]) ?></div>
                    </div>
                    <span class="badge <?= htmlspecialchars($order["status"]) ?>">
                        <?= htmlspecialchars($statusLabel[$order["status"]] ?? $order["status"]) ?>
                    </span>
                </div>

                <?php foreach ($order["items"] as $item): ?>
                    <div class="order-item">
                        <img
                            src="<?= htmlspecialchars(!empty($item["image"]) ? '../' . ltrim($item["image"], '/') : '') ?>"
                            onerror="this.style.background='#eee'; this.src='';">
                        <div>
                            <div class="name"><?= htmlspecialchars($item["title"]) ?></div>
                            <div class="qty">
                                <?= number_format($item["price"], 0, ",", ".") ?> đ
                                &nbsp;×&nbsp;<?= (int)$item["quantity"] ?>
                            </div>
                        </div>
                        <div class="sub">
                            <?= number_format($item["price"] * $item["quantity"], 0, ",", ".") ?> đ
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="order-info">
                    <div><b>Người nhận:</b> <?= htmlspecialchars($order["receiver_name"]) ?></div>
                    <div><b>SĐT:</b> <?= htmlspecialchars($order["receiver_phone"]) ?></div>
                    <div><b>Địa chỉ:</b> <?= htmlspecialchars($order["receiver_address"]) ?></div>
                    <?php if (!empty($order["note"])): ?>
                        <div><b>Ghi chú:</b> <?= nl2br(htmlspecialchars($order["note"])) ?></div>
                    <?php endif; ?>
                    <div><b>Thanh toán:</b> <?= htmlspecialchars($order["payment_method"]) ?>
                        (<?= htmlspecialchars($order["payment_status"]) ?>)</div>
                    <div><b>Ngày đặt:</b> <?= htmlspecialchars($order["created_at"]) ?></div>
                </div>

                <div class="order-foot">
                    <div class="total">
                        Tổng cộng: <span><?= number_format($order["total_amount"], 0, ",", ".") ?> đ</span>
                    </div>

                    <?php if ($order["status"] == "pending"): ?>
                        <a
                            class="btn-cancel"
                            style="text-decoration:none; display:inline-block;"
                            href="../api/order/cancel.php?id=<?= $order["id"] ?>"
                            onclick="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                            Hủy đơn
                        </a>
                    <?php endif; ?>
                </div>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

</body>
</html>