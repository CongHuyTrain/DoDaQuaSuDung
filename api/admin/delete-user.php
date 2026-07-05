<?php

session_start();

require_once "../../config/db.php";

$id=(int)$_GET["id"];

$stmt=$conn->prepare("

DELETE FROM users

WHERE id=?

");

$stmt->bind_param("i",$id);

$stmt->execute();

header("Location: ../../admin/users.php");
exit;