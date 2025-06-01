<?php
// purchase_and_notify.php

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
$conn = new mysqli('localhost', 'username', 'password', 'farmers_market_db');
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check for purchase request submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buy_product'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $buyer_name = trim($_POST['buyer_name']);
    $buyer_phone = trim($_POST['buyer_phone']);

    // Insert purchase request into notifications table
    $sqlNotification = "INSERT INTO notifications (product_id, buyer_name, buyer_phone, quantity) VALUES (?, ?, ?, ?)";
    $stmtNotification = $conn->prepare($sqlNotification);
    $stmtNotification->bind_param("issi", $product_id, $buyer_name, $buyer_phone, $quantity);

    if ($stmtNotification->execute()) {
        echo "<script>alert('Purchase request successful! Seller will be notified.'); window.location.href='purchase_and_notify.php';</script>";
    } else {
        echo "<p>Error: " . $conn->error . "</p>";
    }
    $stmtNotification->close();
}

// Check if a specific product is requested for purchase view
$product = null;
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Display notifications for the seller
$notifications = [];
$sqlNotifications = "SELECT notifications.*, products.crop_name FROM notifications JOIN products ON notifications.product_id = products.id WHERE status = 'pending'";
$result = $conn->query($sqlNotifications);
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

// Accept request functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accept_request'])) {
    $notification_id = intval($_POST['notification_id']);
    $sqlAccept = "UPDATE notifications SET status = 'accepted' WHERE id = ?";
    $stmtAccept = $conn->prepare($sqlAccept);
    $stmtAccept->bind_param("i", $notification_id);
    $stmtAccept->execute();
    echo "<script>alert('Request accepted successfully.'); window.location.href='purchase_and_notify.php';</script>";
    $stmtAccept->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buy Product & Notifications - Farmers Market</title>
    <style>
        /* Styling for page elements */
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 0; }
        .navbar { display: flex; justify-content: space-between; align-items: center; background-color: #4CAF50; padding: 15px; color: white; }
        .navbar a { color: white; text-decoration: none; padding: 8px 15px; border: 1px solid white; border-radius: 5px; }
        .container { padding: 20px; }
        .product-container, .notifications { margin: 20px auto; padding: 20px; background-color: white; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); border-radius: 5px; width: 80%; }
        .btn { background-color: #4CAF50; color: white; padding: 10px; border: none; cursor: pointer; border-radius: 5px; margin-top: 10px; font-size: 14px; }
        .btn.accept { background-color: #45a049; }
        .btn:hover { background-color: #45a049; }
    </style>
</head>
<body>

<div class="navbar">
    <h1>मंडी मित्र</h1>
    <a href="purchase_and_notify.php">Home</a>
</div>

<div class="container">

    <!-- Product Purchase Section -->
    <?php if ($product): ?>
        <h2>Product Details</h2>
        <div class="product-container">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($product['crop_name']); ?></p>
            <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($product['min_price']); ?> - ₹<?php echo htmlspecialchars($product['max_price']); ?></p>
            <p><strong>Available Quantity:</strong> <?php echo htmlspecialchars($product['crop_quantity']); ?> kg</p>
            <form action="purchase_and_notify.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" min="1" max="<?php echo $product['crop_quantity']; ?>" required>
                <br>
                <label for="buyer_name">Your Name:</label>
                <input type="text" name="buyer_name" id="buyer_name" required>
                <br>
                <label for="buyer_phone">Phone Number:</label>
                <input type="tel" name="buyer_phone" id="buyer_phone" required>
                <br>
                <button type="submit" name="buy_product" class="btn">Proceed to Buy</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Notifications for Seller Section -->
    <h2>Purchase Notifications</h2>
    <div class="notifications">
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $note): ?>
                <div>
                    <p><strong>Product:</strong> <?php echo htmlspecialchars($note['crop_name']); ?></p>
                    <p><strong>Buyer Name:</strong> <?php echo htmlspecialchars($note['buyer_name']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($note['buyer_phone']); ?></p>
                    <p><strong>Quantity:</strong> <?php echo htmlspecialchars($note['quantity']); ?></p>
                    <form action="purchase_and_notify.php" method="POST" style="display:inline;">
                        <input type="hidden" name="notification_id" value="<?php echo $note['id']; ?>">
                        <button type="submit" name="accept_request" class="btn accept">Accept</button>
                    </form>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No new notifications.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
