<?php

session_start();
require_once "config/db.php";

header("Content-Type: application/json; charset=utf-8");

function respond($success, $message) {
    echo json_encode([
        "success" => $success,
        "message" => $message
    ]);
    exit;
}

if (!isset($_SESSION["user_id"])) {
    respond(false, "Bạn chưa đăng nhập.");
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    respond(false, "Phương thức không hợp lệ.");
}

$user_id = $_SESSION["user_id"];

$title = trim($_POST["title"] ?? "");
$category_id = intval($_POST["category_id"] ?? 0);
$price = floatval($_POST["price"] ?? 0);
$condition_item = $_POST["condition_item"] ?? "";
$location = trim($_POST["location"] ?? "");
$description = trim($_POST["description"] ?? "");

if (
    empty($title) ||
    empty($category_id) ||
    empty($price)
) {
    respond(false, "Vui lòng nhập đầy đủ thông tin.");
}

$image = "";

if (
    isset($_FILES["image"]) &&
    $_FILES["image"]["error"] == 0
) {

    $uploadDir = "uploads/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

    $fileName = time() . rand(1000, 9999) . "." . $ext;

    move_uploaded_file(
        $_FILES["image"]["tmp_name"],
        $uploadDir . $fileName
    );

    $image = $uploadDir . $fileName;
}

$sql = "
INSERT INTO products
(
user_id,
category_id,
title,
description,
price,
image,
condition_item,
location
)
VALUES
(
?,?,?,?,?,?,?,?
)
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    respond(false, "Lỗi chuẩn bị câu lệnh SQL: " . $conn->error);
}

$stmt->bind_param(
    "iissdsss",
    $user_id,
    $category_id,
    $title,
    $description,
    $price,
    $image,
    $condition_item,
    $location
);

if ($stmt->execute()) {
    respond(true, "Đăng sản phẩm thành công!");
} else {
    respond(false, "Không thể thêm sản phẩm: " . $stmt->error);
}

$stmt->close();
$conn->close();