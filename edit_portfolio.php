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
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-img-top {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            height: 300px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .card:hover .card-img-top {
            transform: scale(1.1);
        }

        .form-label {
            font-weight: bold;
        }

        .btn-delete {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
            transition: background-color 0.3s ease;
        }

        .btn-delete:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 20px;
        }

        .card-body {
            padding: 20px;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #ced4da;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            color: #495057;
            background-color: #fff;
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }

        .btn {
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">Edit Portfolio</h2>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <img src="<?php echo $portfolio['file_path']; ?>" class="card-img-top" alt="Portfolio Image">
                <div class="card-body">
                    <form action="edit_portfolio.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $portfolio['id']; ?>">
                        <div class="mb-3">
                            <label for="portfolioTitle" class="form-label">Portfolio Title</label>
                            <input type="text" class="form-control" id="portfolioTitle" name="title" required value="<?php echo $portfolio['title']; ?>">
                        </div>
                        <br>
                        <div class="mb-3">
                            <label for="portfolioDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="portfolioDescription" name="description" rows="5" required><?php echo $portfolio['description']; ?></textarea>
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="delete_portfolio.php?id=<?= $portfolio['id']; ?>" onclick="return confirm('Are you sure?')" class="btn btn-delete">Delete</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="text-muted py-5">
    <div class="container">
        <p class="mb-1">By Metawee Khotjailak</p>
    </div>
</footer>
</body>
</html>
