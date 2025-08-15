<?php
$host = "localhost";       // XAMPP default host
$user = "root";            // Default MySQL user in XAMPP
$pass = "";                // Default password is empty
$dbname = "bookexchangedb"; 
$port = 3307;// Your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname,$port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
