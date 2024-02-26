<?php

require 'dbconnect.php'; // เชื่อมต่อกับฐานข้อมูล

session_start(); // ตรวจสอบว่ามีการเริ่ม session

if (isset($_POST['title'], $_POST['description']) && isset($_FILES['file'])) {
    if (!isset($_SESSION['user_id'])) {
        // ถ้าไม่มี user_id ใน session ให้กลับไปหน้า login หรือแสดงข้อความแจ้งเตือน
        $_SESSION['error'] = 'Please login to upload portfolio.';
        header('Location: login.php'); // กำหนดเป็นหน้า login ของคุณ
        exit();
    }

    $title = $_POST['title'];
    $description = $_POST['description'];
    $userId = $_SESSION['user_id'];

    // ตรวจสอบและสร้างไดเร็กทอรี `uploads/` ถ้ายังไม่มี
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $file = $_FILES['file'];
    $fileName = $file['name'];
    $filePath = $uploadDir . basename($fileName);
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // สร้างคำสั่ง SQL สำหรับเพิ่มข้อมูล portfolio
        $sql = "INSERT INTO portfolios (user_id, title, description, file_path) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $userId, $title, $description, $filePath);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Portfolio uploaded successfully";
        } else {
            $_SESSION['error'] = "Failed to upload portfolio";
        }
    } else {
        $_SESSION['error'] = "Failed to move uploaded file.";
    }

    header('Location: index.php'); // กลับไปยังหน้า index
    exit();
}
?>
