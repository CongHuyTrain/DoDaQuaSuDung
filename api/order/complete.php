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

// Chỉ người bán hoàn tất
if($order['seller_id']!=$user_id){
    exit("Bạn không có quyền.");
}

// Chỉ hoàn tất khi đã accepted
if($order['status']!='accepted'){
    exit("Không thể hoàn tất.");
}

mysqli_query($conn,"
UPDATE orders
SET status='completed'
WHERE id=$order_id
");

mysqli_query($conn,"
UPDATE products
SET status='sold'
WHERE id={$order['product_id']}
");

$title="Giao dịch hoàn tất";
$content="Đơn hàng của bạn đã hoàn thành.";

mysqli_query($conn,"
INSERT INTO notifications(user_id,title,content,type)
VALUES(
{$order['buyer_id']},
'$title',
'$content',
'order'
)
");

header("Location: ../../pages/transactions.php");
exit();