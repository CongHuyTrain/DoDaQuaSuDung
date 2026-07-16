<?php
require_once __DIR__ . "/inc/guard.php";

$id = getRequiredId();
if ($id === 0) {
    redirectWithMessage("../../admin/products.php", "error", "ID sản phẩm không hợp lệ.");
}

$check = $conn->prepare("SELECT image FROM products WHERE id=?");
$check->bind_param("i", $id);
$check->execute();
$product = $check->get_result()->fetch_assoc();

if (!$product) {
    redirectWithMessage("../../admin/products.php", "error", "Không tìm thấy sản phẩm #$id.");
}

$stmt = $conn->prepare("DELETE FROM products WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Dọn file ảnh vật lý nếu có, không để rác trong /uploads
    if (!empty($product["image"])) {
        $path = __DIR__ . "/../../" . $product["image"];
        if (is_file($path)) {
            @unlink($path);
        }
    }
    redirectWithMessage("../../admin/products.php", "success", "Đã xóa sản phẩm #$id.");
} elseif (isForeignKeyError($conn)) {
    // Cột order_details.product_id không có ON DELETE CASCADE/SET NULL,
    // nên sản phẩm đã từng nằm trong một đơn hàng sẽ không thể xóa cứng.
    redirectWithMessage(
        "../../admin/products.php",
        "error",
        "Không thể xóa sản phẩm #$id vì đã tồn tại trong đơn hàng. Hãy chuyển trạng thái sang \"Đã ẩn\" thay vì xóa."
    );
} else {
    redirectWithMessage("../../admin/products.php", "error", "Xóa sản phẩm thất bại. Vui lòng thử lại.");
}