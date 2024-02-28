<?php
session_start();
require 'dbconnect.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$commentId = $_GET['id'];

// Retrieve comment details
$sql = "SELECT * FROM comments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $commentId);
$stmt->execute();
$comment = $stmt->get_result()->fetch_assoc();

if (!$comment) {
    // Comment not found
    header("Location: index.php");
    exit;
}

// Check if the user is authorized to delete this comment
if ($_SESSION['user_id'] != $comment['user_id']) {
    // User is not authorized
    echo "You are not authorized to delete this comment.";
    exit;
}

// Delete the comment from the database
$sql = "DELETE FROM comments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $commentId);
$stmt->execute();

// Redirect back to portfolio page or wherever you want
header("Location: portfolio.php?id=" . $comment['portfolio_id']);
exit;
?>
