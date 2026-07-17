<?php


session_start();
require_once __DIR__ . "/../../config/db.php";

if (
    !isset($_SESSION["user_id"]) ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    header("Location: ../pages/login.html");
    exit;
}

if (!function_exists("e")) {
    function e($value): string
    {
        return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
    }
}


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