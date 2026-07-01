<?php

include("../../config/database.php");
include("../../includes/auth.php");

$user_id = $_SESSION["user_id"];

$sql = "
SELECT
    orders.*,
    products.title,
    products.price,
    products.status AS product_status,
    users.fullname AS buyer_name

FROM orders

JOIN products
ON orders.product_id = products.id

JOIN users
ON orders.buyer_id = users.id

WHERE orders.seller_id = '$user_id'

ORDER BY orders.created_at DESC
";

$result = mysqli_query($conn,$sql);
while($row=mysqli_fetch_assoc($result))
{
    ?>

<div class="card">

<h3><?= $row["title"] ?></h3>

<p>

Người mua:

<?= $row["buyer_name"] ?>

</p>

<p>

Giá:

<?= number_format($row["price"]) ?>

</p>

<p>

Trạng thái:

<?= $row["status"] ?>

</p>

<a href="accept.php?id=<?= $row["id"] ?>">

Xác nhận

</a>

|

<a href="reject.php?id=<?= $row["id"] ?>">

Từ chối

</a>

</div>

<?php

}
