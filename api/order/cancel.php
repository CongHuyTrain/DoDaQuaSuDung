<?php
session_start();
require_once "../../config/db.php";
if(!isset($_SESSION["user_id"])){
    die("Bạn chưa đăng nhập.");
}
$user=(int)$_SESSION["user_id"];
$id=isset($_GET["id"])?(int)$_GET["id"]:0;

$sql="
    SELECT *
    FROM orders
    WHERE id=?
    AND buyer_id=?
    LIMIT 1
";
$stmt=$conn->prepare($sql);
$stmt->bind_param("ii",$id,$user);
$stmt->execute();
$order=$stmt->get_result()->fetch_assoc();
if(!$order){
    die("Không tìm thấy đơn.");
}

$conn->begin_transaction();
try{

$stmt=$conn->prepare("
    UPDATE orders
    SET status='cancelled'
    WHERE id=?
");
$stmt->bind_param("i",$id);
$stmt->execute();

/*
----------------------------------
Lấy TẤT CẢ sản phẩm trong đơn hàng
(1 đơn có thể có nhiều sản phẩm)
----------------------------------
*/

$stmt=$conn->prepare("
    SELECT product_id
    FROM order_details
    WHERE order_id=?
");
$stmt->bind_param("i",$id);
$stmt->execute();
$rs=$stmt->get_result();

$stmtUpdate=$conn->prepare("
    UPDATE products
    SET status='active'
    WHERE id=?
");

while($row=$rs->fetch_assoc()){
    $stmtUpdate->bind_param("i",$row["product_id"]);
    $stmtUpdate->execute();
}

$conn->commit();
}catch(Exception $e){
$conn->rollback();
}
header("Location: ../../pages/my-orders.php");
exit;