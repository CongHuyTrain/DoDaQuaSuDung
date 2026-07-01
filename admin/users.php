<?php

include("../config/database.php");

$result=mysqli_query($conn,"SELECT * FROM users");

?>
<table class="table table-hover">

<tr>

<th>ID</th>

<th>Avatar</th>

<th>Tên</th>

<th>Email</th>

<th>Vai trò</th>

<th>Trạng thái</th>

<th>Action</th>

</tr>

<?php

while($row=mysqli_fetch_assoc($result)){

?>

<tr>

<td><?= $row['id']?></td>

<td>

<img

src="../uploads/<?= $row['avatar']?>"

width="50">

</td>

<td><?= $row['fullname']?></td>

<td><?= $row['email']?></td>

<td><?= $row['role_id']?></td>

<td><?= $row['status']?></td>

<td>

<form
action="../api/admin/block-user.php"
method="POST">

<input
type="hidden"
name="id"
value="<?= $row['id']?>">

<button
class="btn btn-danger btn-sm">

Khóa

</button>

</form>

</td>

</tr>

<?php

}

?>

</table>
