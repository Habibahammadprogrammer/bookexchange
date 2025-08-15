<?php
session_start();
require_once('includes/config.php');
if(!isset($_SESSION['user_id'])){
    header("Location:login.php");
    exit();
}
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $book_id = $_GET['id'];
    $owner_id = $_SESSION['user_id'];
    $checkStmt = $conn->prepare("SELECT Id FROM books WHERE Id=? AND OwnerID=?");
    $checkStmt->bind_param("ii", $book_id, $owner_id);
    $checkStmt->execute();
    $checkStmt->store_result();
    if ($checkStmt->num_rows > 0) {
        $deleteStmt = $conn->prepare("DELETE FROM books WHERE Id=? AND OwnerId=?");
        $deleteStmt->bind_param("ii", $book_id, $owner_id);
        $deleteStmt->execute();
        $deleteStmt->close();
    }
    $checkStmt->close();
}
header("Location:inventory.php");
exit();
?>