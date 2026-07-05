<?php
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Phương thức không hợp lệ."]);
    exit;
}

session_start();

if (!isset($_SESSION["user_id"])) {
    exit("Chưa đăng nhập");
}

$userId = $_SESSION["user_id"];

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

$check = $conn->prepare("
    SELECT image
    FROM products
    WHERE id = ? AND user_id = ?
");

$check->bind_param("ii", $productId, $userId);
$check->execute();

$result = $check->get_result();
$existing = $result->fetch_assoc();

if (!$existing) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Bạn không có quyền xóa sản phẩm này."]);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $productId, $userId);
    $stmt->execute();

    if ($existing['image'] && file_exists(__DIR__ . '/../../' . $existing['image'])) {
        unlink(__DIR__ . '/../../' . $existing['image']);
    }

    echo json_encode(["success" => true, "message" => "Đã xóa sản phẩm."]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Không thể xóa sản phẩm."]);
}
