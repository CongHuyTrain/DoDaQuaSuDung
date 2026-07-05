<?php
session_start();
require_once "../../config/db.php";
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if($id <= 0){
    die("ID không hợp lệ.");
}
$stmt = $conn->prepare("
    UPDATE products
    SET status='rejected'
    WHERE id=?
");
$stmt->bind_param("i",$id);
$stmt->execute();
header("Location: ../../admin/products.php");
exit;
?>