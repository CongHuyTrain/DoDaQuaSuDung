<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once "../config/db.php";

//========================
// Lấy tham số
//========================
$keyword     = trim($_GET['q'] ?? '');
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$min_price   = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price   = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 0;
$condition   = trim($_GET['condition'] ?? '');
$sort        = $_GET['sort'] ?? 'newest';

$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

//========================
// Validate
//========================
if ($keyword == "") {
    echo json_encode([
        "success" => false,
        "error" => "Vui lòng nhập từ khóa."
    ]);
    exit;
}

//========================
// ORDER BY
//========================
switch ($sort) {

    case "price_asc":
        $order_by = "p.price ASC";
        break;

    case "price_desc":
        $order_by = "p.price DESC";
        break;

    case "oldest":
        $order_by = "p.created_at ASC";
        break;

    default:
        $order_by = "p.created_at DESC";
}

//========================
// WHERE
//========================
$where = "WHERE p.status='active'
AND (p.title LIKE ? OR p.description LIKE ?)";

$params = [];
$types  = "";

$like = "%{$keyword}%";

$params[] = $like;
$params[] = $like;
$types .= "ss";

if ($category_id > 0) {

    $where .= " AND p.category_id=?";

    $params[] = $category_id;

    $types .= "i";
}

if ($min_price > 0) {

    $where .= " AND p.price>=?";

    $params[] = $min_price;

    $types .= "d";
}

if ($max_price > 0) {

    $where .= " AND p.price<=?";

    $params[] = $max_price;

    $types .= "d";
}

if ($condition != "") {

    $where .= " AND p.condition_item=?";

    $params[] = $condition;

    $types .= "s";
}

//========================
// COUNT
//========================
$count_sql = "
SELECT COUNT(*) total
FROM products p
$where
";

$count_stmt = $conn->prepare($count_sql);

if ($types != "") {
    $count_stmt->bind_param($types, ...$params);
}

$count_stmt->execute();

$total = $count_stmt
            ->get_result()
            ->fetch_assoc()['total'];

//========================
// SELECT
//========================
$sql = "
SELECT
    p.id,
    p.title,
    p.description,
    p.price,
    p.image,
    p.condition_item,
    p.location,
    p.views,
    p.created_at,

    c.name AS category_name,

    u.fullname AS seller_name

FROM products p

LEFT JOIN categories c
ON p.category_id = c.id

LEFT JOIN users u
ON p.user_id = u.id

$where

ORDER BY $order_by

LIMIT ?

OFFSET ?
";

$params[] = $limit;
$params[] = $offset;

$types .= "ii";

$stmt = $conn->prepare($sql);

$stmt->bind_param($types, ...$params);

$stmt->execute();

$result = $stmt->get_result();

$products = [];

while ($row = $result->fetch_assoc()) {

    $row["price_formatted"] =
        number_format($row["price"],0,",",".")." đ";

    $products[] = $row;
}

//========================
// JSON
//========================
echo json_encode([

    "success" => true,

    "keyword" => $keyword,

    "data" => $products,

    "pagination" => [

        "total" => (int)$total,

        "page" => $page,

        "limit" => $limit,

        "total_pages" => ceil($total / $limit)

    ]

], JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();