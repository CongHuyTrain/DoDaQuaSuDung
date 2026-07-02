<?php
// api/product-detail.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
require_once '../config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID không hợp lệ']);
    exit;
}

// Lấy sản phẩm
$sql = "
    SELECT p.id, p.title, p.description, p.price, p.image,
           p.condition_item, p.location, p.status, p.created_at,
           p.category_id,
           c.name   AS category_name,
           u.id          AS seller_id,
           u.full_name   AS seller_name,
           u.phone       AS seller_phone,
           u.email       AS seller_email
    FROM products p
    JOIN categories c ON p.category_id = c.id
    JOIN users u      ON p.user_id = u.id
    WHERE p.id = ? AND p.status = 'active'
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Sản phẩm không tồn tại']);
    exit;
}

$product['price_formatted'] = number_format($product['price'], 0, ',', '.') . ' đ';

// Lấy danh sách ảnh từ bảng product_images
$img_sql = "SELECT image_url FROM product_images WHERE product_id = ? ORDER BY sort_order ASC";
$img_stmt = $conn->prepare($img_sql);
$img_stmt->bind_param('i', $id);
$img_stmt->execute();
$img_result = $img_stmt->get_result();

$images = [];
while ($row = $img_result->fetch_assoc()) {
    $images[] = $row['image_url'];
}

// Nếu không có ảnh trong bảng product_images nhưng có image đơn → dùng làm fallback
if (empty($images) && !empty($product['image'])) {
    $images[] = $product['image'];
}

// Sản phẩm liên quan
$rel_sql = "
    SELECT p.id, p.title, p.price, p.image, p.condition_item, p.location,
           c.name AS category_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.category_id = ? AND p.id != ? AND p.status = 'active'
    ORDER BY p.created_at DESC
    LIMIT 4
";
$rel = $conn->prepare($rel_sql);
$rel->bind_param('ii', $product['category_id'], $id);
$rel->execute();
$rel_result = $rel->get_result();

$related = [];
while ($r = $rel_result->fetch_assoc()) {
    $r['price_formatted'] = number_format($r['price'], 0, ',', '.') . ' đ';
    $related[] = $r;
}

echo json_encode([
    'success' => true,
    'data'    => $product,
    'images'  => $images,
    'related' => $related,
]);

$conn->close();
?>
