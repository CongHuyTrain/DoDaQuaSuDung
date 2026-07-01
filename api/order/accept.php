<?php
include("../../config/database.php");
include("../../includes/auth.php");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    exit("Sai phương thức.");
}

$order_id = (int)$_POST["order_id"];
$user_id = $_SESSION["user_id"];

// Lấy thông tin đơn hàng
$sql = "SELECT * FROM orders WHERE id = $order_id";
$result = mysqli_query($conn, $sql);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    exit("Đơn hàng không tồn tại.");
}

// Chỉ người bán mới được xác nhận
if ($order['seller_id'] != $user_id) {
    exit("Bạn không có quyền.");
}

// Chỉ xác nhận khi đang pending
if ($order['status'] != 'pending') {
    exit("Đơn hàng không hợp lệ.");
}

// Cập nhật đơn hàng
mysqli_query($conn, "
UPDATE orders
SET status='accepted'
WHERE id=$order_id
");

// Cập nhật sản phẩm
mysqli_query($conn, "
UPDATE products
SET status='reserved'
WHERE id={$order['product_id']}
");

// Gửi thông báo
$title = "Đơn hàng được chấp nhận";
$content = "Người bán đã chấp nhận yêu cầu mua của bạn.";

mysqli_query($conn, "
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