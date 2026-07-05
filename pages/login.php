<?php
session_start();

require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $account  = trim($_POST["username_email"]);
    $password = trim($_POST["password"]);

    if (empty($account) || empty($password)) {
        echo "<script>
                alert('Vui lòng nhập đầy đủ thông tin!');
                history.back();
              </script>";
        exit;
    }

    $sql = "
        SELECT *
        FROM users
        WHERE username = ?
           OR email = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ss", $account, $account);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 0) {

        echo "<script>
                alert('Tài khoản không tồn tại!');
                window.location='login.html';
              </script>";
        exit;

    }

    $user = $result->fetch_assoc();

    if (!password_verify($password, $user["password"])) {

        echo "<script>
                alert('Sai mật khẩu!');
                window.location='login.html';
              </script>";
        exit;

    }

    if (isset($user["status"]) && $user["status"] == "blocked") {

        echo "<script>
                alert('Tài khoản đã bị khóa!');
                window.location='login.html';
              </script>";
        exit;

    }

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["fullname"] = $user["fullname"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["role"] = $user["role"] ?? "user";

    echo "<script>
            alert('Đăng nhập thành công!');
            window.location='../index.html';
          </script>";

    exit;
}
?>