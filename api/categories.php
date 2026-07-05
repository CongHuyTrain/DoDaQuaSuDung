<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

require_once "../config/db.php";

$sql = "
SELECT
    id,
    name
FROM categories
WHERE status = 1
ORDER BY name ASC
";

$result = $conn->query($sql);

$categories = [];

while($row = $result->fetch_assoc()){
    $categories[] = $row;
}

echo json_encode([
    "success"=>true,
    "data"=>$categories
],JSON_UNESCAPED_UNICODE);

$conn->close();
?>