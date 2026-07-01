<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "dodaquasudung";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>