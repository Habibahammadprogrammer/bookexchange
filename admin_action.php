<?php
session_start();
require_once 'includes/config.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT Role FROM users WHERE Id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['Role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
if (!$user || strtolower($user['Role']) !== 'admin') {
    die("Access denied. Admins only.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';


    if (isset($_POST['user_id'])) {
        $targetUserId = intval($_POST['user_id']);

        if ($action === 'delete') {
            $stmt = $conn->prepare("DELETE FROM users WHERE Id = ?");
            $stmt->bind_param("i", $targetUserId);
            $stmt->execute();
        }

        if ($action === 'promote') {
            $stmt = $conn->prepare("UPDATE users SET Role = 'admin' WHERE Id = ?");
            $stmt->bind_param("i", $targetUserId);
            $stmt->execute();
        }

        if ($action === 'demote') {
            $stmt = $conn->prepare("UPDATE users SET Role = 'user' WHERE Id = ?");
            $stmt->bind_param("i", $targetUserId);
            $stmt->execute();
        }
    }


   if (isset($_POST['book_id']) && $action === 'delete_book') {
    $bookId = intval($_POST['book_id']);

    $stmt = $conn->prepare("DELETE FROM exchangerequests WHERE BookId = ?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();

    // Then delete the book
    $stmt = $conn->prepare("DELETE FROM books WHERE Id = ?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
}


    if (isset($_POST['request_id'])) {
        $requestId = intval($_POST['request_id']);

        if ($action === 'approve_request') {
            $stmt = $conn->prepare("UPDATE exchangerequests SET Status = 'Approved' WHERE Id = ?");
            $stmt->bind_param("i", $requestId);
            $stmt->execute();
        }

        if ($action === 'reject_request') {
            $stmt = $conn->prepare("UPDATE exchangerequests SET Status = 'Rejected' WHERE Id = ?");
            $stmt->bind_param("i", $requestId);
            $stmt->execute();
        }

        if ($action === 'delete_request') {
            $stmt = $conn->prepare("DELETE FROM exchangerequests WHERE Id = ?");
            $stmt->bind_param("i", $requestId);
            $stmt->execute();
        }
    }


    header("Location: admin.php");
    exit();
}
?>
