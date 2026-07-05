<?php
session_start();
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $gender = $_POST['gender'];
    $phone = trim($_POST['phone']);

    $dob_day = $_POST['dob_day'];
    $dob_month = $_POST['dob_month'];
    $dob_year = $_POST['dob_year'];
    $dob = "$dob_year-$dob_month-$dob_day";

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        echo "<script>
                alert('Vui lòng điền đầy đủ các thông tin bắt buộc.');
                history.back();
              </script>";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "<script>
                alert('Mật khẩu xác nhận không khớp.');
                history.back();
              </script>";
        exit;
    }

    if (strlen($password) < 6) {
        echo "<script>
                alert('Mật khẩu phải có ít nhất 6 ký tự.');
                history.back();
              </script>";
        exit;
    }

    // Kiểm tra username hoặc email đã tồn tại
    $stmt = $conn->prepare("
        SELECT id
        FROM users
        WHERE username = ?
           OR email = ?
        LIMIT 1
    ");

    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {

        echo "<script>
                alert('Tên đăng nhập hoặc Email đã được sử dụng.');
                history.back();
              </script>";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $fullname = $username;
    $role = "user";
    $status = "active";

    $stmt = $conn->prepare("
        INSERT INTO users
        (
            fullname,
            username,
            email,
            password,
            gender,
            dob,
            phone,
            role,
            status,
            created_at
        )
        VALUES
        (
            ?,?,?,?,?,?,?,?, ?,NOW()
        )
    ");

    $stmt->bind_param(
        "sssssssss",
        $fullname,
        $username,
        $email,
        $hashed_password,
        $gender,
        $dob,
        $phone,
        $role,
        $status
    );

    if ($stmt->execute()) {

        echo "<script>
                alert('Đăng ký tài khoản thành công!');
                window.location.href='login.html';
              </script>";

    } else {

        echo "<script>
                alert('Đăng ký thất bại!');
                history.back();
              </script>";

    }
}
?>