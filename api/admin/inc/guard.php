<?php


session_start();
require_once __DIR__ . "/../../../config/db.php";

if (
    !isset($_SESSION["user_id"]) ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    
    header("Location: ../../pages/login.html");
    exit;
}


define("CURRENT_ADMIN_ID", (int)$_SESSION["user_id"]);


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


function isForeignKeyError(mysqli $conn): bool
{
    return $conn->errno === 1451;
}