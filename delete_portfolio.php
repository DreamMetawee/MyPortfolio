<?php
ob_start(); // Start output buffering
session_start();
require 'dbconnect.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Check if the portfolio belongs to the current user
    $query = "SELECT user_id FROM portfolios WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $portfolio = $result->fetch_assoc();

    if ($portfolio['user_id'] != $_SESSION['user_id']) {
        echo "You do not have permission to delete this item";
        exit();
    } else {
        // Delete the portfolio
        $query = "DELETE FROM portfolios WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Portfolio deleted successfully";
        } else {
            $_SESSION['error'] = "An error occurred while deleting the portfolio";
        }
    }

    $stmt->close();
    $conn->close();
    header('Location: index.php');
    exit();
}
?>
