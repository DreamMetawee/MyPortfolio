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
    <style>
        
    </style>
</head>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Album example</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a href="#" class="navbar-brand d-flex align-items-center">
                <strong>My Portfolio</strong>
            </a>
            <div class="welcome">
                <span class="text-white me-2">Welcome, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?></span>
                <a href="logout.php" class="btn btn-primary">Logout</a>
            </div>
        </div>
    </div>
</header>

<main>
    <section class="py-5 text-center container">
        <div class="row py-lg-5">
            <div class="col-lg-6 col-md-8 mx-auto">
                <h1 class="fw-light">Create and upload your portfolio here</h1>
                <form action="upload_portfolio.php" method="post" enctype="multipart/form-data" class="upload-form">
                    <div class="form-group">
                        <label for="portfolioTitle" class="form-label">Portfolio Title</label>
                        <input type="text" class="form-control" id="portfolioTitle" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="portfolioDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="portfolioDescription" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="portfolioFile" class="form-label">Upload File</label>
                        <input type="file" class="form-control" id="portfolioFile" name="file">
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Portfolio</button>
                </form>
            </div>
        </div>
    </section>

    <div class="album py-5 bg-light">
    <div class="container">
        <h2 class="fw-bold text-center mb-4">Your Portfolios</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
            <!-- Portfolio cards will be dynamically added here -->
            <?php
            while ($row = $result->fetch_assoc()):
            ?>
            <div class="col">
                <div class="card shadow-sm">
                    <?php if (pathinfo($row['file_path'], PATHINFO_EXTENSION) === 'pdf'): ?>
                    <embed src="<?php echo $row['file_path']; ?>" class="card-img-top portfolio-pdf" type="application/pdf" />
                    <?php else: ?>
                    <img src="<?php echo $row['file_path']; ?>" class="card-img-top portfolio-img" alt="Portfolio Image">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <a href="edit_portfolio.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                
                                <a href="portfolio.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

</main>

<footer class="text-muted py-5">
    <div class="container">
        <p class="mb-1">By Metawee Khotjailak</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
