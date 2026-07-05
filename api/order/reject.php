<?php

session_start();

require_once "../../config/db.php";

if(!isset($_SESSION["user_id"])){
    die("Bạn chưa đăng nhập.");
}

$seller_id=$_SESSION["user_id"];

$id=(int)$_GET["id"];

$sql="

SELECT

o.*,

od.product_id

FROM orders o

INNER JOIN order_details od
ON o.id=od.order_id

WHERE

o.id=?
AND o.seller_id=?

LIMIT 1

";

$stmt=$conn->prepare($sql);

$stmt->bind_param("ii",$id,$seller_id);

$stmt->execute();

$order=$stmt->get_result()->fetch_assoc();

if(!$order){
    die("Không tìm thấy đơn.");
}

$conn->begin_transaction();

try{

$stmt=$conn->prepare("

UPDATE orders

SET status='rejected'

WHERE id=?

");

$stmt->bind_param("i",$id);

$stmt->execute();

$stmt=$conn->prepare("

UPDATE products

SET status='active'

WHERE id=?

");

$stmt->bind_param("i",$order["product_id"]);

$stmt->execute();

$conn->commit();

}catch(Exception $e){

$conn->rollback();

}

header("Location: ../../pages/transactions.php");
exit;