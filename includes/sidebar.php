<div class="col-lg-3">

<div class="card border-0 shadow-sm rounded-4">

<div class="card-body text-center">

<img

src="../assets/images/avatar.png"

width="90"

height="90"

class="rounded-circle mb-3">

<h5>

<?= $_SESSION['fullname']; ?>

</h5>

<p class="text-secondary">

<?= $_SESSION['email']; ?>

</p>

<hr>

<a href="profile.php"

class="sidebar-link">

<i class="fa fa-user"></i>

Thông tin cá nhân

</a>

<a href="my-orders.php"

class="sidebar-link">

<i class="fa fa-bag-shopping"></i>

Đơn mua

</a>

<a href="transactions.php"

class="sidebar-link">

<i class="fa fa-store"></i>

Đơn bán

</a>

<a href="favorites.php"

class="sidebar-link">

<i class="fa fa-heart"></i>

Yêu thích

</a>

<a href="../logout.php"

class="sidebar-link text-danger">

<i class="fa fa-right-from-bracket"></i>

Đăng xuất

</a>

</div>

</div>

</div>