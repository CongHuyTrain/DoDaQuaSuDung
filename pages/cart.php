<?php
session_start();

if(!isset($_SESSION["user_id"])){
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Giỏ hàng</title>

<link rel="stylesheet" href="../assets/css/style.css">

<style>

.cart-table{
width:100%;
border-collapse:collapse;
margin-top:25px;
background:#fff;
border-radius:12px;
overflow:hidden;
box-shadow:0 5px 18px rgba(0,0,0,.08);
}

.cart-table th{
background:#2563eb;
color:#fff;
padding:14px;
}

.cart-table td{
padding:16px;
border-bottom:1px solid #eee;
vertical-align:middle;
}

.cart-table img{
width:90px;
height:90px;
object-fit:cover;
border-radius:8px;
}

.qty{
display:flex;
align-items:center;
gap:8px;
}

.qty button{
width:30px;
height:30px;
border:none;
background:#2563eb;
color:#fff;
border-radius:6px;
cursor:pointer;
}

.remove-btn{

background:#dc2626;
color:#fff;
border:none;
padding:8px 14px;
border-radius:6px;
cursor:pointer;

}

.checkout{

margin-top:25px;
display:flex;
justify-content:flex-end;
gap:20px;
align-items:center;

}

.checkout h2{

margin:0;

}

.checkout button{

padding:14px 28px;
background:#16a34a;
border:none;
color:#fff;
border-radius:8px;
font-size:16px;
cursor:pointer;

}

.empty-cart{

text-align:center;
padding:80px 0;

}

.empty-cart h2{

color:#666;

}

</style>

</head>

<body>

<header>

<div class="header-inner">

<a class="logo" href="../index.html">
Đồ Cũ<span>VN</span>
</a>

<nav class="header-nav">

<a href="../index.html" class="btn btn-outline">
Trang chủ
</a>

<a href="../products.html" class="btn btn-outline">
Sản phẩm
</a>

<a href="my-orders.php" class="btn btn-outline">
Đơn hàng
</a>

<a href="logout.php" class="btn btn-outline">
Đăng xuất
</a>

</nav>

</div>

</header>

<div class="container">

<h1 style="margin-top:30px">
🛒 Giỏ hàng của bạn
</h1>

<div id="cart-content">

<div class="loading">

<div class="spinner"></div>

<p>Đang tải...</p>

</div>

</div>

</div>
<script>

loadCart();

function loadCart(){

fetch("../api/cart/list.php")

.then(r=>r.json())

.then(data=>{

if(!data.success){

alert(data.message);

return;

}

renderCart(data);

});

}

function renderCart(data){

const box=document.getElementById("cart-content");

if(data.items.length==0){

box.innerHTML=`

<div class="empty-cart">

<h2>🛒 Giỏ hàng đang trống</h2>

<p>Hãy thêm sản phẩm để tiếp tục.</p>

<a href="../products.html" class="btn btn-primary">

Đi mua sắm

</a>

</div>

`;

return;

}

let html=`

<table class="cart-table">

<tr>

<th>Ảnh</th>

<th>Sản phẩm</th>

<th>Đơn giá</th>

<th>Số lượng</th>

<th>Thành tiền</th>

<th></th>

</tr>

`;

data.items.forEach(item=>{

html+=`

<tr>

<td>

<img src="../${item.image}">

</td>

<td>

${item.title}

</td>

<td>

${item.price_formatted}

</td>

<td>

<div class="qty">

<button onclick="changeQty(${item.id},${item.quantity-1})">-</button>

<span>${item.quantity}</span>

<button onclick="changeQty(${item.id},${item.quantity+1})">+</button>

</div>

</td>

<td>

${item.subtotal_formatted}

</td>

<td>

<button
class="remove-btn"
onclick="removeItem(${item.id})">

Xóa

</button>

</td>

</tr>

`;

});
html+=`

</table>

<div class="checkout">

<h2>

Tổng cộng:
<b style="color:#e11d48">
${data.total_formatted}
</b>

</h2>

<button onclick="checkout()">

💳 Thanh toán VNPAY

</button>

</div>

`;

box.innerHTML=html;

}

function removeItem(id){

if(!confirm("Xóa sản phẩm này khỏi giỏ hàng?")) return;

fetch("../api/cart/remove.php",{

method:"POST",

headers:{
"Content-Type":"application/x-www-form-urlencoded"
},

body:"cart_id="+id

})

.then(r=>r.json())

.then(res=>{

alert(res.message);

if(res.success){

loadCart();

}

});

}

function changeQty(id,qty){

if(qty<=0){

removeItem(id);

return;

}

fetch("../api/cart/update.php",{

method:"POST",

headers:{
"Content-Type":"application/x-www-form-urlencoded"
},

body:
"cart_id="+id+
"&quantity="+qty

})

.then(r=>r.json())

.then(res=>{

if(res.success){

loadCart();

}else{

alert(res.message);

}

});

}

function checkout(){

fetch("../api/cart/checkout.php",{

method:"POST"

})

.then(r=>r.json())

.then(res=>{

if(!res.success){

alert(res.message);

return;

}

window.location=res.payment_url;

});

}

</script>

</body>
</html>