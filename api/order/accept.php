<?php
session_start();
require_once "../../config/db.php";
if(!isset($_SESSION["user_id"])){
    die("Bạn chưa đăng nhập.");
}
$seller_id=$_SESSION["user_id"];
$id=isset($_GET["id"])?(int)$_GET["id"]:0;
$sql="
SELECT *
FROM orders
WHERE id=?
AND seller_id=?
LIMIT 1
";
$stmt=$conn->prepare($sql);
$stmt->bind_param("ii",$id,$seller_id);
$stmt->execute();
$order=$stmt->get_result()->fetch_assoc();
if(!$order){
    die("Không tìm thấy đơn hàng.");
}
$stmt=$conn->prepare("
UPDATE orders
SET status='accepted'
WHERE id=?
");
$stmt->bind_param("i",$id);
$stmt->execute();
header("Location: ../../admin/orders.php");
exit;