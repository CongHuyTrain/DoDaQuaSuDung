<?php

include("../../config/database.php");
include("../../includes/auth.php");

if($_SERVER["REQUEST_METHOD"]!="POST"){
    exit("Sai phương thức");
}

$product_id=(int)$_POST["product_id"];

$buyer=$_SESSION["user_id"];
$sql="

SELECT *

FROM products

WHERE id=$product_id

";

$result=mysqli_query($conn,$sql);

$product=mysqli_fetch_assoc($result);
if(!$product){

    exit("Không tìm thấy sản phẩm");

}
if($buyer==$product["user_id"]){

    exit("Bạn không thể mua sản phẩm của mình");

}
if($product["status"]!="available"){

    exit("Sản phẩm không còn");

}
$seller=$product["user_id"];
$sql = "INSERT INTO orders(product_id, buyer_id, seller_id, status, price)
VALUES(
    '$product_id',
    '$buyer',
    '$seller',
    'pending',
    '{$product['price']}'
)";

mysqli_query($conn, $sql);
$order_id = mysqli_insert_id($conn);
$title = "Đơn hàng mới";
$content = "Có người muốn mua sản phẩm: ".$product['title'];

$sql = "INSERT INTO notifications(user_id, title, content, type)
VALUES(
    '$seller',
    '$title',
    '$content',
    'order'
)";

mysqli_query($conn, $sql);
header("Location: ../../pages/my-orders.php?success=1");
exit();
