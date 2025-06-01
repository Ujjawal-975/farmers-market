<?php
// Include database connection
include 'db_connection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $crop_name = $_POST['crop_name'];
    $crop_quantity = $_POST['crop_quantity'];
    $min_price = $_POST['min_price'];
    $max_price = $_POST['max_price'];
    $crop_type = $_POST['crop_type'];

    // Debug: Check if crop_type is being passed
    echo '<pre>';
    print_r($_POST); // This will show all submitted form data
    echo '</pre>';

    // Handle image upload
    $target_dir = "uploads/"; // Directory where images will be saved

    // Check if uploads directory exists, if not, create it
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true); // Create the directory if it doesn't exist
    }

    $target_file = $target_dir . basename($_FILES["crop_image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate file is an image
    $check = getimagesize($_FILES["crop_image"]["tmp_name"]);
    if ($check === false) {
        die("File is not an image.");
    }

    // Validate file type
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        die("Sorry, only JPG, JPEG, & PNG files are allowed.");
    }

    // Move uploaded file to the target directory
    if (!move_uploaded_file($_FILES["crop_image"]["tmp_name"], $target_file)) {
        die("Sorry, there was an error uploading your file.");
    }

    // Insert data into the database
    $sql = "INSERT INTO products (crop_name, crop_quantity, min_price, max_price, crop_type, crop_image) 
            VALUES (?, ?, ?, ?, ?, ?)";

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidsss", $crop_name, $crop_quantity, $min_price, $max_price, $crop_type, $target_file);

    if ($stmt->execute()) {
        // Success message and redirect to dashboard
        echo "<script>
                alert('Product uploaded successfully! Redirecting to the dashboard.');
                window.location.href = 'dashboard.html'; // Redirect to the dashboard
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
