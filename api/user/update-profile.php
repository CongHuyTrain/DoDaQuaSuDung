<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập']);
    exit;
}

require_once '../../config/db.php';

$user_id  = (int) $_SESSION['user_id'];
$fullname = trim($_POST['fullname'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$address  = trim($_POST['address'] ?? '');
$gender   = $_POST['gender'] ?? null;
$dob      = trim($_POST['dob'] ?? '');

if ($fullname === '' || $email === '') {
    echo json_encode(['success' => false, 'message' => 'Họ tên và email không được để trống']);
    exit;
}
if (mb_strlen($fullname) > 100) {
    echo json_encode(['success' => false, 'message' => 'Họ tên quá dài']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email không hợp lệ']);
    exit;
}
if (!in_array($gender, ['Nam', 'Nữ', 'Khác'], true)) {
    $gender = null;
}
if ($dob === '') {
    $dob = null;
} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
    echo json_encode(['success' => false, 'message' => 'Ngày sinh không hợp lệ']);
    exit;
}

// Kiểm tra email đã được dùng bởi tài khoản khác chưa
$check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id <> ?");
$check->bind_param("si", $email, $user_id);
$check->execute();
if ($check->get_result()->fetch_assoc()) {
    echo json_encode(['success' => false, 'message' => 'Email này đã được sử dụng bởi tài khoản khác']);
    $check->close();
    exit;
}
$check->close();

$stmt = $conn->prepare(
    "UPDATE users SET fullname=?, email=?, phone=?, address=?, gender=?, dob=? WHERE id=?"
);
$stmt->bind_param("ssssssi", $fullname, $email, $phone, $address, $gender, $dob, $user_id);

if ($stmt->execute()) {
    $_SESSION['fullname'] = $fullname;
    $_SESSION['email']    = $email;
    echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật: ' . $conn->error]);
}
$stmt->close();