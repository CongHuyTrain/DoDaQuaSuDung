<?php
// api/search.php - Tìm kiếm sản phẩm
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once '../config/db.php';

$keyword     = isset($_GET['q']) ? trim($_GET['q']) : '';
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$min_price   = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price   = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 0;
$condition   = isset($_GET['condition']) ? $_GET['condition'] : '';
$page        = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit       = 12;
$offset      = ($page - 1) * $limit;

if (strlen($keyword) < 1) {
    echo json_encode(['success' => false, 'error' => 'Vui lòng nhập từ khóa tìm kiếm']);
    exit;
}

$where   = "WHERE p.status = 'active' AND (p.title LIKE ? OR p.description LIKE ?)";
$like    = "%$keyword%";
$params  = [$like, $like];
$types   = 'ss';

if ($category_id > 0) {
    $where   .= " AND p.category_id = ?";
    $params[] = $category_id;
    $types   .= 'i';
}

if ($min_price > 0) {
    $where   .= " AND p.price >= ?";
    $params[] = $min_price;
    $types   .= 'd';
}

if ($max_price > 0) {
    $where   .= " AND p.price <= ?";
    $params[] = $max_price;
    $types   .= 'd';
}

$allowed_conditions = ['new', 'like_new', 'good', 'fair'];
if ($condition && in_array($condition, $allowed_conditions)) {
    $where   .= " AND p.condition_item = ?";
    $params[] = $condition;
    $types   .= 's';
}

// Đếm tổng kết quả
$count_sql  = "SELECT COUNT(*) as total FROM products p $where";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param($types, ...$params);
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_assoc()['total'];

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
        u.fullname AS seller_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    JOIN users u      ON p.user_id = u.id
    $where
    ORDER BY p.created_at DESC
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
    'success'    => true,
    'keyword'    => $keyword,
    'data'       => $products,
    'pagination' => [
        'total'       => (int)$total,
        'page'        => $page,
        'limit'       => $limit,
        'total_pages' => (int)ceil($total / $limit),
    ],
]);

$conn->close();
?>