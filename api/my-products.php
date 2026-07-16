<?php
// api/my-products.php - Danh sách sản phẩm của người dùng đang đăng nhập
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once '../config/db.php';

// Phải đăng nhập mới xem được sản phẩm của mình (giống cách auth-check.php kiểm tra)
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Bạn cần đăng nhập để xem sản phẩm của mình.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// 'deleted' = đã bị xóa hẳn (qua delete.php) nên không hiển thị lại ở đây
$sql = "
SELECT
    p.id,
    p.category_id,
    p.title,
    p.description,
    p.price,
    p.image,
    p.condition_item,
    p.location,
    p.views,
    p.status,
    p.created_at,
    p.updated_at,
    c.name AS category_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.user_id = ?
  AND p.status <> 'deleted'
ORDER BY p.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $row['price_formatted'] = number_format($row['price'], 0, ',', '.') . ' đ';
    $data[] = $row;
}

echo json_encode([
    'success' => true,
    'data'    => $data
], JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();