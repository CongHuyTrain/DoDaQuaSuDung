<?php
require_once __DIR__ . '/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Phương thức không hợp lệ."]);
    exit;
}

$userId = requireLogin();

$productId = $_POST['product_id'] ?? null;
if ($productId === null) {
    $body = json_decode(file_get_contents('php://input'), true);
    $productId = $body['product_id'] ?? null;
}

if (!ctype_digit((string) $productId)) {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => "Sản phẩm không hợp lệ."]);
    exit;
}

$check = $pdo->prepare("SELECT image FROM products WHERE id = :id AND user_id = :user_id");
$check->execute([':id' => $productId, ':user_id' => $userId]);
$existing = $check->fetch();

if (!$existing) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Bạn không có quyền xóa sản phẩm này."]);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $productId, ':user_id' => $userId]);

    if ($existing['image'] && file_exists(__DIR__ . '/../../' . $existing['image'])) {
        unlink(__DIR__ . '/../../' . $existing['image']);
    }

    echo json_encode(["success" => true, "message" => "Đã xóa sản phẩm."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Không thể xóa sản phẩm."]);
}
