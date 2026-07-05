<?php
session_start();
require_once "../config/db.php";
$result = $conn->query("
    SELECT *
    FROM users
    ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>Quản lý người dùng</title>
        <link rel="stylesheet" href="../assets/css/admin.css">
        <style>

body{
margin:0;
background:#eef2f7;
font-family:Arial;
}

.container{
width:1300px;
margin:auto;
padding:40px;
}

h1{
margin-bottom:25px;
}

table{
width:100%;
background:white;
border-collapse:collapse;
border-radius:12px;
overflow:hidden;
box-shadow:0 8px 20px rgba(0,0,0,.06);
}

th{
background:#2563eb;
color:white;
padding:15px;
}

td{
padding:15px;
border-bottom:1px solid #eee;
text-align:center;
}

img{
width:55px;
height:55px;
border-radius:50%;
object-fit:cover;
}

.badge{
padding:5px 12px;
border-radius:20px;
color:white;
font-size:13px;
}

.active{
background:#16a34a;
}

.blocked{
background:#dc2626;
}

.btn{
padding:8px 15px;
text-decoration:none;
border-radius:8px;
color:white;
font-weight:bold;
}
.block{
background:#dc2626;
}
.unblock{
background:#16a34a;
}

    </style>
    </head>
<body>
    <div class="container">
    <h1> Quản lý người dùng </h1>
    <table>
<tr>
    <th>ID</th>
    <th>Avatar</th>
    <th>Họ tên</th>
    <th>Email</th>
    <th>SĐT</th>
    <th>Vai trò</th>
    <th>Trạng thái</th>
    <th>Hành động</th>
</tr>
    <?php
    while($u=$result->fetch_assoc()){
    ?>
<tr>
    <td><?= $u["id"] ?></td>
    <td>
    <?php
    $avatar = !empty($u["avatar"]) ? $u["avatar"] : "../assets/images/avatar.png";
    ?>
    <img src="<?= htmlspecialchars($avatar) ?>">
    </td>
    <td>
    <?= htmlspecialchars($u["fullname"]) ?>
    </td>
    <td>
    <?= htmlspecialchars($u["email"]) ?>
    </td>
    <td>
    <?= htmlspecialchars($u["phone"]) ?>
    </td>
    <td>
    <?= strtoupper($u["role"]) ?>
    </td>
    <td>
<?php
    if($u["status"]=="active"){
 ?>
    <span class="badge active">
    ACTIVE
    </span>
<?php
    }else{
?>
    <span class="badge blocked">
    BLOCKED
    </span>
<?php
    }
?>
    </td>
    <td>
    <?php
    if($u["status"]=="active"){
    ?>
    <a
    class="btn block"
    href="../api/admin/block-user.php?id=<?= $u["id"] ?>">
    Khóa
    </a>
    <?php
    }else{
    ?>
    <a
    class="btn unblock"
    href="../api/admin/unblock-user.php?id=<?= $u["id"] ?>">
    Mở khóa
    </a>
    <?php
    }
    ?>
    </td>
</tr>
<?php
    }
?>
        </table>
    </div>
</body>
</html>