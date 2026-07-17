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
.cart-table thead th:nth-child(1){ width:44px; text-align:center; }
.cart-table thead th:nth-child(4),
.cart-table thead th:nth-child(5),
.cart-table thead th:nth-child(6){ text-align:center; }

.cart-table td{
    padding:16px; border-bottom:1px solid #f1f5f9; vertical-align:middle;
    font-size:0.9rem;
}
.cart-table tr:last-child td{ border-bottom:none; }
.cart-table img{
    width:74px; height:74px; object-fit:cover; border-radius:10px;
    border:1px solid #e2e8f0;
}
.cart-check-col{ text-align:center; }
.cart-check-col input[type=checkbox]{ width:18px; height:18px; cursor:pointer; accent-color:#f97316; }
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

/* ---- Thanh dưới cùng kiểu Shopee: chọn tất cả / xóa / tổng tiền / mua hàng ---- */
.cart-footer{
    margin-top:18px; background:#fff; border:1px solid #e2e8f0;
    border-radius:14px; padding:16px 20px;
    display:flex; align-items:center; gap:20px; flex-wrap:wrap;
}
.footer-check{
    display:flex; align-items:center; gap:8px; cursor:pointer;
    font-size:0.92rem; font-weight:600; color:#1e293b; white-space:nowrap;
}
.footer-check input[type=checkbox]{ width:18px; height:18px; cursor:pointer; accent-color:#f97316; }
.footer-remove{
    background:none; border:none; color:#f97316; font-weight:700;
    cursor:pointer; font-size:0.9rem;
}
.footer-remove:hover{ text-decoration:underline; }

.footer-total{ margin-left:auto; text-align:right; }
.footer-total-label{ font-size:0.85rem; color:#64748b; }
.footer-total-label span{ font-weight:700; color:#1e293b; }
.footer-total-amount{ font-size:1.35rem; font-weight:900; color:#f97316; }

.footer-buy{
    background:#f97316; color:#fff; border:none;
    padding:14px 36px; font-size:1rem; font-weight:800;
    border-radius:10px; cursor:pointer; transition:.15s; white-space:nowrap;
}
.footer-buy:hover{ background:#ea6c00; }

.empty{
    padding:70px 20px; text-align:center; color:#94a3b8; font-size:0.95rem;
}

@media (max-width:800px){
    .cart-table{ font-size:0.82rem; }
    .cart-table img{ width:56px; height:56px; }
    .cart-footer{ flex-direction:column; align-items:stretch; gap:12px; }
    .footer-total{ margin-left:0; text-align:left; }
    .footer-buy{ width:100%; }
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

    <div class="cart-footer" id="cart-footer" style="display:none;">
        <label class="footer-check">
            <input type="checkbox" id="check-all-footer" onclick="toggleAll(this.checked)">
            Chọn Tất Cả (<span id="total-count">0</span>)
        </label>

        <button class="footer-remove" onclick="removeSelected()">Xóa</button>

        <div class="footer-total">
            <div class="footer-total-label">
                Tổng cộng (<span id="selected-count">0</span> sản phẩm):
            </div>
            <div class="footer-total-amount" id="footer-total-amount">0đ</div>
        </div>

        <button class="footer-buy" onclick="goCheckout()">Mua Hàng</button>
    </div>

</div>

<script>

let cart = [];
let selectedIds = new Set();

function money(v){
    return Number(v).toLocaleString("vi-VN")+" đ";
}

function loadCart(){

    fetch("../api/cart/list.php")
    .then(r=>r.json())
    .then(data=>{

        if(!data.success){
            document.getElementById("cart-content").innerHTML=
            '<div class="empty">'+(data.message||"Không tải được giỏ hàng.")+'</div>';
            document.getElementById("cart-footer").style.display="none";
            return;
        }

        cart=data.items||[];

        // Mặc định chọn tất cả sản phẩm trong giỏ
        selectedIds=new Set(cart.map(i=>i.product_id));

        renderCart();

    });

}

function renderCart(){

    if(cart.length==0){

        document.getElementById("cart-content").innerHTML=
        `<div class="empty">🛒 Giỏ hàng đang trống</div>`;

        document.getElementById("cart-footer").style.display="none";

        return;

    }

    document.getElementById("cart-footer").style.display="flex";

    let html=`
    <table class="cart-table">
    <thead>
    <tr>
        <th><input type="checkbox" id="check-all-header" onclick="toggleAll(this.checked)"></th>
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

    cart.forEach(item=>{

        const price=Number(item.price);
        const qty=Number(item.quantity);
        const sub=price*qty;
        const checked=selectedIds.has(item.product_id)?"checked":"";

        html+=`
        <tr>
            <td class="cart-check-col">
                <input type="checkbox" ${checked}
                    onchange="toggleItem(${item.product_id}, this.checked)">
            </td>
            <td>
                <img src="../${item.image}" alt="">
            </td>
            <td>
                <span class="cart-item-title">${item.title}</span>
            </td>
            <td class="cart-price-col">
                ${money(price)}
            </td>
            <td>
                <div class="qty">
                    <button onclick="updateQty(${item.product_id},${qty-1})">-</button>
                    <span>${qty}</span>
                    <button onclick="updateQty(${item.product_id},${qty+1})">+</button>
                </div>
            </td>
            <td class="cart-sub-col">
                ${money(sub)}
            </td>
            <td>
                <button class="remove-btn" onclick="removeItem(${item.product_id})">Xóa</button>
            </td>
        </tr>
        `;

    });

    html+=`</tbody></table>`;

    document.getElementById("cart-content").innerHTML=html;

    updateFooter();

}

function updateFooter(){

    const selectedItems=cart.filter(i=>selectedIds.has(i.product_id));
    const total=selectedItems.reduce((s,i)=> s + Number(i.price)*Number(i.quantity), 0);

    document.getElementById("total-count").textContent=cart.length;
    document.getElementById("selected-count").textContent=selectedItems.length;
    document.getElementById("footer-total-amount").textContent=money(total);

    const allChecked = cart.length>0 && selectedIds.size===cart.length;

    const headerBox=document.getElementById("check-all-header");
    const footerBox=document.getElementById("check-all-footer");

    if(headerBox) headerBox.checked=allChecked;
    if(footerBox) footerBox.checked=allChecked;

}

function toggleAll(checked){

    if(checked){
        selectedIds=new Set(cart.map(i=>i.product_id));
    }else{
        selectedIds.clear();
    }

    renderCart();

}

function toggleItem(product_id, checked){

    if(checked){
        selectedIds.add(product_id);
    }else{
        selectedIds.delete(product_id);
    }

    updateFooter();

}

function updateQty(product_id,qty){

    if(qty<=0){
        removeItem(product_id);
        return;
    }

    fetch("../api/cart/update.php",{
        method:"POST",
        headers:{ "Content-Type":"application/x-www-form-urlencoded" },
        body:"product_id="+product_id+"&quantity="+qty
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
        headers:{ "Content-Type":"application/x-www-form-urlencoded" },
        body:"product_id="+product_id
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

function removeSelected(){

    if(selectedIds.size==0){
        alert("Vui lòng chọn sản phẩm cần xóa.");
        return;
    }

    if(!confirm("Xóa "+selectedIds.size+" sản phẩm đã chọn khỏi giỏ hàng?")){
        return;
    }

    const ids=Array.from(selectedIds);

    Promise.all(ids.map(id=>
        fetch("../api/cart/remove.php",{
            method:"POST",
            headers:{ "Content-Type":"application/x-www-form-urlencoded" },
            body:"product_id="+id
        }).then(r=>r.json())
    ))
    .then(()=> loadCart());

}

function goCheckout(){

    if(selectedIds.size==0){
        alert("Vui lòng chọn ít nhất 1 sản phẩm để mua.");
        return;
    }

    sessionStorage.setItem("checkout_ids", JSON.stringify(Array.from(selectedIds)));

    location.href="checkout.php";

}

loadCart();

</script>

</body>
</html>