<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "dodaquasudung";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Kết nối CSDL thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>