<?php
$host = "localhost";       // XAMPP default host
$user = "root";            // Default MySQL user in XAMPP
$pass = "";                // Default password is empty
$dbname = "bookexchange"; 
$port = 3307;


$conn = new mysqli($host, $user, $pass, $dbname,$port);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>