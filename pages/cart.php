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
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Giỏ hàng – Đồ Cũ VN</title>

<link rel="stylesheet" href="../assets/css/style.css">

<style>
/* ---- Trang giỏ hàng (dùng chung theme của assets/css/style.css) ---- */
.cart-wrap{ padding:24px 0 60px; }
.cart-title{ font-size:1.4rem; font-weight:800; margin:6px 0 20px; }

.cart-box{
    background:#fff; border:1px solid #e2e8f0; border-radius:14px;
    padding:6px; overflow:hidden;
}

.cart-table{ width:100%; border-collapse:collapse; }
.cart-table thead th{
    background:#f8fafc; color:#475569; text-align:left;
    padding:14px 16px; font-size:0.82rem; font-weight:700;
    border-bottom:1px solid #e2e8f0;
}
.cart-table thead th:nth-child(3),
.cart-table thead th:nth-child(4),
.cart-table thead th:nth-child(5){ text-align:center; }

.cart-table td{
    padding:16px; border-bottom:1px solid #f1f5f9; vertical-align:middle;
    font-size:0.9rem;
}
.cart-table tr:last-child td{ border-bottom:none; }
.cart-table img{
    width:74px; height:74px; object-fit:cover; border-radius:10px;
    border:1px solid #e2e8f0;
}
.cart-item-title{ font-weight:700; color:#1e293b; }
.cart-price-col{ text-align:center; color:#64748b; }
.cart-sub-col{ text-align:center; font-weight:800; color:#f97316; }

.qty{ display:flex; align-items:center; justify-content:center; gap:10px; }
.qty button{
    width:30px; height:30px; border:1px solid #d8dee8; border-radius:7px;
    cursor:pointer; background:#fff; color:#2563eb; font-size:16px; font-weight:700;
    transition:.15s;
}
.qty button:hover{ background:#eff6ff; }
.qty span{ width:26px; text-align:center; font-weight:700; }

.remove-btn{
    background:#fef2f2; color:#dc2626; border:none;
    padding:8px 14px; border-radius:8px; cursor:pointer;
    font-size:0.82rem; font-weight:600; transition:.15s;
}
.remove-btn:hover{ background:#fee2e2; }

.summary{ margin-top:22px; display:flex; justify-content:flex-end; }
.summary-box{
    width:380px; background:#fff; border:1px solid #e2e8f0;
    padding:20px; border-radius:14px;
}
.summary-row{
    display:flex; justify-content:space-between; margin-bottom:14px;
    font-size:0.92rem; color:#475569;
}
.summary-row hr{ display:none; }
.summary-box hr{ border:none; border-top:1px solid #f1f5f9; margin:14px 0; }
.total{ font-size:1rem; font-weight:800; color:#1e293b; margin-bottom:0; }
.total span:last-child{ font-size:1.35rem; font-weight:900; color:#f97316; }

.payment-box{
    margin-top:20px; background:#fff; border:1px solid #e2e8f0;
    padding:20px; border-radius:14px;
}
.payment-box h3{ margin-top:0; font-size:1rem; font-weight:800; }
.payment-item{
    margin:10px 0; padding:12px 14px; border:1px solid #e2e8f0;
    border-radius:10px; transition:.15s; cursor:pointer;
}
.payment-item:has(input:checked){ border-color:#2563eb; background:#eff6ff; }
.payment-item label{ cursor:pointer; font-size:0.92rem; font-weight:600; display:flex; align-items:center; gap:10px; }

#momo-box{
    display:none; margin-top:18px; padding:20px;
    border:2px dashed #f97316; border-radius:14px;
    text-align:center; background:#fff7ed;
}
#momo-box img{ width:220px; border-radius:14px; }
#momo-box h4{ margin:14px 0 6px; font-size:0.98rem; }
#momo-box p{ margin:5px; font-size:0.88rem; color:#475569; }

.checkout{ margin-top:24px; text-align:right; }
.checkout button{
    background:#f97316; color:#fff; border:none;
    padding:15px 34px; font-size:1rem; font-weight:800;
    border-radius:10px; cursor:pointer; transition:.15s;
}
.checkout button:hover{ background:#ea6c00; }

.empty{
    padding:70px 20px; text-align:center; color:#94a3b8; font-size:0.95rem;
}
.empty::before{ content:''; }

@media (max-width:800px){
    .summary, .payment-box{ width:100%; }
    .summary-box{ width:100%; }
    .cart-table{ font-size:0.82rem; }
    .cart-table img{ width:56px; height:56px; }
}
</style>

</head>

<body>

<header>
    <div class="header-inner">
        <a class="logo" href="../index.html">Đồ Cũ<span>VN</span></a>
        <nav class="header-nav" style="margin-left:auto;">
            <a href="../index.html" class="btn btn-outline">Trang chủ</a>
            <a href="../products.html" class="btn btn-outline">Sản phẩm</a>
            <a href="my-orders.php" class="btn btn-outline">Đơn hàng</a>
            <a href="logout.php" class="btn btn-primary">Đăng xuất</a>
        </nav>
    </div>
</header>

<div class="container cart-wrap">

    <h1 class="cart-title">🛒 Giỏ hàng của bạn</h1>

    <div class="cart-box">
        <div id="cart-content">
            <div class="empty">Đang tải...</div>
        </div>
    </div>

    <div class="summary">
        <div class="summary-box">
            <div class="summary-row">
                <span>Tạm tính</span>
                <strong id="subtotal">0đ</strong>
            </div>
            <div class="summary-row">
                <span>Phí vận chuyển</span>
                <strong>0đ</strong>
            </div>
            <hr>
            <div class="summary-row total">
                <span>Tổng cộng</span>
                <span id="grand-total">0đ</span>
            </div>
        </div>
    </div>

    <div class="payment-box">
        <h3>💳 Phương thức thanh toán</h3>

        <div class="payment-item">
            <label>
                <input type="radio" name="payment" value="vnpay" checked>
                VNPAY Sandbox
            </label>
        </div>

        <div class="payment-item">
            <label>
                <input type="radio" name="payment" value="momo">
                MoMo QR
            </label>
        </div>

        <div class="payment-item">
            <label>
                <input type="radio" name="payment" value="cod">
                Thanh toán khi nhận hàng (COD)
            </label>
        </div>

        <div id="momo-box">
            <h4>Quét mã MoMo</h4>
            <img src="../assets/images/momo-qr.jpg" alt="Mã QR MoMo">
            <p><b>Chủ tài khoản:</b> Nguyễn Văn Huy</p>
            <p><b>SĐT:</b> 09xxxxxxxx</p>
            <p style="color:#ef4444;">Sau khi chuyển khoản hãy bấm "Thanh toán"</p>
        </div>
    </div>

    <div class="checkout">
        <button onclick="checkout()">💳 Thanh toán</button>
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

<span class="cart-item-title">${item.title}</span>

</td>

<td class="cart-price-col">

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

<td class="cart-sub-col">

${money(sub)}

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
        "../api/payment/vnpay_create.php";

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