<?php
require 'dbconnect.php';

// ตรวจสอบว่ามี ID ถูกส่งมาหรือไม่
if (isset($_GET['id'])) {
    $portfolioId = $_GET['id'];

    // คำสั่ง SQL เพื่อดึงข้อมูลของ portfolio โดยใช้ ID
    $sql = "SELECT * FROM portfolios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $portfolioId);
    $stmt->execute();
    $result = $stmt->get_result();
    $portfolio = $result->fetch_assoc();

    if ($portfolio) {
        // แสดงข้อมูลของ portfolio
        echo "<h2>" . htmlspecialchars($portfolio['title']) . "</h2>";
        echo "<p>" . htmlspecialchars($portfolio['description']) . "</p>";
        // แสดงรูปภาพหรือเนื้อหาอื่นๆ ตามต้องการ
    } else {
        echo "Portfolio not found.";
    }
} else {
    echo "No portfolio ID provided.";
}
?>
