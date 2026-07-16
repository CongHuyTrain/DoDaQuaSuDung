<?php
require_once "inc/auth.php";

$result = $conn->query("
    SELECT *
    FROM users
    ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quản lý người dùng – Đồ Cũ VN Admin</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">

    <?php include "sidebar.php"; ?>

    <main class="admin-main">

        <div class="admin-topbar">
            <div>
                <h1>👥 Quản lý người dùng</h1>
                <div class="subtitle"><?= $result->num_rows ?> tài khoản</div>
            </div>
        </div>

        <div class="panel">
            <div class="table-scroll">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Avatar</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Vai trò</th>
                        <th class="center">Trạng thái</th>
                        <th class="center">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($result->num_rows === 0): ?>
                        <tr class="empty-row"><td colspan="8">Chưa có người dùng nào.</td></tr>
                    <?php else: while ($u = $result->fetch_assoc()):
                        $avatar = !empty($u["avatar"]) ? "../" . $u["avatar"] : "../assets/images/avatar.png";
                        [$label, $cls] = statusBadge($u["status"]);
                    ?>
                        <tr>
                            <td>#<?= (int)$u["id"] ?></td>
                            <td><img class="thumb round" src="<?= e($avatar) ?>" alt="avatar"></td>
                            <td><?= e($u["fullname"]) ?></td>
                            <td><?= e($u["email"]) ?></td>
                            <td><?= e($u["phone"]) ?></td>
                            <td><?= e(strtoupper($u["role"])) ?></td>
                            <td class="center"><span class="badge <?= $cls ?>"><?= e($label) ?></span></td>
                            <td class="center">
                                <div class="action-group">
                                <?php if ($u["status"] === "active"): ?>
                                    <a class="btn btn-sm btn-danger"
                                       href="../api/admin/block-user.php?id=<?= (int)$u["id"] ?>"
                                       onclick="return confirmAction('Khóa tài khoản này?')">
                                        Khóa
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-sm btn-success"
                                       href="../api/admin/unblock-user.php?id=<?= (int)$u["id"] ?>"
                                       onclick="return confirmAction('Mở khóa tài khoản này?')">
                                        Mở khóa
                                    </a>
                                <?php endif; ?>
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