<?php
// config.php - Kết nối database (MySQL Workbench 8.0 + XAMPP)
// ──────────────────────────────────────────────────────────────
// MySQL Workbench dùng port 3306 (mặc định)
// Nếu XAMPP đổi port thì sửa dòng DB_PORT bên dưới
// ──────────────────────────────────────────────────────────────

define('DB_HOST', '127.0.0.1');   // dùng IP thay 'localhost' để tránh lỗi socket trên Windows
define('DB_PORT', 3306);           // port mặc định MySQL Workbench
define('DB_USER', 'root');         // user mặc định XAMPP
define('DB_PASS', '');             // mặc định XAMPP để trống; nếu Workbench có pass thì điền vào đây
define('DB_NAME', 'dodaqua_db');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode([
        'error' => 'Kết nối database thất bại: ' . $conn->connect_error,
        'hint'  => 'Kiểm tra: MySQL đang chạy trong XAMPP, đúng port ' . DB_PORT . ', đúng password'
    ]));
}
?>
