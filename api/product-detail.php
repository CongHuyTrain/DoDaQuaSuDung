<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
require_once "../config/db.php";
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    echo json_encode([
        "success" => false,
        "error" => "ID không hợp lệ"
    ]);
    exit;
}
$sql = "
SELECT
p.id,
p.user_id,
p.category_id,
p.title,
p.description,
p.price,
p.image,
p.views,
p.condition_item,
p.location,
p.status,
p.created_at,
c.name AS category_name,
u.id AS seller_id,
u.fullname AS seller_name,
u.phone AS seller_phone,
u.email AS seller_email
FROM products p
LEFT JOIN categories c
ON p.category_id=c.id

LEFT JOIN users u
ON p.user_id=u.id
WHERE
p.id=?
AND p.status IN ('active','pending','sold')
LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if(!$product){
    echo json_encode([
        "success"=>false,
        "error"=>"Sản phẩm không tồn tại."
    ]);
    exit;
}
$update = $conn->prepare("
UPDATE products
SET views=views+1
WHERE id=?
");
$update->bind_param("i",$id);
$update->execute();
$product["views"]++;
$product["price_formatted"] =
number_format($product["price"],0,",",".")." đ";
$img_sql="
SELECT image_url
FROM product_images
WHERE product_id=?
ORDER BY id ASC
";
$img_stmt=$conn->prepare($img_sql);
$img_stmt->bind_param("i",$id);
$img_stmt->execute();
$img_result=$img_stmt->get_result();
$images=[];
while($row=$img_result->fetch_assoc()){
    $images[]=$row["image_url"];
}
if(empty($images) && !empty($product["image"])){
    $images[]=$product["image"];
}

$related_sql="
SELECT
p.id,
p.title,
p.price,
p.image,
p.condition_item,
p.location,
c.name AS category_name
FROM products p
INNER JOIN categories c
ON p.category_id=c.id
WHERE
p.category_id=?
AND p.id<>?
AND p.status='active'
ORDER BY p.created_at DESC
LIMIT 4
";
$rel=$conn->prepare($related_sql);
$rel->bind_param(
"ii",
$product["category_id"],
$id
);
$rel->execute();
$rs=$rel->get_result();
$related=[];
while($r=$rs->fetch_assoc()){

    $r["price_formatted"]=
    number_format($r["price"],0,",",".")." đ";

    $related[]=$r;

}
echo json_encode([
    "success"=>true,
    "data"=>$product,
    "images"=>$images,
    "related"=>$related
],JSON_UNESCAPED_UNICODE);
$stmt->close();
$conn->close();
?>