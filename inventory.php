<?php 
session_start(); 
require_once 'includes/config.php'; 

// Get genres for dropdown
$genresResult = $conn->query("SELECT Id, Name AS GenreName FROM genres ORDER BY Name ASC");
if (!$genresResult) {
    die("Error fetching genres: " . $conn->error);
}

$sql = "SELECT books.Id, books.Title, books.Author, books.BookCondition, books.Availability, books.CreatedAt,
        genres.Name AS GenreName,users.Name as OwnerName
        FROM books
        LEFT JOIN genres ON books.GenreID = genres.Id
        LEFT JOIN users ON books.OwnerId=users.Id";

$filters = [];
$orderBy = " ORDER BY books.CreatedAt DESC"; // default newest first

if (!empty($_GET['search'])) { 
    $search = mysqli_real_escape_string($conn, $_GET['search']); 
    $filters[] = "(books.Title LIKE '%$search%' OR books.Author LIKE '%$search%')"; 
} 

if (!empty($_GET['availability'])) { 
    $availability = mysqli_real_escape_string($conn, $_GET['availability']); 
    $filters[] = "books.Availability = '$availability'"; 
} 

if (!empty($_GET['genre'])) { 
    $genre = mysqli_real_escape_string($conn, $_GET["genre"]); 
    $filters[] = "books.GenreID = '$genre'"; 
} 

if (!empty($_GET["sort"])) { 
    if ($_GET["sort"] === "oldest") { 
        $orderBy = " ORDER BY books.CreatedAt ASC"; 
    } elseif ($_GET["sort"] === "newest") { 
        $orderBy = " ORDER BY books.CreatedAt DESC"; 
    } 
} 

// If there are filters, add WHERE clause
if (count($filters) > 0) { 
    $sql .= " WHERE " . implode(" AND ", $filters); 
} 

$sql .= $orderBy; 

$result = $conn->query($sql); 
if (!$result) { 
    die("Error fetching books: " . $conn->error); 
}
?>
<!DOCTYPE html>  
<html>  
<head>  
    <title>Inventory</title>  
    <style>
        body {
    font-family: Arial;
    background-color: #fdfaf6;
    margin: 0;
    padding: 40px;
    color: #4a4a4a;
}

h1 {
    font-family:Arial;;
    text-align: center;
    color: #2c2c2c;
    font-size: 2.5rem;
    margin-bottom: 30px;
}

form {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 12px;
    margin-bottom: 30px;
}

form input,
form select {
    padding: 10px 14px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background-color: #fff;
    transition: border 0.2s;
}

form input:focus,
form select:focus {
    border-color: #d6b98c;
    outline: none;
}

form button {
    background-color: #d6b98c;
    color: #fff;
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.3s;
}

form button:hover {
    background-color: #c0a672;
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

thead {
    background-color: #f7f2ea;
}

thead th {
    font-family: 'Playfair Display', serif;
    font-weight: 600;
    padding: 14px;
    text-align: left;
    font-size: 15px;
    color: #3a3a3a;
}

tbody td {
    padding: 14px;
    font-size: 14px;
    border-bottom: 1px solid #f0e9df;
}

tbody tr:last-child td {
    border-bottom: none;
}

tbody tr:hover {
    background-color: #fdf6ee;
    transition: background-color 0.2s;
}

.btn {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
    transition: background-color 0.2s;
    color:#fff;
}

.btn:first-child {
    background-color: #a3c9a8;
}

.btn:first-child:hover {
    background-color: #8ab790;
}
.btn.delete {
    background-color: gray;
    text-decoration: none;
}

.btn.delete:hover {
    background-color: #000;
}

.btn:last-child {
    background-color: #e69a8d;;
}

.btn:last-child:hover {
    background-color: #d17e71;
}
.bottom-btn{
    position:fixed;
    bottom:20px;
    left:50%;
    transform:translateX(-50%);
    z-index:1000;
}
.btn-back {
    display: inline-block;
    padding: 10px 20px;
    background-color: #E8A87C; /* soft orange */
    color: white;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-back:hover {
    background-color: #d17a5f;
    transform: scale(1.05);
}

@media (max-width: 768px) {
    table, thead, tbody, th, td, tr {
        display: block;
    }

    thead {
        display: none;
    }

    tbody tr {
        background-color: #fff;
        margin-bottom: 12px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        padding: 10px;
    }

    tbody td {
        border-bottom: none;
        padding: 8px 0;
    }

    tbody td:before {
        content: attr(data-label);
        font-weight: 600;
        color: #6a6a6a;
        display: block;
        margin-bottom: 4px;
    }
}
    </style>
</head>  
<body>

<h1>Book Inventory</h1>

<form method="GET" action="inventory.php">
    <input type="text" name="search" placeholder="Search by title or author" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">

    <select name="genre">
        <option value="">All Genres</option>
        <?php while ($g = $genresResult->fetch_assoc()): ?>
            <option value="<?php echo $g['Id']; ?>" <?php echo (isset($_GET['genre']) && $_GET['genre'] == $g['Id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($g['GenreName']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <select name="availability">
        <option value="">All Availability</option>
        <option value="Available" <?php echo (isset($_GET['availability']) && $_GET['availability'] == 'Available') ? 'selected' : ''; ?>>Available</option>
        <option value="Unavailable" <?php echo (isset($_GET['availability']) && $_GET['availability'] == 'Unavailable') ? 'selected' : ''; ?>>Unavailable</option>
    </select>

    <select name="sort">
        <option value="newest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'newest') ? 'selected' : ''; ?>>Newest First</option>
        <option value="oldest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'oldest') ? 'selected' : ''; ?>>Oldest First</option>
    </select>

    <button type="submit">Search</button>
</form>

<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Condition</th>
            <th>Availability</th>
            <th>Genre</th>
            <th>Owner</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Title']); ?></td>
                    <td><?php echo htmlspecialchars($row['Author']); ?></td>
                    <td><?php echo htmlspecialchars($row['BookCondition']); ?></td>
                    <td><?php echo htmlspecialchars($row['Availability']); ?></td>
                    <td><?php echo htmlspecialchars($row['GenreName']); ?></td>
                     <td><?= htmlspecialchars($row['OwnerName']) ?></td> 
                    <td>
                        <a href="edit_book.php?id=<?php echo $row['Id']; ?>" class="btn">Edit</a>
                        <a href="delete_book.php?id=<?php echo $row['Id']; ?>" onclick="return confirm('Are you sure you want to delete this book?');" class="btn delete">Delete</a>
                        <a href="request_exchanges.php=?id<?php echo $row['Id'];?>" class="btn">Request Exchange</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No books found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<div class="bottom-btn">
    <a href="home.php" class="btn-back">Back to Home</a>
</div>
</body>
</html>

