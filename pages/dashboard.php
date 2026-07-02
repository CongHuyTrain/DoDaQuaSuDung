<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Trị Hệ Thống</title>
</head>
<body>
    <h1>Chào mừng bạn, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Email của bạn: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
    <hr>
    <a href="logout.php" style="color: red; font-weight: bold; text-decoration: none;">👉 ĐĂNG XUẤT TÀI KHOẢN</a>
</body>
</html>