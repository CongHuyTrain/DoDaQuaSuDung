<?php
require_once __DIR__ . "/inc/guard.php";

$id = getRequiredId();
if ($id === 0) {
    redirectWithMessage("../../admin/products.php", "error", "ID sản phẩm không hợp lệ.");
}

$check = $conn->prepare("SELECT status FROM products WHERE id=?");
$check->bind_param("i", $id);
$check->execute();
$product = $check->get_result()->fetch_assoc();

if (!$product) {
    redirectWithMessage("../../admin/products.php", "error", "Không tìm thấy sản phẩm #$id.");
}

$stmt = $conn->prepare("UPDATE products SET status='active' WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    redirectWithMessage("../../admin/products.php", "success", "Đã duyệt sản phẩm #$id.");
} else {
    redirectWithMessage("../../admin/products.php", "error", "Duyệt sản phẩm thất bại. Vui lòng thử lại.");
}