<?php

function createOrder(
    mysqli $conn,
    int $buyer_id,
    string $payment_method,
    string $payment_status
){

$sql="

SELECT
ci.product_id,
ci.quantity,
p.user_id seller_id,
p.price,
p.status
FROM cart c
JOIN cart_items ci
ON ci.cart_id=c.id
JOIN products p
ON ci.product_id=p.id
WHERE c.user_id=?

";

$stmt=$conn->prepare($sql);
$stmt->bind_param("i",$buyer_id);
$stmt->execute();

$rs=$stmt->get_result();

if($rs->num_rows==0){

throw new Exception("Giỏ hàng trống.");

}

$sellers = [];

while($row = $rs->fetch_assoc()){

    if($row["status"] != "active"){
        throw new Exception("Có sản phẩm không còn khả dụng.");
    }

    $sellers[$row["seller_id"]][] = $row;

}

$status = "pending";

$order_ids = [];

foreach($sellers as $seller_id => $items){

    $total = 0;

    foreach($items as $i){
        $total += $i["price"] * $i["quantity"];
    }

    $stmt = $conn->prepare("
        INSERT INTO orders
        (
            buyer_id,
            seller_id,
            total_amount,
            status,
            payment_method,
            payment_status
        )
        VALUES (?,?,?,?,?,?)
    ");

    $stmt->bind_param(
        "iidsss",
        $buyer_id,
        $seller_id,
        $total,
        $status,
        $payment_method,
        $payment_status
    );

    $stmt->execute();

    $order_id = $conn->insert_id;

    $order_ids[] = $order_id;

    foreach($items as $i){

        $stmt = $conn->prepare("
            INSERT INTO order_details
            (
                order_id,
                product_id,
                quantity,
                price
            )
            VALUES (?,?,?,?)
        ");

        $stmt->bind_param(
            "iiid",
            $order_id,
            $i["product_id"],
            $i["quantity"],
            $i["price"]
        );

        $stmt->execute();

        $stmt = $conn->prepare("
            UPDATE products
            SET status='pending'
            WHERE id=?
        ");

        $stmt->bind_param("i",$i["product_id"]);
        $stmt->execute();

    }

}

$stmt = $conn->prepare("
DELETE FROM cart
WHERE user_id=?
");

$stmt->bind_param("i",$buyer_id);
$stmt->execute();

return $order_ids;

}