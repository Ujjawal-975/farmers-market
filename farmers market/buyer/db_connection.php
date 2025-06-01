<?php
// db_connection.php
$servername = "localhost";
$username = "root"; // Change as per your database configuration
$password = ""; // Change as per your database configuration
$dbname = "farmers_market_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
