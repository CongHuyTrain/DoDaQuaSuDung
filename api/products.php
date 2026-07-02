<?php
// api/products.php - Lấy danh sách sản phẩm
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once '../config.php';

// Tham số lọc
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$page        = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit       = isset($_GET['limit']) ? min(50, (int)$_GET['limit']) : 12;
$offset      = ($page - 1) * $limit;
$sort        = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Xác định cách sắp xếp
$order_by = match($sort) {
    'price_asc'  => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'oldest'     => 'p.created_at ASC',
    default      => 'p.created_at DESC',  // newest
};

// Điều kiện lọc theo danh mục
$where = "WHERE p.status = 'active'";
$params = [];
$types  = '';

if ($category_id > 0) {
    $where   .= " AND p.category_id = ?";
    $params[] = $category_id;
    $types   .= 'i';
}

// Đếm tổng số sản phẩm
$count_sql = "SELECT COUNT(*) as total FROM products p $where";
$count_stmt = $conn->prepare($count_sql);
if ($types) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_assoc()['total'];

// Lấy danh sách sản phẩm có JOIN danh mục và người đăng
$sql = "
    SELECT 
        p.id,
        p.title,
        p.price,
        p.image,
        p.condition_item,
        p.location,
        p.created_at,
        c.name  AS category_name,
        u.full_name AS seller_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    JOIN users u      ON p.user_id = u.id
    $where
    ORDER BY $order_by
    LIMIT ? OFFSET ?
";

$params[] = $limit;
$params[] = $offset;
$types   .= 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $row['price_formatted'] = number_format($row['price'], 0, ',', '.') . ' đ';
    $products[] = $row;
}

echo json_encode([
    'success'      => true,
    'data'         => $products,
    'pagination'   => [
        'total'        => (int)$total,
        'page'         => $page,
        'limit'        => $limit,
        'total_pages'  => (int)ceil($total / $limit),
    ],
]);

$conn->close();
?>
