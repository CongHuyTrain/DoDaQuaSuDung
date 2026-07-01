<?php
include("../../config/database.php");
include("../../includes/auth.php");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    exit("Sai phương thức.");
}

$order_id = (int)$_POST["order_id"];
$user_id = $_SESSION["user_id"];

$sql = "SELECT * FROM orders WHERE id=$order_id";
$result = mysqli_query($conn,$sql);
$order = mysqli_fetch_assoc($result);

if(!$order){
    exit("Không tìm thấy đơn.");
}

if($order['seller_id'] != $user_id){
    exit("Bạn không có quyền.");
}

if($order['status'] != 'pending'){
    exit("Không thể từ chối.");
}

mysqli_query($conn,"
UPDATE orders
SET status='rejected'
WHERE id=$order_id
");

$title = "Đơn hàng bị từ chối";
$content = "Người bán đã từ chối yêu cầu mua.";

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