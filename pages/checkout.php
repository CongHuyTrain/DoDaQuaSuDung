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
<title>Thanh toán – Đồ Cũ VN</title>

<link rel="stylesheet" href="../assets/css/style.css">

<style>
.checkout-wrap{ padding:24px 0 60px; }
.checkout-title{ font-size:1.4rem; font-weight:800; margin:6px 0 20px; }

.checkout-box{
    background:#fff; border:1px solid #e2e8f0; border-radius:14px;
    padding:6px 20px;
}

.checkout-row{
    display:flex; align-items:center; gap:16px;
    padding:16px 0; border-bottom:1px solid #f1f5f9;
}
.checkout-row:last-child{ border-bottom:none; }
.checkout-row img{
    width:64px; height:64px; object-fit:cover; border-radius:10px;
    border:1px solid #e2e8f0; flex-shrink:0;
}
.checkout-info{ flex:1; }
.checkout-title-item{ font-weight:700; color:#1e293b; margin-bottom:4px; }
.checkout-price{ font-size:0.85rem; color:#64748b; }
.checkout-sub{ font-weight:800; color:#f97316; white-space:nowrap; }

.checkout-total-line{
    display:flex; justify-content:flex-end; align-items:center; gap:10px;
    padding:16px 0; font-size:0.95rem; color:#475569;
}
.checkout-total-line strong{ color:#f97316; font-size:1.2rem; }

.payment-box{
    margin-top:20px; background:#fff; border:1px solid #e2e8f0;
    padding:20px; border-radius:14px;
}
.payment-box h3{ margin-top:0; font-size:1rem; font-weight:800; }

.payment-selected{
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 0;
}
.payment-selected-label{ font-weight:600; color:#1e293b; }
.change-btn{
    background:none; border:none; color:#2563eb; font-weight:700;
    cursor:pointer; font-size:0.9rem;
}
.change-btn:hover{ text-decoration:underline; }

.payment-options{ margin-top:10px; }
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

.summary-box{
    margin-top:20px; background:#fff; border:1px solid #e2e8f0;
    padding:20px; border-radius:14px;
}
.summary-row{
    display:flex; justify-content:space-between; margin-bottom:14px;
    font-size:0.92rem; color:#475569;
}
.summary-box hr{ border:none; border-top:1px solid #f1f5f9; margin:14px 0; }
.total{ font-size:1rem; font-weight:800; color:#1e293b; margin-bottom:0; }
.total span:last-child{ font-size:1.35rem; font-weight:900; color:#f97316; }

.place-order{ margin-top:24px; text-align:right; }
.place-order button{
    background:#f97316; color:#fff; border:none;
    padding:15px 34px; font-size:1rem; font-weight:800;
    border-radius:10px; cursor:pointer; transition:.15s;
}
.place-order button:hover{ background:#ea6c00; }

.back-link{ display:inline-block; margin-bottom:14px; color:#2563eb; font-weight:600; font-size:0.9rem; }

.empty{ padding:70px 20px; text-align:center; color:#94a3b8; font-size:0.95rem; }

@media (max-width:800px){
    .checkout-row img{ width:52px; height:52px; }
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

<div class="container checkout-wrap">

    <a class="back-link" href="cart.php">← Quay lại giỏ hàng</a>

    <h1 class="checkout-title">💳 Xác nhận đơn hàng</h1>

    <div class="checkout-box">
        <div id="checkout-items">
            <div class="empty">Đang tải...</div>
        </div>

        <div class="checkout-total-line">
            Tổng số tiền (<span id="item-count">0</span> sản phẩm):
            <strong id="item-total">0đ</strong>
        </div>
    </div>

    <div class="payment-box">
        <h3>Phương thức thanh toán</h3>

        <div class="payment-selected" id="payment-selected-view">
            <span class="payment-selected-label" id="payment-selected-label">Thanh toán khi nhận hàng</span>
            <button class="change-btn" onclick="togglePaymentOptions()">THAY ĐỔI</button>
        </div>

        <div class="payment-options" id="payment-options" style="display:none;">

            <div class="payment-item">
                <label>
                    <input type="radio" name="payment" value="cod" checked>
                    Thanh toán khi nhận hàng (COD)
                </label>
            </div>

            <div class="payment-item">
                <label>
                    <input type="radio" name="payment" value="vnpay">
                    VNPAY Sandbox
                </label>
            </div>

            <div class="payment-item">
                <label>
                    <input type="radio" name="payment" value="momo">
                    MoMo QR
                </label>
            </div>

        </div>

        <div id="momo-box">
            <h4>Quét mã MoMo</h4>
            <img src="../assets/images/momo-qr.jpg" alt="Mã QR MoMo">
            <p><b>Chủ tài khoản:</b> Nguyễn Văn Huy</p>
            <p><b>SĐT:</b> 09xxxxxxxx</p>
            <p style="color:#ef4444;">Sau khi chuyển khoản hãy bấm "Đặt hàng"</p>
        </div>
    </div>

    <div class="summary-box">
        <div class="summary-row">
            <span>Tổng tiền hàng</span>
            <strong id="sum-item-total">0đ</strong>
        </div>
        <div class="summary-row">
            <span>Phí vận chuyển</span>
            <strong id="sum-shipping">0đ</strong>
        </div>
        <hr>
        <div class="summary-row total">
            <span>Tổng thanh toán</span>
            <span id="grand-total">0đ</span>
        </div>
    </div>

    <div class="place-order">
        <button onclick="placeOrder()">Đặt hàng</button>
    </div>

</div>

<script>

let selectedItems=[];

// Phí vận chuyển tạm tính cố định (dự án chưa có bảng tính phí ship thực tế)
const SHIPPING_FEE=30000;

const paymentLabels={
    cod:"Thanh toán khi nhận hàng (COD)",
    vnpay:"VNPAY Sandbox",
    momo:"MoMo QR"
};

function money(v){
    return Number(v).toLocaleString("vi-VN")+" đ";
}

function togglePaymentOptions(){
    const box=document.getElementById("payment-options");
    box.style.display = (box.style.display==="none") ? "block" : "none";
}

document.querySelectorAll("input[name=payment]").forEach(r=>{
    r.onchange=function(){

        document.getElementById("payment-selected-label").textContent=paymentLabels[this.value];
        document.getElementById("momo-box").style.display = (this.value==="momo") ? "block" : "none";
        document.getElementById("payment-options").style.display="none";

    };
});

function loadCheckout(){

    let ids=[];

    try{
        ids=JSON.parse(sessionStorage.getItem("checkout_ids"))||[];
    }catch(e){
        ids=[];
    }

    if(ids.length===0){
        alert("Vui lòng chọn sản phẩm từ giỏ hàng trước khi thanh toán.");
        location.href="cart.php";
        return;
    }

    fetch("../api/cart/list.php")
    .then(r=>r.json())
    .then(data=>{

        if(!data.success){
            alert(data.message||"Không tải được giỏ hàng.");
            location.href="cart.php";
            return;
        }

        const cart=data.items||[];

        selectedItems=cart.filter(i=>ids.includes(i.product_id));

        if(selectedItems.length===0){
            alert("Sản phẩm đã chọn không còn trong giỏ hàng.");
            location.href="cart.php";
            return;
        }

        renderCheckout();

    });

}

function renderCheckout(){

    let html="";
    let itemTotal=0;

    selectedItems.forEach(item=>{

        const price=Number(item.price);
        const qty=Number(item.quantity);
        const sub=price*qty;

        itemTotal+=sub;

        html+=`
        <div class="checkout-row">
            <img src="../${item.image}" alt="">
            <div class="checkout-info">
                <div class="checkout-title-item">${item.title}</div>
                <div class="checkout-price">${money(price)} x ${qty}</div>
            </div>
            <div class="checkout-sub">${money(sub)}</div>
        </div>
        `;

    });

    document.getElementById("checkout-items").innerHTML=html;

    document.getElementById("item-count").textContent=selectedItems.length;
    document.getElementById("item-total").textContent=money(itemTotal);

    document.getElementById("sum-item-total").textContent=money(itemTotal);
    document.getElementById("sum-shipping").textContent=money(SHIPPING_FEE);
    document.getElementById("grand-total").textContent=money(itemTotal+SHIPPING_FEE);

}

async function placeOrder(){

    const payment=document.querySelector("input[name=payment]:checked").value;

    if(payment==="vnpay"){
        // Lưu ý: vnpay_create.php hiện xử lý theo toàn bộ giỏ hàng,
        // chưa hỗ trợ chọn riêng từng sản phẩm.
        location.href="../api/payment/vnpay_create.php";
        return;
    }

    if(payment==="momo" && !confirm("Bạn xác nhận đã chuyển khoản MoMo?")){
        return;
    }

    const product_ids=selectedItems.map(i=>i.product_id);

    try{

        const res=await fetch("../api/cart/checkout.php",{
            method:"POST",
            headers:{ "Content-Type":"application/json" },
            body:JSON.stringify({
                payment_method:payment,
                product_ids:product_ids
            })
        });

        const data=await res.json();

        if(!data.success){
            alert(data.message);
            return;
        }

        sessionStorage.removeItem("checkout_ids");

        alert(data.message);

        location.href="my-orders.php";

    }catch(e){

        alert("Không thể kết nối tới máy chủ.");

    }

}

loadCheckout();

</script>

</body>
</html>