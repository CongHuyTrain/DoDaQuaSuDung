
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


function confirmAction(message){
    return confirm(message);
}