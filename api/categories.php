<?php
// api/categories.php - Lấy danh sách danh mục
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once '../config.php';

$result = $conn->query("SELECT id, name FROM categories ORDER BY name");
$categories = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'success' => true,
    'data'    => $categories,
]);

$conn->close();
?>
