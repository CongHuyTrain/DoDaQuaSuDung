<?php
/**
 * api/admin/inc/guard.php
 * Bootstrap dùng chung cho MỌI file hành động trong api/admin/
 * (approve, reject, block-user, unblock-user, delete-product, delete-user...).
 *
 * Trước đây mỗi file tự làm một kiểu:
 *  - approve.php, delete-product.php, delete-user.php, reject.php, unblock-user.php
 *    không hề check quyền admin -> ai biết URL cũng gọi được.
 *  - block-user.php có check nhưng dùng $_SESSION["admin_id"], trong khi toàn bộ
 *    hệ thống (admin/inc/auth.php, dashboard.php...) lưu session là
 *    $_SESSION["user_id"] + $_SESSION["role"]. Vì "admin_id" không bao giờ được
 *    set ở đâu cả, check đó luôn luôn false -> file này thực chất bị "khóa cứng",
 *    bấm nút Khóa tài khoản sẽ ra trang trắng không rõ lý do.
 *
 * Từ giờ mọi file trong api/admin/ chỉ cần:
 *   require_once __DIR__ . "/inc/guard.php";
 */

session_start();
require_once __DIR__ . "/../../../config/db.php";

if (
    !isset($_SESSION["user_id"]) ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    // Dùng đường dẫn URL (2 cấp lên root), không phải đường dẫn file
    header("Location: ../../pages/login.html");
    exit;
}

/** ID của admin đang thao tác - dùng để chặn tự khóa/tự xóa chính mình */
define("CURRENT_ADMIN_ID", (int)$_SESSION["user_id"]);

/**
 * Lấy id từ query string, trả về 0 nếu không hợp lệ (thay vì die() cộc lốc,
 * cho phép nơi gọi tự quyết định redirect kèm thông báo lỗi).
 */
function getRequiredId(): int
{
    $id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
    return $id > 0 ? $id : 0;
}

/**
 * Redirect kèm thông báo (flash message) hiển thị lại trên trang admin.
 * $type: "success" | "error"
 */
function redirectWithMessage(string $url, string $type, string $message): void
{
    $sep = strpos($url, "?") === false ? "?" : "&";
    header("Location: " . $url . $sep . "msg=" . urlencode($message) . "&type=" . urlencode($type));
    exit;
}

/**
 * Kiểm tra lỗi FK phổ biến khi xóa (mã 1451: "Cannot delete or update a parent
 * row: a foreign key constraint fails") để trả thông báo dễ hiểu cho admin,
 * thay vì để trang trắng hoặc lỗi SQL khó hiểu.
 */
function isForeignKeyError(mysqli $conn): bool
{
    return $conn->errno === 1451;
}