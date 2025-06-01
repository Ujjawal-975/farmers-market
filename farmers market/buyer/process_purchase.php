<?php
// process_purchase.php

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
include 'db_connection.php';

// Check if form data was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $buyer_name = isset($_POST['buyer_name']) ? trim($_POST['buyer_name']) : '';
    $buyer_phone = isset($_POST['buyer_phone']) ? trim($_POST['buyer_phone']) : '';

    // Simple validation
    if ($product_id > 0 && $quantity > 0 && !empty($buyer_name) && !empty($buyer_phone)) {
        
        // Insert purchase request into the database
        $sql = "INSERT INTO purchase_requests (product_id, quantity, buyer_name, buyer_phone) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("iiss", $product_id, $quantity, $buyer_name, $buyer_phone);

            if ($stmt->execute()) {
                // Show success message and redirect to dashboard
                echo "<script>
                    alert('Purchase request successful! We will contact you soon.');
                    window.location.href = 'dashboard.html';
                </script>";
            } else {
                // Show error message if execution failed
                echo "<script>
                    alert('Error: " . $conn->error . "');
                    window.location.href = 'dashboard.html';
                </script>";
            }
            $stmt->close();
        } else {
            // Show error message if preparation failed
            echo "<script>
                alert('Error preparing statement: " . $conn->error . "');
                window.location.href = 'dashboard.html';
            </script>";
        }

    } else {
        // Show validation error message
        echo "<script>
            alert('Invalid data provided. Please check your entries and try again.');
            window.location.href = 'dashboard.html';
        </script>";
    }
} else {
    // Show invalid request message
    echo "<script>
        alert('Invalid request.');
        window.location.href = 'dashboard.html';
    </script>";
}

$conn->close();
?>
















