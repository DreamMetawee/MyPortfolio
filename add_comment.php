<?php
session_start();
require 'dbconnect.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['comment'])) {
    // Redirect หากไม่มีการเข้าสู่ระบบหรือไม่มีความคิดเห็น
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user_id'];
$portfolioId = $_POST['portfolio_id'];
$comment = $_POST['comment'];

// ป้องกัน SQL Injection
$sql = "INSERT INTO comments (portfolio_id, user_id, comment) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $portfolioId, $userId, $comment);

if ($stmt->execute()) {
    // Redirect กลับไปยัง portfolio พร้อมข้อความสำเร็จ
    $_SESSION['message'] = "Comment added successfully.";
    header("Location: portfolio.php?id=" . $portfolioId);
} else {
    // Redirect กลับไปยัง portfolio พร้อมข้อความแสดงข้อผิดพลาด
    $_SESSION['error'] = "Failed to add comment.";
    header("Location: portfolio.php?id=" . $portfolioId);
}

$stmt->close();
$conn->close();
?>
