<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập']);
    exit;
}

require_once '../../config/db.php';

$user_id = (int) $_SESSION['user_id'];

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Không nhận được file ảnh']);
    exit;
}

$file = $_FILES['avatar'];

if ($file['size'] > 3 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'Kích thước ảnh tối đa 3MB']);
    exit;
}

$allowedMime = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
$mime = function_exists('mime_content_type') ? mime_content_type($file['tmp_name']) : $file['type'];

if (!isset($allowedMime[$mime])) {
    echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận ảnh JPG, PNG hoặc WEBP']);
    exit;
}

$uploadDir = __DIR__ . '/../../uploads/avatar/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$ext      = $allowedMime[$mime];
$filename = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
$destPath = $uploadDir . $filename;
$dbPath   = 'uploads/avatar/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    echo json_encode(['success' => false, 'message' => 'Không thể lưu file ảnh lên máy chủ']);
    exit;
}

// Xóa ảnh đại diện cũ (nếu không phải ảnh mặc định) để tránh rác file
$old = $conn->prepare("SELECT avatar FROM users WHERE id = ?");
$old->bind_param("i", $user_id);
$old->execute();
$oldRow = $old->get_result()->fetch_assoc();
$old->close();

if ($oldRow && !empty($oldRow['avatar']) && strpos($oldRow['avatar'], 'default.png') === false) {
    $oldFile = __DIR__ . '/../../' . $oldRow['avatar'];
    if (is_file($oldFile)) {
        @unlink($oldFile);
    }
}

$stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
$stmt->bind_param("si", $dbPath, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        'success'    => true,
        'message'    => 'Cập nhật ảnh đại diện thành công',
        'avatar_url' => '../' . $dbPath,
    ]);
} else {
    // rollback file đã lưu nếu update CSDL thất bại
    @unlink($destPath);
    echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật CSDL']);
}
$stmt->close();