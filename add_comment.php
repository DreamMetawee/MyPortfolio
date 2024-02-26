<?php
session_start();
require 'dbconnect.php';

// ตรวจสอบว่ามีการส่งข้อมูลมาจากฟอร์มหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $portfolio_id = $_POST['portfolio_id']; // หรือรับค่าโดยใช้ filter_input() เพื่อความปลอดภัย
    $comment = htmlspecialchars($_POST['comment']); // ใช้ htmlspecialchars เพื่อป้องกัน XSS

    // ตรวจสอบว่าค่าที่จำเป็นไม่ว่างเปล่า
    if (!empty($portfolio_id) && !empty($comment)) {
        // เตรียมคำสั่ง SQL และผูกค่า
        $sql = "INSERT INTO comments (portfolio_id, user_id, comment) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $portfolio_id, $_SESSION['user_id'], $comment);

        // ประมวลผลคำสั่ง SQL
        if ($stmt->execute()) {
            // ส่งกลับไปยังหน้า portfolio พร้อมข้อความสำเร็จ
            $_SESSION['success'] = "Your comment has been added successfully.";
            header("Location: portfolio.php?id=" . $portfolio_id);
        } else {
            // แสดงข้อผิดพลาดหากไม่สามารถเพิ่มความคิดเห็น
            $_SESSION['error'] = "There was an error adding your comment. Please try again.";
            header("Location: portfolio.php?id=" . $portfolio_id);
        }
    } else {
        // หากค่าที่จำเป็นว่างเปล่า กลับไปยังหน้า portfolio พร้อมข้อความแสดงข้อผิดพลาด
        $_SESSION['error'] = "Please fill in all required fields.";
        header("Location: portfolio.php?id=" . $portfolio_id);
    }

    $stmt->close();
    $conn->close();
} else {
    // หากไม่มีการส่งข้อมูลมาจากฟอร์ม กลับไปยังหน้าหลัก
    header("Location: index.php");
}
?>
