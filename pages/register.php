<?php
require_once 'db.php';

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
        die("Vui lòng điền đầy đủ các thông tin bắt buộc.");
    }

    if ($password !== $confirm_password) {
        die("Mật khẩu và xác nhận mật khẩu không trùng khớp.");
    }

    if (strlen($password) < 6) {
        die("Mật khẩu phải chứa ít nhất 6 ký tự.");
    }

    try {

        $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        
        if ($stmt->rowCount() > 0) {
            die("Tên đăng nhập hoặc địa chỉ Email đã được sử dụng.");
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (username, email, password, gender, dob, phone) 
                VALUES (:username, :email, :password, :gender, :dob, :phone)";
        $insertStmt = $conn->prepare($sql);
        
        $insertStmt->execute([
            'username' => $username,
            'email'    => $email,
            'password' => $hashed_password,
            'gender'   => $gender,
            'dob'      => $dob,
            'phone'    => $phone
        ]);

        echo "<script>
                alert('Đăng ký tài khoản thành công!');
                window.location.href = 'login.html'; 
              </script>";

    } catch(PDOException $e) {
        die("Lỗi hệ thống: " . $e->getMessage());
    }
}
?>