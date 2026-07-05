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

$title           = trim($_POST['title'] ?? '');
$description     = trim($_POST['description'] ?? '');
$price           = $_POST['price'] ?? null;
$categoryId      = $_POST['category_id'] ?? null;
$conditionStatus = $_POST['condition_status'] ?? 'Đã qua sử dụng';

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

$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize = 5 * 1024 * 1024;

    $fileType = mime_content_type($_FILES['image']['tmp_name']);
    $fileSize = $_FILES['image']['size'];

    if (!in_array($fileType, $allowedTypes, true)) {
        http_response_code(422);
        echo json_encode(["success" => false, "message" => "Chỉ chấp nhận ảnh JPG, PNG hoặc WEBP."]);
        exit;
    }
    if ($fileSize > $maxSize) {
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
        $imagePath = 'uploads/products/' . $fileName;
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Lưu ảnh thất bại."]);
        exit;
    }
}

try {
    $stmt = $conn->prepare("
    INSERT INTO products (user_id, category_id, title, description, price, condition_status, image )VALUES (?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        ':user_id'          => $userId,
        ':category_id'      => $categoryId,
        ':title'            => $title,
        ':description'      => $description,
        ':price'            => $price,
        ':condition_status' => $conditionStatus,
        ':image'            => $imagePath,
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Đăng sản phẩm thành công.",
        "product_id" => $conn->insert_id,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Không thể thêm sản phẩm."]);
}
