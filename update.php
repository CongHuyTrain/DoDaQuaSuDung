<?php
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Phương thức không hợp lệ."]);
    exit;
}

$userId = requireLogin();

$productId       = $_POST['product_id'] ?? null;
$title           = trim($_POST['title'] ?? '');
$description     = trim($_POST['description'] ?? '');
$price           = $_POST['price'] ?? null;
$categoryId      = $_POST['category_id'] ?? null;
$conditionStatus = $_POST['condition_status'] ?? 'Đã qua sử dụng';
$status          = $_POST['status'] ?? 'Đang bán';

if (!ctype_digit((string) $productId)) {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => "Sản phẩm không hợp lệ."]);
    exit;
}

// Kiểm tra quyền sở hữu sản phẩm
$check = $pdo->prepare("SELECT image FROM products WHERE id = :id AND user_id = :user_id");
$check->execute([':id' => $productId, ':user_id' => $userId]);
$existing = $check->fetch();

if (!$existing) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Bạn không có quyền sửa sản phẩm này."]);
    exit;
}

$errors = [];
if ($title === '' || mb_strlen($title) > 255) {
    $errors[] = "Tên sản phẩm không hợp lệ.";
}
if (!is_numeric($price) || (float) $price < 0) {
    $errors[] = "Giá sản phẩm không hợp lệ.";
}
if (!ctype_digit((string) $categoryId)) {
    $errors[] = "Danh mục không hợp lệ.";
}
if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => implode(' ', $errors)]);
    exit;
}

$imagePath = $existing['image'];
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize = 5 * 1024 * 1024;

    $fileType = mime_content_type($_FILES['image']['tmp_name']);
    if (!in_array($fileType, $allowedTypes, true)) {
        http_response_code(422);
        echo json_encode(["success" => false, "message" => "Chỉ chấp nhận ảnh JPG, PNG hoặc WEBP."]);
        exit;
    }
    if ($_FILES['image']['size'] > $maxSize) {
        http_response_code(422);
        echo json_encode(["success" => false, "message" => "Ảnh không được vượt quá 5MB."]);
        exit;
    }

    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid('prod_', true) . '.' . $ext;
    $uploadDir = __DIR__ . '/../../uploads/products/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
        if ($imagePath && file_exists(__DIR__ . '/../../' . $imagePath)) {
            unlink(__DIR__ . '/../../' . $imagePath);
        }
        $imagePath = 'uploads/products/' . $fileName;
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Lưu ảnh thất bại."]);
        exit;
    }
}

try {
    $stmt = $pdo->prepare(
        "UPDATE products
         SET category_id = :category_id, title = :title, description = :description,
             price = :price, condition_status = :condition_status, status = :status, image = :image
         WHERE id = :id AND user_id = :user_id"
    );
    $stmt->execute([
        ':category_id'      => $categoryId,
        ':title'            => $title,
        ':description'      => $description,
        ':price'            => $price,
        ':condition_status' => $conditionStatus,
        ':status'           => $status,
        ':image'            => $imagePath,
        ':id'               => $productId,
        ':user_id'          => $userId,
    ]);

    echo json_encode(["success" => true, "message" => "Cập nhật sản phẩm thành công."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Không thể cập nhật sản phẩm."]);
}
