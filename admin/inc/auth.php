<?php
/**
 * admin/inc/auth.php
 * Bootstrap dùng chung cho MỌI trang trong khu vực admin:
 *  - Mở session + kết nối DB
 *  - Chặn truy cập nếu chưa đăng nhập hoặc không phải admin
 *  - Cung cấp các hàm tiện ích dùng chung (escape HTML, format tiền, badge trạng thái)
 *
 * Trước đây chỉ dashboard.php có đoạn check quyền này, các file admin khác
 * (users.php, products.php, orders.php, reports.php, transactions.php,
 * edit-product.php) không hề kiểm tra -> ai gõ đúng URL cũng vào được.
 * Từ giờ mọi trang admin chỉ cần require_once "inc/auth.php" là đủ.
 */

session_start();
require_once __DIR__ . "/../../config/db.php";

if (
    !isset($_SESSION["user_id"]) ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    header("Location: ../pages/login.html");
    exit;
}

/** Escape HTML an toàn, không lỗi khi giá trị NULL (PHP 8.1+) */
if (!function_exists("e")) {
    function e($value): string
    {
        return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
    }
}

/**
 * In ra banner thông báo (thành công/lỗi) khi trang được load sau một
 * redirect từ api/admin/*.php (?msg=...&type=success|error).
 */
if (!function_exists("flashMessage")) {
    function flashMessage(): void
    {
        if (empty($_GET["msg"])) {
            return;
        }
        $type = ($_GET["type"] ?? "") === "success" ? "success" : "error";
        echo '<div class="alert alert-' . $type . '">' . e($_GET["msg"]) . '</div>';
    }
}

/** Format tiền VNĐ: 12500000 -> 12.500.000đ */
if (!function_exists("money")) {
    function money($amount): string
    {
        return number_format((float)$amount, 0, ",", ".") . "đ";
    }
}

/**
 * Trả về [nhãn hiển thị, class badge] cho một trạng thái, áp dụng chung
 * cho product / order / user status. Nếu gặp giá trị lạ hoặc rỗng
 * (ví dụ dữ liệu cũ bị lưu sai) thì vẫn hiển thị được thay vì vỡ giao diện.
 */
if (!function_exists("statusBadge")) {
    function statusBadge(?string $status): array
    {
        $map = [
            // Sản phẩm
            "active"    => ["Đang bán",     "badge-success"],
            "pending"   => ["Chờ duyệt",    "badge-warning"],
            "sold"      => ["Đã bán",       "badge-info"],
            "hidden"    => ["Đã ẩn",        "badge-neutral"],
            "rejected"  => ["Từ chối",      "badge-danger"],
            "deleted"   => ["Đã xóa",       "badge-danger"],
            // Đơn hàng
            "accepted"  => ["Đã nhận",      "badge-info"],
            "completed" => ["Hoàn thành",   "badge-success"],
            "cancelled" => ["Đã hủy",       "badge-danger"],
            // Người dùng
            "blocked"   => ["Đã khóa",      "badge-danger"],
        ];

        $status = trim((string)$status);
        if ($status === "" || !isset($map[$status])) {
            return ["Không xác định", "badge-neutral"];
        }
        return $map[$status];
    }
}