<?php

function createOrder(
    mysqli $conn,
    int $buyer_id,
    string $payment_method,
    string $payment_status,
    array $selected_product_ids = []
){

if(empty($selected_product_ids)){

    throw new Exception("Vui lòng chọn ít nhất 1 sản phẩm để mua.");

}

$placeholders = implode(",", array_fill(0, count($selected_product_ids), "?"));

$sql="

SELECT
ci.id AS cart_item_id,
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
AND ci.product_id IN ($placeholders)

";

$stmt=$conn->prepare($sql);

$types = "i" . str_repeat("i", count($selected_product_ids));
$params = array_merge([$buyer_id], $selected_product_ids);

$stmt->bind_param($types, ...$params);
$stmt->execute();

$rs=$stmt->get_result();

if($rs->num_rows==0){

throw new Exception("Không tìm thấy sản phẩm đã chọn trong giỏ hàng.");

}

$sellers = [];
$cart_item_ids = [];

while($row = $rs->fetch_assoc()){

    if($row["status"] != "active"){
        throw new Exception("Có sản phẩm không còn khả dụng.");
    }

    $sellers[$row["seller_id"]][] = $row;
    $cart_item_ids[] = $row["cart_item_id"];

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

/*
----------------------------------
Chỉ xóa những cart_items đã được chọn để mua,
KHÔNG xóa cả giỏ hàng (giữ lại các sản phẩm chưa chọn)
----------------------------------
*/

if(!empty($cart_item_ids)){

    $ph = implode(",", array_fill(0, count($cart_item_ids), "?"));

    $stmt = $conn->prepare("DELETE FROM cart_items WHERE id IN ($ph)");

    $stmt->bind_param(str_repeat("i", count($cart_item_ids)), ...$cart_item_ids);

    $stmt->execute();

}

return $order_ids;

}