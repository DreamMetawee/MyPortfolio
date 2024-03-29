<?php
session_start(); // เริ่มต้นการใช้งาน session

// ล้างข้อมูล session ทั้งหมด
$_SESSION = array();

// ทำลาย session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// ส่งผู้ใช้กลับไปยังหน้า login
header("Location: login.php");
exit();
?>
