<?php
require_once __DIR__ . '/db_connect.php';

$userId = requireLogin();

try {
    $stmt = $pdo->prepare(
        "SELECT p.id, p.title, p.price, p.condition_status, p.image, p.status, p.created_at,
                c.name AS category_name
         FROM products p
         JOIN categories c ON c.id = p.category_id
         WHERE p.user_id = :user_id
         ORDER BY p.created_at DESC"
    );
    $stmt->execute([':user_id' => $userId]);
    $products = $stmt->fetchAll();

    echo json_encode(["success" => true, "data" => $products]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Không thể tải danh sách sản phẩm."]);
}
