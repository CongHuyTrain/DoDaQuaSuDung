<?php

session_start();

$_SESSION = array();

session_destroy();

echo "<script>
        alert('Đã đăng xuất tài khoản thành công.');
        window.location.href = 'login.html';
      </script>";
exit;
?>