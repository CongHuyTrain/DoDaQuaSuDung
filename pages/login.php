<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_email = trim($_POST['username_email']);
    $password = $_POST['password'];

    if (empty($username_email) || empty($password)) {
        die("Vui lòng nhập tài khoản và mật khẩu.");
    }

    try {

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :account OR email = :account");
        $stmt->execute(['account' => $username_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            echo "<script>
                    alert('Đăng nhập thành công! Chào mừng " . $user['username'] . "');
                    window.location.href = 'dashboard.php'; // Chuyển hướng tới trang chủ/giao diện chính của bạn
                  </script>";
        } else {

            echo "<script>
                    alert('Tên đăng nhập hoặc mật khẩu không chính xác.');
                    window.location.href = 'login.html';
                  </script>";
        }

    } catch(PDOException $e) {
        die("Lỗi hệ thống: " . $e->getMessage());
    }
}
?>