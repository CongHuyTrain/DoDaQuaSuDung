<?php


$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    header("Location: product-detail.html?id=$id", true, 301);
} else {
    header("Location: products.html", true, 301);
}
exit;
?>
