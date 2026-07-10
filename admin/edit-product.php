<?php
session_start();
require_once "../config/db.php";

if (!isset($_GET["id"])) {
    die("Thiếu ID sản phẩm.");
}

$id = (int)$_GET["id"];

/* Lấy danh mục */
$categories = $conn->query("SELECT id,name FROM categories ORDER BY name");

/* Lấy thông tin sản phẩm */
$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Không tìm thấy sản phẩm.");
}

/* Cập nhật */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $price = $_POST["price"];
    $category_id = $_POST["category_id"];
    $condition_item = $_POST["condition_item"];
    $location = trim($_POST["location"]);
    $status = $_POST["status"];

    $image = $product["image"];

    if (
        isset($_FILES["image"]) &&
        $_FILES["image"]["error"] == 0
    ) {

        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $target = "../uploads/" . $filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target)) {
            $image = "uploads/" . $filename;
        }
    }

    $update = $conn->prepare("
        UPDATE products
        SET
            title=?,
            description=?,
            price=?,
            category_id=?,
            image=?,
            condition_item=?,
            location=?,
            status=?
        WHERE id=?
    ");

    $update->bind_param(
        "ssdissssi",
        $title,
        $description,
        $price,
        $category_id,
        $image,
        $condition_item,
        $location,
        $status,
        $id
    );

    if ($update->execute()) {
        header("Location: products.php");
        exit;
    } else {
        $error = "Cập nhật thất bại!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chỉnh sửa sản phẩm</title>

<link rel="stylesheet" href="../assets/css/admin.css">

<style>

body{
    font-family:Arial;
    background:#eef2f7;
}

.wrapper{
    display:flex;
}

.content{
    margin-left:240px;
    width:calc(100% - 240px);
    padding:30px;
}

.form-box{
    background:#fff;
    padding:25px;
    border-radius:10px;
    box-shadow:0 5px 20px rgba(0,0,0,.1);
}

.form-group{
    margin-bottom:18px;
}

label{
    display:block;
    margin-bottom:6px;
    font-weight:bold;
}

input,
textarea,
select{

    width:100%;
    padding:10px;
    border:1px solid #ccc;
    border-radius:6px;
    box-sizing:border-box;
}

textarea{
    resize:vertical;
    height:120px;
}

img{
    width:160px;
    border-radius:8px;
    margin-top:10px;
}

.btn{
    padding:10px 20px;
    border:none;
    border-radius:6px;
    color:white;
    text-decoration:none;
    cursor:pointer;
    font-size:15px;
}

.save{
    background:#16a34a;
}

.cancel{
    background:#64748b;
}

.error{
    color:red;
    margin-bottom:15px;
}

</style>

</head>

<body>

<div class="wrapper">

<?php include "sidebar.php"; ?>

<div class="content">

<h1>Chỉnh sửa sản phẩm</h1>

<div class="form-box">

<?php
if(isset($error)){
    echo "<p class='error'>$error</p>";
}
?>

<form method="POST" enctype="multipart/form-data">

<div class="form-group">
<label>Tên sản phẩm</label>
<input
type="text"
name="title"
required
value="<?= htmlspecialchars($product["title"]) ?>">
</div>

<div class="form-group">
<label>Giá</label>
<input
type="number"
name="price"
required
value="<?= $product["price"] ?>">
</div>

<div class="form-group">
<label>Danh mục</label>

<select name="category_id">

<?php
$categories->data_seek(0);

while($c = $categories->fetch_assoc()){
?>

<option
value="<?= $c["id"] ?>"
<?= $c["id"]==$product["category_id"]?"selected":"" ?>>
<?= htmlspecialchars($c["name"]) ?>
</option>

<?php } ?>

</select>

</div>

<div class="form-group">

<label>Mô tả</label>

<textarea
name="description"><?= htmlspecialchars($product["description"]) ?></textarea>

</div>

<div class="form-group">

<label>Tình trạng</label>

<select name="condition_item">

<option value="new" <?= $product["condition_item"]=="new"?"selected":"" ?>>Mới</option>

<option value="like_new" <?= $product["condition_item"]=="like_new"?"selected":"" ?>>Như mới</option>

<option value="good" <?= $product["condition_item"]=="good"?"selected":"" ?>>Tốt</option>

<option value="fair" <?= $product["condition_item"]=="fair"?"selected":"" ?>>Khá</option>

</select>

</div>

<div class="form-group">

<label>Địa điểm</label>

<input
type="text"
name="location"
value="<?= htmlspecialchars($product["location"]) ?>">

</div>

<div class="form-group">

<label>Trạng thái</label>

<select name="status">

<option value="pending" <?= $product["status"]=="pending"?"selected":"" ?>>Pending</option>

<option value="active" <?= $product["status"]=="active"?"selected":"" ?>>Active</option>

<option value="sold" <?= $product["status"]=="sold"?"selected":"" ?>>Sold</option>

<option value="hidden" <?= $product["status"]=="hidden"?"selected":"" ?>>Hidden</option>

<option value="rejected" <?= $product["status"]=="rejected"?"selected":"" ?>>Rejected</option>

</select>

</div>

<div class="form-group">

<label>Ảnh hiện tại</label>

<br>

<img src="../<?= htmlspecialchars($product["image"]) ?>">

</div>

<div class="form-group">

<label>Chọn ảnh mới (nếu muốn)</label>

<input type="file" name="image">

</div>

<button class="btn save">
Lưu thay đổi
</button>

<a
href="products.php"
class="btn cancel">
Quay lại
</a>

</form>

</div>

</div>

</div>

</body>
</html>