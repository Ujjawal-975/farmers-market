<?php
// Database connection details
$servername = "localhost";  
$username = "root";  
$password = "";  
$dbname = "farmers_market_db";  

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $village = $_POST['village'];
    $district = $_POST['district'];
    $state = $_POST['state'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check for existing username or phone number
    $checkSql = "SELECT * FROM users WHERE username = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $username); // Changed to a single 's' for the username
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Username already exists
        header("Location: registration.html?error=exists");
        exit();
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL query
    $sql = "INSERT INTO users (name, phone, village, district, state, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Check if the prepare statement failed
    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }

    // Bind the parameters to the SQL query
    $stmt->bind_param("sssssss", $name, $phone, $village, $district, $state, $username, $hashed_password);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect back to the registration page with a success message
        header("Location: registration.html?success=true");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
