<?php 
session_start(); 
require_once 'includes/config.php'; 

$sql = "SELECT Id,Title, Author, BookCondition, Availability 
        FROM books 
        ORDER BY CreatedAT DESC"; 
$result = $conn->query($sql); 

if (!$result) { 
    die("Error fetching books: " . $conn->error); 
} 
?> 

<!DOCTYPE html> 
<html> 
<head> 
    <title>Inventory</title> 
    <link rel="stylesheet" href="Assets/inventory.css"> 
</head> 
<body> 
    <h1>Book Inventory</h1> 

    <table border="1"> 
        <thead> 
            <tr> 
                <th>Book Name</th> 
                <th>Author</th> 
                <th>Condition</th> 
                <th>Status</th> 
                <th>Actions</th>
            </tr> 
        </thead> 
       <tbody>  
    <?php while($row = $result->fetch_assoc()): ?>  
        <?php  
            $statusClass = strtolower($row['Availability']);  
        ?>  
        <tr>  
            <td><?php echo htmlspecialchars($row['Title']); ?></td>  
            <td><?php echo htmlspecialchars($row['Author']); ?></td>  
            <td><?php echo htmlspecialchars($row['BookCondition']); ?></td>  
            <td class="status <?php echo $statusClass; ?>">  
                <?php echo htmlspecialchars($row['Availability']); ?>  
            </td>  
            <td> 
                <a href="edit_book.php?id=<?php echo $row['Id'];?>" class="btn">Edit</a> 
                <a href="delete_book.php?id=<?php echo $row['Id']; ?>"  
                   onclick="return confirm('Are you sure you want to delete this book?');"  
                   class="btn">Delete</a>  
            </td> 
        </tr>  
    <?php endwhile; ?>  
</tbody>
    </table> 
</body> 
</html>
