<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");

require_once "../../config/db.php";

if (!isset($_SESSION["user_id"])) {
    exit(json_encode([
        "success" => false,
        "message" => "Bạn chưa đăng nhập."
    ]));
}

$user_id = (int)$_SESSION["user_id"];

/*
Lấy toàn bộ sản phẩm trong giỏ
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
$stmt->bind_param("i",$user_id);
$stmt->execute();

$rs = $stmt->get_result();

$items=[];

$total=0;

while($row=$rs->fetch_assoc()){

    if($row["status"]!="active"){

        exit(json_encode([
            "success"=>false,
            "message"=>"Có sản phẩm không còn khả dụng."
        ]));

    }

    $items[]=$row;

    $total += $row["price"]*$row["quantity"];

}

if(empty($items)){

    exit(json_encode([
        "success"=>false,
        "message"=>"Giỏ hàng đang trống."
    ]));

}

/*
Hiện tại 1 đơn chỉ cho 1 người bán.
*/

$seller_id=$items[0]["seller_id"];

foreach($items as $i){

    if($i["seller_id"]!=$seller_id){

        exit(json_encode([
            "success"=>false,
            "message"=>"Hiện tại chỉ hỗ trợ thanh toán các sản phẩm cùng một người bán."
        ]));

    }

}

$conn->begin_transaction();

try{

$status="pending";

$stmt=$conn->prepare("
INSERT INTO orders(

buyer_id,
seller_id,
total_amount,
status

)

VALUES(

?,?,?,?

)
");

$stmt->bind_param(

"iids",

$user_id,
$seller_id,
$total,
$status

);

$stmt->execute();

$order_id=$conn->insert_id;


/*
order_details
*/

$stmt=$conn->prepare("
INSERT INTO order_details(

order_id,
product_id,
quantity,
price

)

VALUES(

?,?,?,?

)

");

foreach($items as $i){

$stmt->bind_param(

"iiid",

$order_id,
$i["product_id"],
$i["quantity"],
$i["price"]

);

$stmt->execute();

}


/*
đổi trạng thái sản phẩm
*/

$stmt=$conn->prepare("
UPDATE products
SET status='pending'
WHERE id=?
");

foreach($items as $i++){

}
foreach($items as $i){

$id=$i["product_id"];

$stmt->bind_param("i",$id);

$stmt->execute();

}


/*
xóa giỏ hàng
*/

$stmt=$conn->prepare("
DELETE FROM cart
WHERE user_id=?
");

$stmt->bind_param("i",$user_id);

$stmt->execute();

$conn->commit();

echo json_encode([

"success"=>true,

"payment_url"=>"../payment/vnpay_create.php?order_id=".$order_id

]);

}catch(Exception $e){

$conn->rollback();

echo json_encode([

"success"=>false,

"message"=>$e->getMessage()

]);

}

$conn->close();

?>