<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");

require_once "../../config/db.php";

if(!isset($_SESSION["user_id"])){

echo json_encode([
"success"=>false
]);

exit;

}

$user_id=(int)$_SESSION["user_id"];

$stmt=$conn->prepare("

SELECT id
FROM cart
WHERE user_id=?
LIMIT 1

");

$stmt->bind_param("i",$user_id);

$stmt->execute();

$cart=$stmt->get_result()->fetch_assoc();

if(!$cart){

echo json_encode([

"success"=>true,

"items"=>[]

]);

exit;

}

$cart_id=$cart["id"];

$stmt=$conn->prepare("

SELECT

ci.id AS cart_item_id,

ci.product_id,

ci.quantity,

p.title,

p.price,

p.image,

p.status,

p.location,

p.condition_item,

u.fullname AS seller

FROM cart_items ci

JOIN products p
ON ci.product_id=p.id

JOIN users u
ON p.user_id=u.id

WHERE ci.cart_id=?

ORDER BY ci.id DESC

");

$stmt->bind_param("i",$cart_id);

$stmt->execute();

$rs=$stmt->get_result();

$items=[];

$total=0;

while($r=$rs->fetch_assoc()){

$r["subtotal"]=
$r["price"]*$r["quantity"];

$total+=$r["subtotal"];

$items[]=$r;

}

echo json_encode([

"success"=>true,

"items"=>$items,

"total"=>$total

]);

$conn->close();