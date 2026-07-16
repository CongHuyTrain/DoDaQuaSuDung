<?php
require_once "inc/auth.php";

$result = $conn->query("
    SELECT
        o.id,
        o.total_amount,
        o.status,
        o.created_at,
        u1.fullname AS buyer_name,
        u2.fullname AS seller_name,
        p.title,
        p.image
    FROM orders o
    LEFT JOIN users u1 ON o.buyer_id = u1.id
    LEFT JOIN users u2 ON o.seller_id = u2.id
    LEFT JOIN order_details od ON o.id = od.order_id
    LEFT JOIN products p ON od.product_id = p.id
    ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quản lý đơn hàng – Đồ Cũ VN Admin</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">

    <?php include "sidebar.php"; ?>

    <main class="admin-main">

        <div class="admin-topbar">
            <div>
                <h1>🧾 Quản lý đơn hàng</h1>
                <div class="subtitle"><?= $result->num_rows ?> đơn hàng</div>
            </div>
        </div>

        <div class="panel">
            <div class="table-scroll">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Người mua</th>
                        <th>Người bán</th>
                        <th>Tổng tiền</th>
                        <th class="center">Trạng thái</th>
                        <th>Ngày đặt</th>
                        <th class="center">Chi tiết</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($result->num_rows === 0): ?>
                        <tr class="empty-row"><td colspan="9">Chưa có đơn hàng nào.</td></tr>
                    <?php else: while ($o = $result->fetch_assoc()):
                        [$label, $cls] = statusBadge($o["status"]);
                    ?>
                        <tr>
                            <td>#<?= (int)$o["id"] ?></td>
                            <td>
                                <?php if (!empty($o["image"])): ?>
                                    <img class="thumb" src="../<?= e($o["image"]) ?>" alt="">
                                <?php else: ?>
                                    <div class="thumb" style="display:flex;align-items:center;justify-content:center;">📦</div>
                                <?php endif; ?>
                            </td>
                            <td><?= $o["title"] !== null ? e($o["title"]) : "<em>—</em>" ?></td>
                            <td><?= $o["buyer_name"] !== null ? e($o["buyer_name"]) : "<em>(đã xóa)</em>" ?></td>
                            <td><?= $o["seller_name"] !== null ? e($o["seller_name"]) : "<em>(đã xóa)</em>" ?></td>
                            <td><?= money($o["total_amount"]) ?></td>
                            <td class="center"><span class="badge <?= $cls ?>"><?= e($label) ?></span></td>
                            <td><?= e($o["created_at"]) ?></td>
                            <td class="center">
                                <a class="btn btn-sm btn-outline" href="transactions.php?id=<?= (int)$o["id"] ?>">
                                    Xem
                                </a>
                            </td>
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