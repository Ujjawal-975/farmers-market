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

// Retrieve form data
$name = $_POST['name'];
$phone = $_POST['phone'];
$billing_address = $_POST['billing_address'];
$gst = $_POST['gst'];
$user = $_POST['username'];
$pass = $_POST['password'];

// Insert data into the buyers table
$sql = "INSERT INTO buyers (name, phone, billing_address, gst, username, password) 
        VALUES ('$name', '$phone', '$billing_address', '$gst', '$user', '$pass')";

if ($conn->query($sql) === TRUE) {
    echo "<script>
            alert('Registration submitted successfully. You can log in now.');
            window.location.href = 'login.html';
          </script>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the connection
$conn->close();
?>
