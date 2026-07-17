<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

require_once '../config/db.php';

$user_id = (int) $_SESSION['user_id'];

// Luôn lấy dữ liệu mới nhất từ CSDL (không dựa vào session cũ)
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    session_destroy();
    header("Location: login.html");
    exit;
}

// Đồng bộ lại session cho các trang khác dùng chung
$_SESSION['username'] = $user['username'];
$_SESSION['fullname'] = $user['fullname'];
$_SESSION['email']    = $user['email'];
$_SESSION['role']     = $user['role'];

// Đếm thông báo chưa đọc cho sidebar
$unreadCount = 0;
if ($r = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = $user_id AND is_read = 0")) {
    $unreadCount = (int) ($r->fetch_assoc()['cnt'] ?? 0);
}

$activeTab  = ($_GET['tab'] ?? 'profile') === 'password' ? 'password' : 'profile';
$activePage = 'profile';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài khoản của tôi – Đồ Cũ VN</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
       
        body{ background:#f5f6fa; }

        .acc-wrap{
            max-width:1120px; margin:24px auto 60px; padding:0 16px;
            display:grid; grid-template-columns:240px 1fr; gap:24px; align-items:start;
        }

        
        .acc-sidebar{
            background:#fff; border:1px solid #e2e8f0; border-radius:14px;
            padding:20px 0; position:sticky; top:20px;
        }
        .acc-sidebar-user{
            display:flex; flex-direction:column; align-items:center;
            text-align:center; padding:0 16px 18px; border-bottom:1px solid #f1f5f9; margin-bottom:8px;
        }
        .acc-sidebar-avatar{
            width:64px; height:64px; border-radius:50%; overflow:hidden;
            background:linear-gradient(135deg,#2563eb,#1d4ed8);
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-weight:800; font-size:1.4rem; margin-bottom:10px;
        }
        .acc-sidebar-avatar img{ width:100%; height:100%; object-fit:cover; }
        .acc-sidebar-username{
            font-weight:800; font-size:0.95rem; color:#1e293b;
            max-width:190px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
        }
        .acc-sidebar-edit{
            font-size:0.8rem; color:#64748b; text-decoration:none; margin-top:4px;
        }
        .acc-sidebar-edit:hover{ color:#2563eb; }

        .acc-nav-item{
            display:flex; align-items:center; gap:10px;
            padding:11px 20px; color:#334155; text-decoration:none;
            font-size:0.92rem; font-weight:600; position:relative;
        }
        .acc-nav-item:hover{ background:#f8fafc; color:#2563eb; }
        .acc-nav-item.active{ color:#2563eb; background:#eff6ff; border-right:3px solid #2563eb; }
        .acc-nav-icon{ font-size:1.05rem; width:20px; text-align:center; }
        .acc-nav-badge{
            margin-left:auto; background:#dc2626; color:#fff; font-size:0.72rem;
            font-weight:800; border-radius:999px; padding:2px 7px; line-height:1.3;
        }
        .acc-nav-parent{ cursor:default; font-weight:700; color:#1e293b; }
        .acc-nav-group.open .acc-nav-parent{ color:#2563eb; }
        .acc-nav-sub{ display:flex; flex-direction:column; }
        .acc-nav-sub a{
            padding:9px 20px 9px 50px; color:#64748b; text-decoration:none;
            font-size:0.88rem; font-weight:600;
        }
        .acc-nav-sub a:hover{ color:#2563eb; }
        .acc-nav-sub a.active{ color:#2563eb; font-weight:800; }
        .acc-nav-logout{ color:#dc2626 !important; }
        .acc-nav-logout:hover{ background:#fef2f2; }

        
        .acc-content{ min-width:0; }
        .acc-card{
            background:#fff; border:1px solid #e2e8f0; border-radius:14px;
            padding:28px 32px;
        }
        .acc-card-head{ border-bottom:1px solid #f1f5f9; padding-bottom:16px; margin-bottom:24px; }
        .acc-card-head h2{ margin:0 0 4px; font-size:1.15rem; font-weight:800; color:#1e293b; }
        .acc-card-head p{ margin:0; font-size:0.85rem; color:#64748b; }

        .acc-form-row{
            display:flex; align-items:flex-start; gap:24px; margin-bottom:20px;
        }
        .acc-form-row label{
            width:140px; flex-shrink:0; padding-top:10px; font-size:0.9rem; color:#64748b; font-weight:600;
        }
        .acc-form-field{ flex:1; max-width:420px; }
        .acc-form-field input[type="text"],
        .acc-form-field input[type="email"],
        .acc-form-field input[type="tel"],
        .acc-form-field input[type="date"],
        .acc-form-field input[type="password"],
        .acc-form-field select{
            width:100%; padding:10px 12px; border:1px solid #e2e8f0; border-radius:8px;
            font-size:0.92rem; font-family:inherit; color:#1e293b; background:#fff;
        }
        .acc-form-field input:focus,
        .acc-form-field select:focus{ outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.12); }
        .acc-form-field input:disabled{ background:#f8fafc; color:#94a3b8; }
        .acc-radio-group{ display:flex; gap:20px; padding-top:9px; }
        .acc-radio-group label{ width:auto; padding:0; display:flex; align-items:center; gap:6px; font-weight:500; color:#334155; }
        .acc-hint{ font-size:0.78rem; color:#94a3b8; margin-top:6px; }

        .acc-form-actions{ display:flex; gap:24px; }
        .acc-form-actions .spacer{ width:140px; flex-shrink:0; }
        .acc-btn-save{
            background:#2563eb; color:#fff; border:none; border-radius:8px;
            padding:11px 28px; font-size:0.92rem; font-weight:700; cursor:pointer; transition:.15s;
        }
        .acc-btn-save:hover{ background:#1d4ed8; }
        .acc-btn-save:disabled{ background:#93c5fd; cursor:not-allowed; }

        
        .acc-profile-grid{ display:flex; gap:40px; }
        .acc-profile-main{ flex:1; min-width:0; }
        .acc-avatar-col{
            width:200px; flex-shrink:0; display:flex; flex-direction:column;
            align-items:center; border-left:1px solid #f1f5f9; padding-left:40px;
        }
        .acc-avatar-preview{
            width:110px; height:110px; border-radius:50%; overflow:hidden;
            background:linear-gradient(135deg,#2563eb,#1d4ed8);
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-weight:800; font-size:2.2rem; margin-bottom:14px;
            border:3px solid #eff6ff;
        }
        .acc-avatar-preview img{ width:100%; height:100%; object-fit:cover; }
        .acc-avatar-btn{
            background:#fff; border:1px solid #cbd5e1; color:#334155;
            padding:8px 18px; border-radius:8px; font-size:0.85rem; font-weight:700;
            cursor:pointer;
        }
        .acc-avatar-btn:hover{ border-color:#2563eb; color:#2563eb; }
        .acc-avatar-hint{ font-size:0.76rem; color:#94a3b8; text-align:center; margin-top:10px; line-height:1.5; }

        
        .acc-toast{
            position:fixed; top:24px; right:24px; z-index:999;
            background:#1e293b; color:#fff; padding:13px 20px; border-radius:10px;
            font-size:0.88rem; font-weight:600; box-shadow:0 10px 30px rgba(0,0,0,.2);
            opacity:0; transform:translateY(-10px); pointer-events:none; transition:.2s;
        }
        .acc-toast.show{ opacity:1; transform:translateY(0); }
        .acc-toast.error{ background:#dc2626; }
        .acc-toast.success{ background:#16a34a; }

        @media (max-width:900px){
            .acc-wrap{ grid-template-columns:1fr; }
            .acc-sidebar{ position:static; }
            .acc-profile-grid{ flex-direction:column-reverse; gap:24px; }
            .acc-avatar-col{ border-left:none; padding-left:0; border-bottom:1px solid #f1f5f9; padding-bottom:24px; }
            .acc-form-row{ flex-direction:column; gap:8px; }
            .acc-form-row label{ width:auto; padding-top:0; }
            .acc-form-actions .spacer{ display:none; }
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
            <a href="logout.php" class="btn btn-primary">Đăng xuất</a>
        </nav>
    </div>
</header>

<div class="acc-wrap">

    <?php include __DIR__ . '/inc/account-sidebar.php'; ?>

    <div class="acc-content">

        
        <div class="acc-card" id="tab-profile" style="<?php echo $activeTab === 'profile' ? '' : 'display:none;'; ?>">
            <div class="acc-card-head">
                <h2>Hồ Sơ Của Tôi</h2>
                <p>Quản lý thông tin hồ sơ để bảo mật tài khoản</p>
            </div>

            <div class="acc-profile-grid">
                <div class="acc-profile-main">
                    <form id="profile-form">
                        <div class="acc-form-row">
                            <label>Tên đăng nhập</label>
                            <div class="acc-form-field">
                                <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                            </div>
                        </div>
                        <div class="acc-form-row">
                            <label for="f-fullname">Họ và tên</label>
                            <div class="acc-form-field">
                                <input type="text" id="f-fullname" name="fullname" maxlength="100" required
                                       value="<?php echo htmlspecialchars($user['fullname']); ?>">
                            </div>
                        </div>
                        <div class="acc-form-row">
                            <label for="f-email">Email</label>
                            <div class="acc-form-field">
                                <input type="email" id="f-email" name="email" maxlength="100" required
                                       value="<?php echo htmlspecialchars($user['email']); ?>">
                            </div>
                        </div>
                        <div class="acc-form-row">
                            <label for="f-phone">Số điện thoại</label>
                            <div class="acc-form-field">
                                <input type="tel" id="f-phone" name="phone" maxlength="20"
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="acc-form-row">
                            <label>Giới tính</label>
                            <div class="acc-form-field acc-radio-group">
                                <?php foreach (['Nam', 'Nữ', 'Khác'] as $g): ?>
                                <label>
                                    <input type="radio" name="gender" value="<?php echo $g; ?>"
                                        <?php echo ($user['gender'] ?? '') === $g ? 'checked' : ''; ?>>
                                    <?php echo $g; ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="acc-form-row">
                            <label for="f-dob">Ngày sinh</label>
                            <div class="acc-form-field">
                                <input type="date" id="f-dob" name="dob"
                                       value="<?php echo htmlspecialchars($user['dob'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="acc-form-row">
                            <label for="f-address">Địa chỉ</label>
                            <div class="acc-form-field">
                                <input type="text" id="f-address" name="address" maxlength="255"
                                       value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="acc-form-actions">
                            <div class="spacer"></div>
                            <button type="submit" class="acc-btn-save" id="profile-save-btn">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>

                <div class="acc-avatar-col">
                    <div class="acc-avatar-preview" id="avatar-preview">
                        <?php if ($avatarSrc): ?>
                            <img src="<?php echo htmlspecialchars($avatarSrc); ?>" id="avatar-img" alt="avatar">
                        <?php else: ?>
                            <span id="avatar-fallback"><?php echo htmlspecialchars($initial); ?></span>
                        <?php endif; ?>
                    </div>
                    <input type="file" id="avatar-input" accept="image/png,image/jpeg,image/webp" style="display:none;">
                    <button type="button" class="acc-avatar-btn" onclick="document.getElementById('avatar-input').click()">Chọn ảnh</button>
                    <p class="acc-avatar-hint">Dung lượng tối đa 3MB<br>Định dạng: JPEG, PNG, WEBP</p>
                </div>
            </div>
        </div>

        
        <div class="acc-card" id="tab-password" style="<?php echo $activeTab === 'password' ? '' : 'display:none;'; ?>">
            <div class="acc-card-head">
                <h2>Đổi Mật Khẩu</h2>
                <p>Để bảo mật tài khoản, không chia sẻ mật khẩu cho người khác</p>
            </div>

            <form id="password-form" style="max-width:520px;">
                <div class="acc-form-row">
                    <label for="p-current">Mật khẩu hiện tại</label>
                    <div class="acc-form-field">
                        <input type="password" id="p-current" name="current_password" required autocomplete="current-password">
                    </div>
                </div>
                <div class="acc-form-row">
                    <label for="p-new">Mật khẩu mới</label>
                    <div class="acc-form-field">
                        <input type="password" id="p-new" name="new_password" required minlength="6" autocomplete="new-password">
                        <p class="acc-hint">Tối thiểu 6 ký tự</p>
                    </div>
                </div>
                <div class="acc-form-row">
                    <label for="p-confirm">Xác nhận mật khẩu</label>
                    <div class="acc-form-field">
                        <input type="password" id="p-confirm" name="confirm_password" required minlength="6" autocomplete="new-password">
                    </div>
                </div>
                <div class="acc-form-actions">
                    <div class="spacer"></div>
                    <button type="submit" class="acc-btn-save" id="password-save-btn">Xác nhận</button>
                </div>
            </form>
        </div>

    </div>
</div>

<div class="acc-toast" id="acc-toast"></div>

<script>
function showToast(message, type){
    const el = document.getElementById('acc-toast');
    el.textContent = message;
    el.className = 'acc-toast show ' + (type || 'success');
    clearTimeout(window.__toastTimer);
    window.__toastTimer = setTimeout(() => el.classList.remove('show'), 2600);
}


function switchTab(tab){
    document.getElementById('tab-profile').style.display  = tab === 'profile'  ? '' : 'none';
    document.getElementById('tab-password').style.display = tab === 'password' ? '' : 'none';
    const url = new URL(window.location);
    url.searchParams.set('tab', tab);
    window.history.replaceState({}, '', url);
}
document.querySelectorAll('.acc-nav-sub a').forEach(a => {
    a.addEventListener('click', function(e){
        e.preventDefault();
        document.querySelectorAll('.acc-nav-sub a').forEach(x => x.classList.remove('active'));
        this.classList.add('active');
        switchTab(this.getAttribute('href').includes('password') ? 'password' : 'profile');
    });
});


document.getElementById('profile-form').addEventListener('submit', function(e){
    e.preventDefault();
    const btn = document.getElementById('profile-save-btn');
    btn.disabled = true; btn.textContent = 'Đang lưu...';

    fetch('../api/user/update-profile.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.message, data.success ? 'success' : 'error');
    })
    .catch(() => showToast('Không thể kết nối máy chủ', 'error'))
    .finally(() => { btn.disabled = false; btn.textContent = 'Lưu thay đổi'; });
});


document.getElementById('password-form').addEventListener('submit', function(e){
    e.preventDefault();
    if (document.getElementById('p-new').value !== document.getElementById('p-confirm').value) {
        showToast('Xác nhận mật khẩu không khớp', 'error');
        return;
    }
    const btn = document.getElementById('password-save-btn');
    btn.disabled = true; btn.textContent = 'Đang xử lý...';

    const form = this;
    fetch('../api/user/change-password.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) form.reset();
    })
    .catch(() => showToast('Không thể kết nối máy chủ', 'error'))
    .finally(() => { btn.disabled = false; btn.textContent = 'Xác nhận'; });
});

// ---- Upload avatar ----
document.getElementById('avatar-input').addEventListener('change', function(){
    const file = this.files[0];
    if (!file) return;

    if (!['image/jpeg','image/png','image/webp'].includes(file.type)) {
        showToast('Chỉ chấp nhận ảnh JPG, PNG hoặc WEBP', 'error');
        this.value = '';
        return;
    }
    if (file.size > 3 * 1024 * 1024) {
        showToast('Ảnh không được vượt quá 3MB', 'error');
        this.value = '';
        return;
    }

    const formData = new FormData();
    formData.append('avatar', file);

    fetch('../api/user/upload-avatar.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const box = document.getElementById('avatar-preview');
                box.innerHTML = '<img src="' + data.avatar_url + '?t=' + Date.now() + '" alt="avatar">';
                document.querySelector('.acc-sidebar-avatar').innerHTML =
                    '<img src="' + data.avatar_url + '?t=' + Date.now() + '" alt="avatar">';
                showToast(data.message, 'success');
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(() => showToast('Không thể kết nối máy chủ', 'error'));
});
</script>

</body>
</html>