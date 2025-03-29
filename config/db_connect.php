<?php
$host = "localhost";  // Change if using a different database server
$username = "root";   // Your database username
$password = "";       // Your database password
$dbname = "legacy_db"; // Replace with your actual database name

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
