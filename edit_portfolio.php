<?php
session_start();
require 'dbconnect.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$id = $_GET['id'] ?? null; // รับค่า ID จาก URL

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // อัปเดตข้อมูลในฐานข้อมูล
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    $query = "UPDATE portfolios SET title = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $title, $description, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "อัปเดตข้อมูลสำเร็จ";
    } else {
        echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล";
    }

    $stmt->close();
    $conn->close();

    header('Location: index.php');
    exit();
} else {
    // ดึงข้อมูลเดิมจากฐานข้อมูลมาแสดงบนฟอร์ม
    if ($id !== null) {
        $query = "SELECT * FROM portfolios WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $portfolio = $result->fetch_assoc();

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Portfolio</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2>Edit Portfolio</h2>
        <form action="edit_portfolio.php" method="post">
            <input type="hidden" name="id" value="<?php echo $portfolio['id']; ?>">
            <div class="mb-3">
                <label for="portfolioTitle" class="form-label">Portfolio Title</label>
                <input type="text" class="form-control" id="portfolioTitle" name="title" required value="<?php echo $portfolio['title']; ?>">
            </div>
            <div class="mb-3">
                <label for="portfolioDescription" class="form-label">Description</label>
                <textarea class="form-control" id="portfolioDescription" name="description" rows="3" required><?php echo $portfolio['description']; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</body>
</html>
