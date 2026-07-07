<?php

session_start();
require_once "config/db.php";

if (!isset($_SESSION["user_id"])) {
    die("Bạn chưa đăng nhập.");
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: add-product.html");
    exit;
}

$user_id = $_SESSION["user_id"];

$title = trim($_POST["title"]);
$category_id = intval($_POST["category_id"]);
$price = floatval($_POST["price"]);
$condition_item = $_POST["condition_item"];
$location = trim($_POST["location"]);
$description = trim($_POST["description"]);

if (
    empty($title) ||
    empty($category_id) ||
    empty($price)
) {
    die("Vui lòng nhập đầy đủ thông tin.");
}

$image = "";

if (
    isset($_FILES["image"]) &&
    $_FILES["image"]["error"] == 0
) {

    $uploadDir = "uploads/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir,0777,true);
    }

    $ext = strtolower(pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION));

    $fileName = time().rand(1000,9999).".".$ext;

    move_uploaded_file(
        $_FILES["image"]["tmp_name"],
        $uploadDir.$fileName
    );

    $image = $uploadDir.$fileName;
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

if($stmt->execute()){

    echo "<script>
    alert('Thêm sản phẩm thành công!');
    window.location='admin/products.php';
    </script>";

}else{

    echo "<script>
    alert('Không thể thêm sản phẩm!');
    history.back();
    </script>";

}

$stmt->close();
$conn->close();

?>