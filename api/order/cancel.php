<?php
include("../../config/database.php");
include("../../includes/auth.php");

if($_SERVER["REQUEST_METHOD"]!="POST"){
    exit("Sai phương thức.");
}

$order_id=(int)$_POST["order_id"];
$user_id=$_SESSION["user_id"];

$sql="SELECT * FROM orders WHERE id=$order_id";
$result=mysqli_query($conn,$sql);
$order=mysqli_fetch_assoc($result);

if(!$order){
    exit("Không tìm thấy đơn.");
}

// Chỉ người mua được hủy
if($order['buyer_id']!=$user_id){
    exit("Bạn không có quyền.");
}

// Chỉ hủy khi đang chờ
if($order['status']!='pending'){
    exit("Không thể hủy.");
}

mysqli_query($conn,"
UPDATE orders
SET status='cancelled'
WHERE id=$order_id
");

$title="Đơn hàng đã bị hủy";
$content="Người mua đã hủy yêu cầu mua.";

mysqli_query($conn,"
INSERT INTO notifications(user_id,title,content,type)
VALUES(
{$order['seller_id']},
'$title',
'$content',
'order'
)
");

header("Location: ../../pages/my-orders.php");
exit();