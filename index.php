<?php
session_start();
require 'dbconnect.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// ตรวจสอบว่ามีการส่งคำค้นหามาหรือไม่
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM portfolios WHERE (title LIKE ? OR description LIKE ?) AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = '%' . $search . '%';
    $stmt->bind_param("ssi", $searchTerm, $searchTerm, $userId);
} else {
    $sql = "SELECT * FROM portfolios WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
}
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Portfolio</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="fw-bold">My Portfolio</h1>
                <div>
                <p class="mb-0">Welcome, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?>!</p>

                    <a href="logout.php">Logout</a> <!-- ตรวจสอบว่าไฟล์นี้ถูกต้อง -->
                </div>
            </div>
        </div>
    </header>

     <div class="container my-5">
     <form action="index.php" method="get">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Search portfolios..." name="search">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </div>
        </form>
        <h2 class="mb-4">Create and upload your portfolio here</h2>
        <form action="upload_portfolio.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="portfolioTitle" class="form-label">Portfolio Title</label>
                <input type="text" class="form-control" id="portfolioTitle" name="title" required>
            </div>
            <div class="mb-3">
                <label for="portfolioDescription" class="form-label">Description</label>
                <textarea class="form-control" id="portfolioDescription" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="portfolioFile" class="form-label">Upload File</label>
                <input type="file" class="form-control" id="portfolioFile" name="file">
            </div>
            <button type="submit" class="btn btn-primary">Upload Portfolio</button>
        </form>
    </div>

    <!-- Display portfolios -->
    <div class="container">
        <h2>Your Portfolios</h2>
        <div class="row">
        <?php
        // Query ข้อมูลจากตาราง portfolios ของผู้ใช้ที่เข้าสู่ระบบ
        $sql = "SELECT * FROM portfolios WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()):
        ?>
    <div class="col-md-4">
        <div class="card">
            <?php if (pathinfo($row['file_path'], PATHINFO_EXTENSION) === 'pdf'): ?>
                <embed src="<?php echo $row['file_path']; ?>" class="card-img-top portfolio-pdf" type="application/pdf" />
            <?php else: ?>
                <img src="<?php echo $row['file_path']; ?>" class="card-img-top portfolio-img" alt="Portfolio Image">
            <?php endif; ?>
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                
                <a href="edit_portfolio.php?id=<?= $row['id']; ?>" class="btn btn-primary">Edit</a>

                <a href="delete_portfolio.php?id=<?= $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>


                
            </div>
        </div>
    </div>
<?php endwhile; ?>
        </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
