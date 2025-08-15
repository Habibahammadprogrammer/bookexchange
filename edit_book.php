<?php
session_start();
require_once 'includes/config.php';
$errors = [];
if(!isset($_SESSION['user_id'])){
    header("Location:login.php");
    exit;
}
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid Book ID.");
}
$book_id = $_GET["id"];
$sql = "SELECT * FROM books Where Id=? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("Book not found.");
}
$book= $result->fetch_assoc();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $condition = $_POST['condition'];
    $availability = $_POST['availability'];
    $update_sql = "UPDATE books SET Title=?, Author=?,BookCondition=?,Availability=? WHERE Id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $title, $author, $condition, $availability, $book_id);
    if ($update_stmt->execute()) {
        header("Location:inventory.php");
        exit();
    } else {
        $errors[] = "Error Updating Book:" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Book</title>
    <link rel="stylesheet" href="Assets/inventory.css">
</head>
<body>
    <h1>Edit Book</h1>

    <form method="POST" action="edit_book.php?id=<?php echo $book_id; ?>">
        <label>Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($book['Title']); ?>" required>

        <label>Author:</label>
        <input type="text" name="author" value="<?php echo htmlspecialchars($book['Author']); ?>" required>

        <label>Condition:</label>
        <select name="condition">
            <option value="new" <?php if ($book['BookCondition'] === 'new') echo 'selected'; ?>>New</option>
            <option value="like-new" <?php if ($book['BookCondition'] === 'like-new') echo 'selected'; ?>>Like-new</option>
            <option value="good" <?php if ($book['BookCondition'] === 'good') echo 'selected'; ?>>Good</option>
            <option value="fair" <?php if ($book['BookCondition'] === 'fair') echo 'selected'; ?>>Fair</option>
            <option value="poor" <?php if ($book['BookCondition'] === 'poor') echo 'selected'; ?>>Poor</option>
        </select>

        <label>Availability:</label>
        <select name="availability">
            <option value="Available" <?php if ($book['Availability'] === 'Available') echo 'selected'; ?>>Available</option>
            <option value="Pending" <?php if ($book['Availability'] === 'Pending') echo 'selected'; ?>>Pending</option>
            <option value="Sold" <?php if ($book['Availability'] === 'Sold') echo 'selected'; ?>>Sold</option>
        </select>

        <button type="submit">Update Book</button>
    </form>
</body>
</html>