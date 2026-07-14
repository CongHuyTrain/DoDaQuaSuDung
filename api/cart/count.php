<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

require_once "../../config/db.php";

if(!isset($_SESSION["user_id"])){

    echo json_encode([
        "success"=>true,
        "count"=>0
    ]);
    exit;

}

$user_id=$_SESSION["user_id"];

$sql="
SELECT
COUNT(*) total
FROM cart_items ci
INNER JOIN cart c
ON ci.cart_id=c.id
WHERE c.user_id=?
";

$stmt=$conn->prepare($sql);

$stmt->bind_param("i",$user_id);

$stmt->execute();

$count=$stmt
->get_result()
->fetch_assoc()["total"];

echo json_encode([
    "success"=>true,
    "count"=>(int)$count
]);

$stmt->close();
$conn->close();
?>