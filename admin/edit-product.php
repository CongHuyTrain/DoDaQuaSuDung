<?php
require_once "inc/auth.php";

if (!isset($_GET["id"])) {
    die("Thiếu ID sản phẩm.");
}

$id = (int)$_GET["id"];

/* Danh sách trạng thái hợp lệ - PHẢI khớp với enum cột products.status trong DB.
   (Xem sql/fix-schema.sql để mở rộng enum nếu muốn dùng 'hidden' / 'rejected'.) */
$validStatuses = ["pending", "active", "sold", "hidden", "rejected", "deleted"];

$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");

$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Không tìm thấy sản phẩm.");
}

$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = $_POST["price"] ?? 0;
    $category_id = (int)($_POST["category_id"] ?? 0);
    $condition_item = $_POST["condition_item"] ?? "good";
    $location = trim($_POST["location"] ?? "");
    $status = $_POST["status"] ?? "pending";

    if ($title === "") {
        $error = "Tên sản phẩm không được để trống.";
    } elseif (!is_numeric($price) || (float)$price < 0) {
        $error = "Giá sản phẩm không hợp lệ.";
    } elseif (!in_array($status, $validStatuses, true)) {
        // Đây chính là lỗi từng khiến sản phẩm #3 bị lưu status = '' (rỗng):
        // trước đây form gửi lên giá trị không nằm trong enum của cột products.status,
        // MySQL tự chuyển thành chuỗi rỗng và sản phẩm biến mất khỏi mọi bộ lọc.
        $error = "Trạng thái không hợp lệ.";
    }

    $image = $product["image"];

    if (!$error && isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {

        $allowedExt = ["jpg", "jpeg", "png", "webp"];
        $ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($ext, $allowedExt, true)) {
            $error = "Chỉ chấp nhận ảnh định dạng JPG, PNG hoặc WEBP.";
        } elseif ($_FILES["image"]["size"] > $maxSize) {
            $error = "Ảnh không được vượt quá 5MB.";
        } else {
            $filename = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES["image"]["name"]));
            $target = "../uploads/" . $filename;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target)) {
                $image = "uploads/" . $filename;
            } else {
                $error = "Tải ảnh lên thất bại.";
            }
        }
    }

    if (!$error) {
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
        }
        $error = "Cập nhật thất bại: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chỉnh sửa sản phẩm – Đồ Cũ VN Admin</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">

    <?php include "sidebar.php"; ?>

    <main class="admin-main">

        <div class="admin-topbar">
            <div>
                <h1>Chỉnh sửa sản phẩm</h1>
                <div class="subtitle">#<?= (int)$product["id"] ?> — <?= e($product["title"]) ?></div>
            </div>
            <a href="products.php" class="btn btn-outline">← Quay lại</a>
        </div>

        <div class="panel">

            <?php if ($error): ?>
                <div class="alert alert-error"><?= e($error) ?></div>
            <?php endif; ?>

            <?php if (trim((string)$product["status"]) === ""): ?>
                <div class="alert alert-info">
                    ⚠️ Sản phẩm này đang có trạng thái không hợp lệ trong dữ liệu gốc (rỗng).
                    Hãy chọn lại một trạng thái hợp lệ ở bên dưới rồi lưu để sửa lỗi.
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">

                    <div class="form-group">
                        <label>Tên sản phẩm</label>
                        <input type="text" name="title" required value="<?= e($product["title"]) ?>">
                    </div>

                    <div class="form-group">
                        <label>Giá (đ)</label>
                        <input type="number" name="price" min="0" step="1000" required value="<?= e($product["price"]) ?>">
                    </div>

                    <div class="form-group">
                        <label>Danh mục</label>
                        <select name="category_id">
                            <?php $categories->data_seek(0); while ($c = $categories->fetch_assoc()): ?>
                                <option value="<?= (int)$c["id"] ?>" <?= (int)$c["id"] === (int)$product["category_id"] ? "selected" : "" ?>>
                                    <?= e($c["name"]) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tình trạng</label>
                        <select name="condition_item">
                            <option value="new" <?= $product["condition_item"] === "new" ? "selected" : "" ?>>Mới</option>
                            <option value="like_new" <?= $product["condition_item"] === "like_new" ? "selected" : "" ?>>Như mới</option>
                            <option value="good" <?= $product["condition_item"] === "good" ? "selected" : "" ?>>Tốt</option>
                            <option value="fair" <?= $product["condition_item"] === "fair" ? "selected" : "" ?>>Khá</option>
                        </select>
                    </div>

                    <div class="form-group full">
                        <label>Mô tả</label>
                        <textarea name="description"><?= e($product["description"]) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Địa điểm</label>
                        <input type="text" name="location" value="<?= e($product["location"]) ?>">
                    </div>

                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="status">
                            <option value="pending"  <?= $product["status"] === "pending"  ? "selected" : "" ?>>Chờ duyệt</option>
                            <option value="active"   <?= $product["status"] === "active"   ? "selected" : "" ?>>Đang bán</option>
                            <option value="sold"     <?= $product["status"] === "sold"     ? "selected" : "" ?>>Đã bán</option>
                            <option value="hidden"   <?= $product["status"] === "hidden"   ? "selected" : "" ?>>Đã ẩn</option>
                            <option value="rejected" <?= $product["status"] === "rejected" ? "selected" : "" ?>>Từ chối</option>
                        </select>
                        <div class="hint">Cần chạy sql/fix-schema.sql một lần để DB chấp nhận "Đã ẩn" / "Từ chối".</div>
                    </div>

                    <div class="form-group full">
                        <label>Ảnh hiện tại</label>
                        <img class="current-image" src="../<?= e($product["image"]) ?>" alt="">
                    </div>

                    <div class="form-group full">
                        <label>Chọn ảnh mới (nếu muốn)</label>
                        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
                        <div class="hint">Định dạng JPG / PNG / WEBP, tối đa 5MB.</div>
                    </div>

                    <div class="form-actions">
                        <button class="btn btn-primary">Lưu thay đổi</button>
                        <a href="products.php" class="btn btn-outline">Hủy</a>
                    </div>

                </div>
            </form>

        </div>

    </main>
</div>

<script src="../assets/js/admin.js"></script>
</body>
</html>