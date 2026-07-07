<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "../config/db.php";

$sql = "
SELECT
id,
name
FROM categories
WHERE status=1
ORDER BY name
";

$result = $conn->query($sql);

$data=[];

while($row=$result->fetch_assoc()){

    $data[]=$row;

}

echo json_encode([
    "success"=>true,
    "data"=>$data
],JSON_UNESCAPED_UNICODE);

$conn->close();

?>