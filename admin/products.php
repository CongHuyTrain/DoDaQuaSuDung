<?php
// admin/products.php

session_start();
require_once "../config/db.php";

$keyword = trim($_GET["keyword"] ?? "");

if ($keyword != "") {

    $stmt = $conn->prepare("
        SELECT
            p.*,
            c.name category_name,
            u.fullname seller_name
        FROM products p
        LEFT JOIN categories c ON p.category_id=c.id
        LEFT JOIN users u ON p.user_id=u.id
        WHERE p.title LIKE ?
        ORDER BY p.created_at DESC
    ");

    $like="%".$keyword."%";
    $stmt->bind_param("s",$like);
    $stmt->execute();
    $result=$stmt->get_result();

}else{

    $result=$conn->query("
        SELECT
            p.*,
            c.name category_name,
            u.fullname seller_name
        FROM products p
        LEFT JOIN categories c ON p.category_id=c.id
        LEFT JOIN users u ON p.user_id=u.id
        ORDER BY p.created_at DESC
    ");

}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý sản phẩm</title>

<link rel="stylesheet" href="../assets/css/admin.css">

<style>

body{
font-family:Arial;
background:#eef2f7;
margin:0;
}

.container{
width:1400px;
margin:auto;
padding:40px;
}

table{
width:100%;
background:#fff;
border-collapse:collapse;
box-shadow:0 10px 25px rgba(0,0,0,.08);
}

th{
background:#2563eb;
color:white;
padding:15px;
}

td{
padding:12px;
text-align:center;
border-bottom:1px solid #eee;
}

img{
width:70px;
height:70px;
object-fit:cover;
border-radius:8px;
}

.btn{

padding:8px 14px;

border-radius:8px;

text-decoration:none;

color:#fff;

font-weight:bold;

display:inline-block;

margin:2px;

}

.green{background:#16a34a;}
.red{background:#dc2626;}
.gray{background:#475569;}

.pending{color:#f59e0b;font-weight:bold;}
.active{color:#16a34a;font-weight:bold;}
.sold{color:#2563eb;font-weight:bold;}
.rejected{color:red;font-weight:bold;}

</style>

</head>

<body>

<div class="container">

<h1>Quản lý sản phẩm</h1>

<form method="GET" style="margin-bottom:20px">

<input
type="text"
name="keyword"
placeholder="Tìm sản phẩm..."
value="<?= htmlspecialchars($keyword) ?>"
style="padding:10px;width:300px;">

<button style="padding:10px 18px">
Tìm
</button>

</form>

<table>

<tr>

<th>ID</th>

<th>Ảnh</th>

<th>Tên</th>

<th>Danh mục</th>

<th>Người đăng</th>

<th>Giá</th>

<th>Trạng thái</th>

<th>Hành động</th>

</tr>

<?php while($p=$result->fetch_assoc()){ ?>

<tr>

<td><?= $p["id"] ?></td>

<td>

<img src="../<?= htmlspecialchars($p["image"]) ?>">

</td>

<td><?= htmlspecialchars($p["title"]) ?></td>

<td><?= htmlspecialchars($p["category_name"]) ?></td>

<td><?= htmlspecialchars($p["seller_name"]) ?></td>

<td><?= number_format($p["price"],0,",",".") ?>đ</td>

<td>

<span class="<?= $p["status"] ?>">

<?= strtoupper($p["status"]) ?>

</span>

</td>

<td>

<?php if($p["status"]=="pending"){ ?>

<a
class="btn green"
href="../api/admin/approve.php?id=<?= $p["id"] ?>">

Duyệt

</a>

<a
class="btn red"
href="../api/admin/reject.php?id=<?= $p["id"] ?>">

Từ chối

</a>

<?php } ?>

<a
class="btn gray"
onclick="return confirm('Xóa sản phẩm?')"
href="../api/admin/delete-product.php?id=<?= $p["id"] ?>">

Xóa

</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>