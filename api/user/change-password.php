<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập']);
    exit;
}

require_once '../../config/db.php';

$user_id = (int) $_SESSION['user_id'];
$current = $_POST['current_password'] ?? '';
$new     = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($current === '' || $new === '' || $confirm === '') {
    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin']);
    exit;
}
if (mb_strlen($new) < 6) {
    echo json_encode(['success' => false, 'message' => 'Mật khẩu mới phải từ 6 ký tự trở lên']);
    exit;
}
if ($new !== $confirm) {
    echo json_encode(['success' => false, 'message' => 'Xác nhận mật khẩu không khớp']);
    exit;
}

$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row || !password_verify($current, $row['password'])) {
    echo json_encode(['success' => false, 'message' => 'Mật khẩu hiện tại không đúng']);
    exit;
}
if (password_verify($new, $row['password'])) {
    echo json_encode(['success' => false, 'message' => 'Mật khẩu mới phải khác mật khẩu hiện tại']);
    exit;
}

$newHash = password_hash($new, PASSWORD_DEFAULT);
$upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$upd->bind_param("si", $newHash, $user_id);

if ($upd->execute()) {
    echo json_encode(['success' => true, 'message' => 'Đổi mật khẩu thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật mật khẩu']);
}
$upd->close();