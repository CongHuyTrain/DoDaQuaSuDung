<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

session_start();

$DB_HOST = "localhost";
$DB_NAME = "do_da_qua_su_dung";
$DB_USER = "root";
$DB_PASS = "";

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Kết nối CSDL thất bại."]);
    exit;
}

function requireLogin(): int
{
    // if (!isset($_SESSION['user_id'])) {
    //     http_response_code(401);
    //     echo json_encode(["success" => false, "message" => "Bạn cần đăng nhập."]);
    //     exit;
    // }
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = 1;
    }   
    return (int) $_SESSION['user_id'];
}
