<?php
session_start();
require 'dbconnect.php';

if (isset($_GET['id'])) {
    $commentId = $_GET['id'];

    // ดึงข้อมูลความคิดเห็น
    $sql = "SELECT * FROM comments WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $commentId);
    $stmt->execute();
    $comment = $stmt->get_result()->fetch_assoc();

    // ตรวจสอบว่าผู้ใช้เป็นเจ้าของความคิดเห็นหรือไม่
    if ($comment && $_SESSION['user_id'] == $comment['user_id']) {
        // แสดงแบบฟอร์มแก้ไขความคิดเห็น
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $newComment = $_POST['comment'];

            $sql = "UPDATE comments SET comment = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $newComment, $commentId);
            $stmt->execute();

            // Redirect back to portfolio page after editing
            header("Location: portfolio.php?id=" . $comment['portfolio_id']);
            exit();
        }
    } else {
        echo "You don't have permission to edit this comment.";
    }
} else {
    echo "Comment ID not provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Comment</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #f7f7f7;
        }

        .container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 40px;
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #0056b3;
            border: none;
        }

        footer {
            margin-top: 40px;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Edit Comment</h2>
    <form action="" method="post">
        <div class="mb-3">
            <textarea class="form-control" id="comment" name="comment" rows="4" required><?= htmlspecialchars($comment['comment']) ?></textarea>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
        </div>
    </form>
</div>

<footer class="text-muted py-5">
    <div class="container">
        <p>By Metawee Khotjailak</p>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
