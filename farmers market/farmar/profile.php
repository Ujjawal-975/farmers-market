<?php
session_start();
include 'db_connection.php'; // Include your database connection file

// Assuming user_id is stored in the session directly after login
$user_id = $_SESSION['user_id'] ?? null;
$message = "";

if (!$user_id) {
    die("Error: User not logged in."); // Show an error if user_id is missing
}

// Connect to the database
$conn = new mysqli("localhost", "root", "", "farmers_market_db");

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user information from the database
$sql = "SELECT name, phone, village, district, state, username, password FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $phone, $village, $district, $state, $username, $password);
$stmt->fetch();
$stmt->close();

// Update user information if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = !empty($_POST['name']) ? $_POST['name'] : $name;
    $new_phone = !empty($_POST['phone']) ? $_POST['phone'] : $phone;
    $new_village = !empty($_POST['village']) ? $_POST['village'] : $village;
    $new_district = !empty($_POST['district']) ? $_POST['district'] : $district;
    $new_state = !empty($_POST['state']) ? $_POST['state'] : $state;
    $new_username = !empty($_POST['username']) ? $_POST['username'] : $username;
    $new_password = !empty($_POST['password']) ? $_POST['password'] : $password;

    // Update query
    $update_sql = "UPDATE users SET name = ?, phone = ?, village = ?, district = ?, state = ?, username = ?, password = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssssssi", $new_name, $new_phone, $new_village, $new_district, $new_state, $new_username, $new_password, $user_id);

    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
        $name = $new_name;
        $phone = $new_phone;
        $village = $new_village;
        $district = $new_district;
        $state = $new_state;
        $username = $new_username;
        $password = $new_password;
    } else {
        $message = "Error updating profile.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }
        .profile-container {
            width: 400px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #28a745;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="tel"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .message {
            text-align: center;
            color: green;
            margin-bottom: 10px;
        }
        .submit-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h1>Manage Profile</h1>
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">

            <label for="phone">Phone:</label>
            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">

            <label for="village">Village:</label>
            <input type="text" id="village" name="village" value="<?php echo htmlspecialchars($village); ?>">

            <label for="district">District:</label>
            <input type="text" id="district" name="district" value="<?php echo htmlspecialchars($district); ?>">

            <label for="state">State:</label>
            <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($state); ?>">

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter new password if changing">

            <input type="submit" class="submit-btn" value="Save Changes">
        </form>
    </div>
</body>
</html>
