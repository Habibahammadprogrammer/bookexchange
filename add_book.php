<?php
session_start();
$errors=[];
$success = "";
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $isbn=trim($_POST['isbn']);
    $condition = trim($_POST['condition']);
    $availability = trim($_POST['availability']);
    $genre_id = intval($_POST['genre']);
    $cover_url = trim($_POST['cover_url']);
    $owner_id = $_SESSION['user_id'];
    $description=trim($_POST['description']);
    if (!empty($title) && !empty($author) && !empty($condition) && !empty($availability) && $genre_id > 0) {
        $stmt = $conn->prepare("INSERT INTO books (Title, Author, BookCondition, Availability, GenreId, CoverURL,OwnerId,Description,ISBN) VALUES (?, ?, ?, ?, ?, ?,?,?,?)");
        $stmt->bind_param("ssssisiss", $title, $author, $condition, $availability, $genre_id, $cover_url, $owner_id, $description, $isbn);

        if ($stmt->execute()) {
            header("Location:inventory.php?success=1");        } else {
            $errors[]= " Error: " . $stmt->error ;
        }
        $stmt->close();
    } else {
        $errors[]= "Please fill in all required fields.";
    }
}

$genres_result = $conn->query("SELECT Id, Name FROM genres ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
    <link rel="stylesheet" href="Assets/Bookentry.css">
</head>
<body>
     <?php
    if(!empty($errors)){
        echo '<ul style="color:red;">';
        foreach($errors as $error){
            echo "<li>$error</li>";
        }
        echo '</ul>';
    }

    if($success){
        echo '<p style="color:green;">'.$success.'</p>';
    }
    ?>
    <section class="contact-section">
    <h2>Add Book</h2>
    <form class="contact-form" method="POST" action="">
        <div class="form-row">
            <div>
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div>
                <label for="author">Author</label>
                <input type="text" id="author" name="author" required>
            </div>
        </div>
        <div>
            <label for="isbn">ISBN</label>
                <input type="text" id="isbn" name="isbn" required>
        </div>

        <div class="form-row">
            <div>
                <label for="genre">Genre</label>
                <select id="genre" name="genre" required>
                    <option value="">Select a genre</option>
                    <?php while ($genre = $genres_result->fetch_assoc()): ?>
    <option value="<?= $genre['Id'] ?>"><?= htmlspecialchars($genre['Name']) ?></option>
<?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="cover_url">Cover URL</label>
                <input type="url" id="cover_url" name="cover_url" required>
            </div>
        </div>

        <div class="form-row">
            <div>
                <label for="availability">Availability</label>
                <select id="availability" name="availability" required>
                    <option>---Select Availability</option>
                    <option value="Avaliable">Available</option>
                    <option value="Pedning">Pending</option>
                    <option value="Sold">Sold</option>
                </select>
            </div>
        </div>
        <div class="form-row">
      <div>
        <label for="condition">Condition</label>
        <select id="condition" name="condition" required>
        <option value="">-- Select Condition --</option>
        <option value="new">New</option>
        <option value="like-new">Like New</option>
        <option value="good">Good</option>
        <option value="fair">Fair</option>
        <option value="poor">Poor</option>
        </select>
      </div>
      </div>
        <label for="description">Description</label>
    <textarea id="description" name="description" rows="5"></textarea>
        <button  action="inventory.php" type="submit">Add Book</button>
    </form>
</section>
</body>
</html>
