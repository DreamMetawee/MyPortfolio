<?php
session_start();
require 'dbconnect.php';

if (isset($_GET['id'])) {
    $portfolioId = $_GET['id'];

    // คำสั่ง SQL สำหรับดึงข้อมูล portfolio
    $sql = "SELECT * FROM portfolios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $portfolioId);
    $stmt->execute();
    $portfolio = $stmt->get_result()->fetch_assoc();

    if ($portfolio) {
        echo "<div class='portfolio-item'>";
        echo "<h2 class='portfolio-title'>" . htmlspecialchars($portfolio['title']) . "</h2>";
        echo "<img src='" . htmlspecialchars($portfolio['file_path']) . "' class='portfolio-img' alt='Portfolio Image'>";
        echo "<p class='portfolio-description'>" . htmlspecialchars($portfolio['description']) . "</p>";
        echo "</div>";
    } else {
        echo "Portfolio not found.";
    }

    $stmt->close(); // ปิดคำสั่ง SQL statement
} else {
    echo "No portfolio ID provided.";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        /* เพิ่ม CSS สำหรับส่วนการแสดงความคิดเห็น */
        .comment-section {
    background-color: #f8f9fa;
    border-radius: 25px;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.shadow-textarea textarea.form-control::placeholder {
    font-weight: 500;
}

.shadow-textarea .form-control {
    padding-left: 2.5rem;
}

.form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.btn-primary {
    border-radius: 25px;
}



        .comment {
            background-color: #fff;
            padding: 15px;
            margin-top: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .comment-info {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }

        .comment-content {
            font-size: 1rem;
        }


        .btn-edit,
        .btn-delete {
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .btn-edit {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-delete {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-edit:hover,
        .btn-delete:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>

<body>


    <!-- ปุ่ม Back to Home -->
    <div class="mt-4 mb-4 text-center">
        <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 back-to-home">Back to Home</a>
    </div>

    <!-- ส่วนเพิ่มความคิดเห็น -->
    <div class="comment-section py-4">
    <form action="add_comment.php" method="post" class="comment-form mt-3">
        <input type="hidden" name="portfolio_id" value="<?php echo $portfolioId; ?>">
        <div class="form-group shadow-textarea">
            <textarea class="form-control z-depth-1" id="comment" name="comment" rows="3" placeholder="Type your comment here..." required></textarea>
        </div>
        <br>
        <div class="d-grid gap-2 mt-3">
            <button type="submit" class="btn btn-primary">Post Comment</button>
        </div>
    </form>
</div>


    <!-- แสดงความคิดเห็น -->
    <?php
    $sql = "SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE portfolio_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $portfolioId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($comment = $result->fetch_assoc()): ?>
            <div class="comment">
                <div class="comment-info">
                    <strong>
                        <?php echo htmlspecialchars($comment['username']); ?>
                    </strong> says:
                </div>
                <div class="comment-content">
                    <?php echo htmlspecialchars($comment['comment']); ?>
                </div>
                <!-- ปุ่มแก้ไขและลบความคิดเห็น -->
                <?php if ($comment['user_id'] == $_SESSION['user_id']): ?>
                    <div class="comment-actions">
                        <button class="btn btn-edit" onclick="editComment(<?= $comment['id'] ?>)">
                            <i class="bi bi-pencil-fill"></i> Edit
                        </button>
                        <button class="btn btn-delete" onclick="deleteComment(<?= $comment['id'] ?>)">
                            <i class="bi bi-trash-fill"></i> Delete
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile;
    } ?>
    </div>

    


    <!-- JavaScript สำหรับลบความคิดเห็น -->
    <script>
        function editComment(commentId) {
            window.location.href = 'edit_comment.php?id=' + commentId;
        }

        function deleteComment(commentId) {
            if (confirm('Are you sure you want to delete this comment?')) {
                window.location.href = 'delete_comment.php?id=' + commentId;
            }
        }
    </script>
<footer class="text-muted py-5">
    <div class="container">
        <p class="mb-1">By Metawee Khotjailak</p>
    </div>
</footer>
</body>

</html>