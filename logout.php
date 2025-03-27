<?php
// --- logout.php ---
session_start(); // Bắt đầu session để có thể hủy nó

// Hủy tất cả các biến session
$_SESSION = array(); // Hoặc dùng session_unset();

// Hủy session cookie (nếu có)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hủy session hoàn toàn
session_destroy();

// Chuyển hướng người dùng về trang đăng nhập
header("Location: login.php");
exit();
?>