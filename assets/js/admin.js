// assets/js/admin.js
// Dùng riêng cho khu vực /admin (products.php, users.php, orders.php, ...)
// File này TRƯỚC ĐÂY không tồn tại trên server dù các trang admin có gọi tới
// (<script src="../assets/js/admin.js">) -> toggle sidebar và hộp thoại xác
// nhận "Xóa/Khóa..." không hoạt động do 2 hàm dưới đây bị thiếu.

// Toggle sidebar trên di động
function toggleAdminSidebar(){
    document.getElementById('admin-sidebar').classList.toggle('open');
}

// Đóng sidebar khi bấm ra ngoài (trên mobile)
document.addEventListener('click', function(e){
    const sidebar = document.getElementById('admin-sidebar');
    const toggle = document.getElementById('admin-sidebar-toggle');
    if (!sidebar || !toggle) return;
    if (window.innerWidth > 900) return;
    if (sidebar.classList.contains('open') &&
        !sidebar.contains(e.target) &&
        !toggle.contains(e.target)) {
        sidebar.classList.remove('open');
    }
});

// Xác nhận trước khi thực hiện hành động nguy hiểm (xóa, chặn, từ chối...)
function confirmAction(message){
    return confirm(message);
}