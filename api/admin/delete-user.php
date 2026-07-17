<?php
require_once __DIR__ . "/inc/guard.php";

$id = getRequiredId();
if ($id === 0) {
    redirectWithMessage("../../admin/users.php", "error", "ID người dùng không hợp lệ.");
}

if ($id === CURRENT_ADMIN_ID) {
    redirectWithMessage("../../admin/users.php", "error", "Bạn không thể tự xóa chính tài khoản của mình.");
}

$check = $conn->prepare("SELECT role FROM users WHERE id=?");
$check->bind_param("i", $id);
$check->execute();
$user = $check->get_result()->fetch_assoc();

if (!$user) {
    redirectWithMessage("../../admin/users.php", "error", "Không tìm thấy người dùng #$id.");
}

if ($user["role"] === "admin") {
    redirectWithMessage("../../admin/users.php", "error", "Không thể xóa một tài khoản quản trị khác.");
}

$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    redirectWithMessage("../../admin/users.php", "success", "Đã xóa người dùng #$id.");
} elseif (isForeignKeyError($conn)) {
    
    redirectWithMessage(
        "../../admin/users.php",
        "error",
        "Không thể xóa người dùng #$id vì còn sản phẩm hoặc đơn hàng liên quan. Hãy \"Khóa\" tài khoản này thay vì xóa."
    );
} else {
    redirectWithMessage("../../admin/users.php", "error", "Xóa người dùng thất bại. Vui lòng thử lại.");
}