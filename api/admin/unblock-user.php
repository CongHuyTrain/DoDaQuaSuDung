<?php
require_once __DIR__ . "/inc/guard.php";

$id = getRequiredId();
if ($id === 0) {
    redirectWithMessage("../../admin/users.php", "error", "ID người dùng không hợp lệ.");
}

$check = $conn->prepare("SELECT status FROM users WHERE id=?");
$check->bind_param("i", $id);
$check->execute();
$user = $check->get_result()->fetch_assoc();

if (!$user) {
    redirectWithMessage("../../admin/users.php", "error", "Không tìm thấy người dùng #$id.");
}

$stmt = $conn->prepare("UPDATE users SET status='active' WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    redirectWithMessage("../../admin/users.php", "success", "Đã mở khóa tài khoản #$id.");
} else {
    redirectWithMessage("../../admin/users.php", "error", "Mở khóa tài khoản thất bại. Vui lòng thử lại.");
}