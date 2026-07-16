<?php
require_once __DIR__ . "/inc/guard.php";

$id = getRequiredId();
if ($id === 0) {
    redirectWithMessage("../../admin/users.php", "error", "ID người dùng không hợp lệ.");
}

if ($id === CURRENT_ADMIN_ID) {
    redirectWithMessage("../../admin/users.php", "error", "Bạn không thể tự khóa chính tài khoản của mình.");
}

$check = $conn->prepare("SELECT role, status FROM users WHERE id=?");
$check->bind_param("i", $id);
$check->execute();
$user = $check->get_result()->fetch_assoc();

if (!$user) {
    redirectWithMessage("../../admin/users.php", "error", "Không tìm thấy người dùng #$id.");
}

if ($user["role"] === "admin") {
    redirectWithMessage("../../admin/users.php", "error", "Không thể khóa một tài khoản quản trị khác.");
}

$stmt = $conn->prepare("UPDATE users SET status='blocked' WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    redirectWithMessage("../../admin/users.php", "success", "Đã khóa tài khoản #$id.");
} else {
    redirectWithMessage("../../admin/users.php", "error", "Khóa tài khoản thất bại. Vui lòng thử lại.");
}