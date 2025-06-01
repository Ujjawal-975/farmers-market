<?php
// Database connection details
$host = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "farmers_market_db"; // Your database name

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the posted data from the login form
$user = $_POST['username'];
$pass = $_POST['password'];

// Prepare and execute a query to check if the username and password are correct
$sql = "SELECT * FROM buyers WHERE username = '$user' AND password = '$pass'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Successful login, redirect to the dashboard
    echo "<script>
            alert('Login successful! Redirecting to your dashboard.');
            window.location.href = 'dashboard.html'; // Replace with your dashboard page
          </script>";
} else {
    // Login failed, show a pop-up asking to register first
    echo "<script>
            alert('Invalid login details. Please register first.');
            window.location.href = 'registration.html'; // Redirect to the registration page
          </script>";
}

// Close the connection
$conn->close();
?>
