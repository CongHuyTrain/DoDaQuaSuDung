<?php
// product-detail.php
// File này chỉ để redirect sang product-detail.html
// Giải quyết trường hợp link cũ vẫn trỏ vào .php

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    header("Location: product-detail.html?id=$id", true, 301);
} else {
    header("Location: products.html", true, 301);
}
exit;
?>
