<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");

require_once "../../config/db.php";
require_once "../order/create_order.php";

if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Bạn chưa đăng nhập."
    ]);
    exit;
}

$user_id = (int)$_SESSION["user_id"];

$payment_method = trim($_POST["payment_method"] ?? "");

if (!in_array($payment_method, ["cod", "momo"])) {
    echo json_encode([
        "success" => false,
        "message" => "Phương thức thanh toán không hợp lệ."
    ]);
    exit;
}

/*
====================================
Lấy giỏ hàng
(sửa: phải join qua bảng cart_items vì bảng cart
không có product_id/quantity)
====================================
*/

$sql = "
SELECT
ci.product_id,
ci.quantity,
p.user_id AS seller_id,
p.price,
p.status
FROM cart c
INNER JOIN cart_items ci
ON ci.cart_id=c.id
INNER JOIN products p
ON ci.product_id=p.id
WHERE c.user_id=?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$rs = $stmt->get_result();

if ($rs->num_rows == 0) {

    echo json_encode([
        "success" => false,
        "message" => "Giỏ hàng đang trống."
    ]);

    exit;
}

$items = [];
$total = 0;
$seller_id = 0;

while ($row = $rs->fetch_assoc()) {

    if ($row["status"] != "active") {

        echo json_encode([
            "success" => false,
            "message" => "Có sản phẩm không còn khả dụng."
        ]);

        exit;
    }

    $seller_id = $row["seller_id"];

    $total += $row["price"] * $row["quantity"];

    $items[] = $row;
}

$status = "pending";

// sửa: bảng orders chỉ nhận enum('pending','paid','failed'),
// không có giá trị 'unpaid'
$payment_status = "pending";

$conn->begin_transaction();

try{

    $order_ids = createOrder(

        $conn,

        $user_id,

        $payment_method,

        $payment_status

    );

    $conn->commit();

    $message =
    ($payment_method=="cod")
    ?
    "Đặt hàng thành công."
    :
    "Đơn hàng đã được tạo, vui lòng chờ xác nhận thanh toán MoMo.";

    echo json_encode([

        "success"=>true,

        "message"=>$message,

        // sửa: biến $order_id chưa từng được khai báo,
        // dùng đúng $order_ids trả về từ createOrder()
        "order_id"=>$order_ids

    ]);

}
catch(Exception $e){

    $conn->rollback();

    echo json_encode([

        "success"=>false,

        "message"=>$e->getMessage()

    ]);

}

$conn->close();