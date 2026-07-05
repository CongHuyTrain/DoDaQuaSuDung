<?php
// api/admin/delete-product.php

session_start();

require_once "../../config/db.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if($id <= 0){
    die("ID không hợp lệ.");
}

$stmt = $conn->prepare("
DELETE FROM products
WHERE id=?
");

$stmt->bind_param("i",$id);
$stmt->execute();

header("Location: ../../admin/products.php");
exit;
?>