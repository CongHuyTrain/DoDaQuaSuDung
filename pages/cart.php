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

<meta name="viewport"
content="width=device-width,initial-scale=1.0">

<title>Giỏ hàng</title>

<link rel="stylesheet"
href="../assets/css/style.css">

<style>

.container{

max-width:1200px;
margin:30px auto;
padding:20px;

}

.cart-box{

background:#fff;
border-radius:14px;
padding:25px;
box-shadow:0 8px 30px rgba(0,0,0,.08);

}

.cart-table{

width:100%;
border-collapse:collapse;

}

.cart-table th{

background:#2563eb;
color:#fff;
padding:14px;
font-size:15px;

}

.cart-table td{

padding:15px;
border-bottom:1px solid #eee;
vertical-align:middle;

}

.cart-table img{

width:90px;
height:90px;
object-fit:cover;
border-radius:10px;

}

.qty{

display:flex;
align-items:center;
justify-content:center;
gap:8px;

}

.qty button{

width:32px;
height:32px;
border:none;
border-radius:8px;
cursor:pointer;
background:#2563eb;
color:#fff;
font-size:18px;

}

.qty span{

width:35px;
text-align:center;
font-weight:bold;

}

.remove-btn{

background:#ef4444;
color:#fff;
border:none;
padding:8px 15px;
border-radius:8px;
cursor:pointer;

}

.summary{

margin-top:25px;
display:flex;
justify-content:flex-end;

}

.summary-box{

width:380px;
background:#f8fafc;
padding:20px;
border-radius:12px;

}

.summary-row{

display:flex;
justify-content:space-between;
margin-bottom:15px;
font-size:17px;

}

.total{

font-size:24px;
font-weight:bold;
color:#dc2626;

}

.payment-box{

margin-top:25px;
background:#fff;
padding:20px;
border-radius:12px;
box-shadow:0 5px 20px rgba(0,0,0,.08);

}

.payment-box h3{

margin-top:0;

}

.payment-item{

margin:15px 0;

}

.payment-item label{

cursor:pointer;
font-size:16px;

}

#momo-box{

display:none;
margin-top:20px;
padding:20px;
border:2px dashed #ff4fa3;
border-radius:15px;
text-align:center;
background:#fff7fb;

}

#momo-box img{

width:240px;
border-radius:15px;

}

#momo-box h4{

margin:15px 0 8px;

}

#momo-box p{

margin:6px;

}

.checkout{

margin-top:30px;
text-align:right;

}

.checkout button{

background:#16a34a;
color:#fff;
border:none;
padding:16px 35px;
font-size:18px;
border-radius:10px;
cursor:pointer;

}

.checkout button:hover{

background:#15803d;

}

.empty{

padding:80px;
text-align:center;
font-size:20px;
color:#888;

}

</style>

</head>

<body>

<header>

<div class="header-inner">

<a class="logo"
href="../index.html">

Đồ Cũ<span>VN</span>

</a>

<nav class="header-nav">

<a href="../index.html"
class="btn btn-outline">

Trang chủ

</a>

<a href="../products.html"
class="btn btn-outline">

Sản phẩm

</a>

<a href="my-orders.php"
class="btn btn-outline">

Đơn hàng

</a>

<a href="logout.php"
class="btn btn-primary">

Đăng xuất

</a>

</nav>

</div>

</header>

<div class="container">

<div class="cart-box">

<h2>

🛒 Giỏ hàng của bạn

</h2>

<div id="cart-content">

<div class="empty">

Đang tải...

</div>

</div>

</div>

<div class="summary">

<div class="summary-box">

<div class="summary-row">

<span>

Tạm tính

</span>

<strong id="subtotal">

0đ

</strong>

</div>

<div class="summary-row">

<span>

Phí vận chuyển

</span>

<strong>

0đ

</strong>

</div>

<hr>

<div class="summary-row total">

<span>

Tổng cộng

</span>

<span id="grand-total">

0đ

</span>

</div>

</div>

</div>

<div class="payment-box">

<h3>

💳 Phương thức thanh toán

</h3>

<div class="payment-item">

<label>

<input
type="radio"
name="payment"
value="vnpay"
checked>

VNPAY Sandbox

</label>

</div>

<div class="payment-item">

<label>

<input
type="radio"
name="payment"
value="momo">

MoMo QR

</label>

</div>

<div class="payment-item">

<label>

<input
type="radio"
name="payment"
value="cod">

Thanh toán khi nhận hàng (COD)

</label>

</div>

<div id="momo-box">

<h4>

Quét mã MoMo

</h4>

<img
src="../assets/images/momo-qr.jpg">

<p>

<b>Chủ tài khoản:</b>

Nguyễn Văn Huy

</p>

<p>

<b>SĐT:</b>

09xxxxxxxx

</p>

<p style="color:#ef4444;">

Sau khi chuyển khoản hãy bấm

"Thanh toán"

</p>

</div>

</div>

<div class="checkout">

<button
onclick="checkout()">

💳 Thanh toán

</button>

</div>

</div>
<script>

let cart = [];

document.querySelectorAll("input[name=payment]").forEach(r=>{

    r.onchange=function(){

        if(this.value=="momo"){

            document.getElementById("momo-box").style.display="block";

        }else{

            document.getElementById("momo-box").style.display="none";

        }

    }

});

function money(v){

    return Number(v).toLocaleString("vi-VN")+" đ";

}

function loadCart(){

fetch("../api/cart/list.php")

.then(r=>r.json())

.then(data=>{

    if(!data.success){

        document.getElementById("cart-content").innerHTML=
        '<div class="empty">'+data.message+'</div>';

        return;

    }

    cart=data.items||[];

    renderCart();

});

}

function renderCart(){

if(cart.length==0){

document.getElementById("cart-content").innerHTML=
`
<div class="empty">

🛒 Giỏ hàng đang trống

</div>
`;

document.getElementById("subtotal").innerHTML="0 đ";
document.getElementById("grand-total").innerHTML="0 đ";

return;

}

let html=`

<table class="cart-table">

<thead>

<tr>

<th>Ảnh</th>

<th>Sản phẩm</th>

<th>Giá</th>

<th>Số lượng</th>

<th>Thành tiền</th>

<th></th>

</tr>

</thead>

<tbody>

`;

let total=0;

cart.forEach(item=>{

const price=Number(item.price);

const qty=Number(item.quantity);

const sub=price*qty;

total+=sub;

html+=`

<tr>

<td>

<img
src="../${item.image}"
alt="">

</td>

<td>

<b>${item.title}</b>

</td>

<td>

${money(price)}

</td>

<td>

<div class="qty">

<button
onclick="updateQty(${item.product_id},${qty-1})">

-

</button>

<span>

${qty}

</span>

<button
onclick="updateQty(${item.product_id},${qty+1})">

+

</button>

</div>

</td>

<td>

<b>

${money(sub)}

</b>

</td>

<td>

<button
class="remove-btn"
onclick="removeItem(${item.product_id})">

Xóa

</button>

</td>

</tr>

`;

});

html+=`

</tbody>

</table>

`;

document.getElementById("cart-content").innerHTML=html;

document.getElementById("subtotal").innerHTML=money(total);

document.getElementById("grand-total").innerHTML=money(total);

}

function updateQty(product_id,qty){

if(qty<=0){

removeItem(product_id);

return;

}

fetch("../api/cart/update.php",{

method:"POST",

headers:{

"Content-Type":"application/x-www-form-urlencoded"

},

body:

"product_id="+product_id+

"&quantity="+qty

})

.then(r=>r.json())

.then(data=>{

if(data.success){

loadCart();

}else{

alert(data.message);

}

});

}

function removeItem(product_id){

if(!confirm("Xóa sản phẩm khỏi giỏ?")){

return;

}

fetch("../api/cart/remove.php",{

method:"POST",

headers:{

"Content-Type":"application/x-www-form-urlencoded"

},

body:

"product_id="+product_id

})

.then(r=>r.json())

.then(data=>{

if(data.success){

loadCart();

}else{

alert(data.message);

}

});

}

loadCart();

</script>
<script>

async function checkout(){

    if(cart.length==0){

        alert("Giỏ hàng đang trống.");

        return;

    }

    const payment=document.querySelector(
        "input[name=payment]:checked"
    ).value;

    if(payment=="vnpay"){

        location.href=
        "../payment/vnpay_create.php";

        return;

    }

    if(payment=="momo"){

        if(!confirm(
            "Bạn xác nhận đã chuyển khoản MoMo?"
        )){
            return;
        }

    }

    try{

        const response=await fetch(
            "../api/cart/checkout.php",
            {

                method:"POST",

                headers:{
                    "Content-Type":
                    "application/x-www-form-urlencoded"
                },

                body:
                "payment_method="+
                encodeURIComponent(payment)

            }
        );

        const data=await response.json();

        if(!data.success){

            alert(data.message);

            return;

        }

        alert(data.message);

        location.href="my-orders.php";

    }
    catch(e){

        alert("Không thể kết nối tới máy chủ.");

    }

}

</script>

</body>
</html>