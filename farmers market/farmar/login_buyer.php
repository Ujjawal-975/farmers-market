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

// Prepare and execute a query to check if the username exists
$stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the stored hashed password
    $row = $result->fetch_assoc();
    $hashed_password = $row['password'];
    
    // Verify the entered password with the hashed password
    if (password_verify($pass, $hashed_password)) {
        // Successful login, redirect to the dashboard
        echo "<script>
                alert('Login successful! Redirecting to your dashboard.');
                window.location.href = 'dashboard.html'; // Replace with your dashboard page
              </script>";
    } else {
        // Password is incorrect
        echo "<script>
                alert('Invalid login details. Please try again.');
                window.location.href = 'login.html'; // Redirect to the login page
              </script>";
    }
} else {
    // Username not found
    echo "<script>
            alert('Username not found. Please register first.');
            window.location.href = 'registration.html'; // Redirect to the registration page
          </script>";
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
	