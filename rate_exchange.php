<?php
require_once 'includes/config.php';
session_start();
if(!isset($_SESSION["user_id"])){
    header("Location:login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $exchange_id = intval($_POST['exchange_id']);
    $rating = intval($_POST['rating']);
    $review = trim($_POST['review']);
    
    // Update rating only if exchange is completed and not already rated
    $stmt = $conn->prepare("
        UPDATE exchange_requests
        SET Rating = ?, Review = ?
        WHERE Id = ? AND Status='Completed' AND Rating IS NULL
    ");
    $stmt->bind_param("isi", $rating, $review, $exchange_id);
    $stmt->execute();

    header("Location: profile.php"); // redirect back to profile
    exit();
}
?>
