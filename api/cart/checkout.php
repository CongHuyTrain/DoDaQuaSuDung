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
====================================
*/

$sql = "
SELECT
c.product_id,
c.quantity,
p.user_id AS seller_id,
p.price,
p.status
FROM cart c
INNER JOIN products p
ON c.product_id=p.id
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

$payment_status = ($payment_method == "cod")
    ? "unpaid"
    : "pending";

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

        "order_id"=>$order_id

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