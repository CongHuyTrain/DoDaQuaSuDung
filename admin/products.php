<?php
require_once "inc/auth.php";

$keyword = trim($_GET["keyword"] ?? "");

if ($keyword !== "") {
    $stmt = $conn->prepare("
        SELECT
            p.*,
            c.name AS category_name,
            u.fullname AS seller_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN users u ON p.user_id = u.id
        WHERE p.title LIKE ?
        ORDER BY p.created_at DESC
    ");
    $like = "%" . $keyword . "%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("
        SELECT
            p.*,
            c.name AS category_name,
            u.fullname AS seller_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC
    ");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quản lý sản phẩm – Đồ Cũ VN Admin</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">

    <?php include "sidebar.php"; ?>

    <main class="admin-main">

        <div class="admin-topbar">
            <div>
                <h1>📦 Quản lý sản phẩm</h1>
                <div class="subtitle"><?= $result->num_rows ?> sản phẩm<?= $keyword !== "" ? " khớp với \"" . e($keyword) . "\"" : "" ?></div>
            </div>
            <a href="../add-product.html" class="btn btn-primary">+ Thêm sản phẩm</a>
        </div>

        <div class="panel">

            <?php flashMessage(); ?>

            <form method="GET" class="filter-bar">
                <input type="text" name="keyword" placeholder="Tìm sản phẩm theo tên..." value="<?= e($keyword) ?>">
                <button class="btn btn-outline">Tìm</button>
                <?php if ($keyword !== ""): ?>
                    <a href="products.php" class="btn btn-neutral btn-sm">Xóa lọc</a>
                <?php endif; ?>
            </form>

            <div class="table-scroll">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên</th>
                        <th>Danh mục</th>
                        <th>Người đăng</th>
                        <th>Giá</th>
                        <th class="center">Trạng thái</th>
                        <th class="center">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($result->num_rows === 0): ?>
                        <tr class="empty-row"><td colspan="8">Không có sản phẩm nào.</td></tr>
                    <?php else: while ($p = $result->fetch_assoc()):
                        [$label, $cls] = statusBadge($p["status"]);
                    ?>
                        <tr>
                            <td>#<?= (int)$p["id"] ?></td>
                            <td><img class="thumb" src="../<?= e($p["image"]) ?>" alt="<?= e($p["title"]) ?>"></td>
                            <td><?= e($p["title"]) ?></td>
                            <td><?= $p["category_name"] !== null ? e($p["category_name"]) : "<em>—</em>" ?></td>
                            <td><?= $p["seller_name"] !== null ? e($p["seller_name"]) : "<em>(tài khoản đã xóa)</em>" ?></td>
                            <td><?= money($p["price"]) ?></td>
                            <td class="center"><span class="badge <?= $cls ?>"><?= e($label) ?></span></td>
                            <td class="center">
                                <div class="action-group">
                                <?php if ($p["status"] === "pending"): ?>
                                    <a class="btn btn-sm btn-success"
                                       href="../api/admin/approve.php?id=<?= (int)$p["id"] ?>">
                                        Duyệt
                                    </a>
                                    <a class="btn btn-sm btn-danger"
                                       href="../api/admin/reject.php?id=<?= (int)$p["id"] ?>"
                                       onclick="return confirmAction('Từ chối sản phẩm này?')">
                                        Từ chối
                                    </a>
                                <?php endif; ?>
                                    <a class="btn btn-sm btn-outline" href="edit-product.php?id=<?= (int)$p["id"] ?>">
                                        Sửa
                                    </a>
                                    <a class="btn btn-sm btn-neutral"
                                       onclick="return confirmAction('Xóa sản phẩm này? Hành động không thể hoàn tác.')"
                                       href="../api/admin/delete-product.php?id=<?= (int)$p["id"] ?>">
                                        Xóa
                                    </a>
                                </div>
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