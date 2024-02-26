<?php
session_start();
require 'dbconnect.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // การเข้าสู่ระบบสำเร็จ
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username']; // เพิ่มบรรทัดนี้
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['error'] = 'Invalid login credentials';
            header('Location: login.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'Invalid login credentials';
        header('Location: login.php');
        exit();
    }
} else {
    // ข้อมูลฟอร์มไม่ครบถ้วน
    $_SESSION['error'] = 'Please fill both username and password fields';
    header('Location: login.php');
    exit();
}
?>
