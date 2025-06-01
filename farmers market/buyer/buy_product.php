<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
$conn = new mysqli("localhost", "root", "", "farmers_market_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$message = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data and validate inputs
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $quantity_unit = $conn->real_escape_string(trim($_POST['quantity_unit']));
    $buyer_name = $conn->real_escape_string(trim($_POST['buyer_name']));
    $buyer_phone = $conn->real_escape_string(trim($_POST['buyer_phone']));
    $billing_address = $conn->real_escape_string(trim($_POST['billing_address']));

    // Check if all fields are filled
    if (empty($product_id) || empty($quantity) || empty($quantity_unit) || empty($buyer_name) || empty($buyer_phone) || empty($billing_address)) {
        $message = "All fields are required.";
    } else {
        // Insert order into the orders table with "Pending" status
        $sql = "INSERT INTO orders (product_id, quantity, quantity_unit, buyer_name, buyer_phone, billing_address, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("iissss", $product_id, $quantity, $quantity_unit, $buyer_name, $buyer_phone, $billing_address);
            if ($stmt->execute()) {
                $message = "Order placed successfully.";
            } else {
                $message = "Error placing order: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error preparing statement: " . $conn->error;
        }
    }
} else {
    // Fetch product details
    $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($product_id == 0) {
        die("Invalid product ID.");
    }

    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    if (!$product) {
        die("Product not found.");
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
    <title>Buy Product - Farmers Market</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 0; }
        .navbar { display: flex; justify-content: space-between; align-items: center; background-color: #4CAF50; padding: 15px; color: white; }
        .navbar h1 { margin: 0; font-size: 24px; font-weight: bold; }
        .navbar .links { display: flex; gap: 10px; }
        .navbar a { color: white; text-decoration: none; padding: 8px 15px; border: 1px solid white; border-radius: 5px; }
        .product-container { width: 50%; margin: 20px auto; padding: 20px; background-color: white; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); border-radius: 5px; }
        .product-image { width: 100%; height: 250px; object-fit: cover; border-radius: 5px; }
        .product-info { display: flex; flex-direction: column; gap: 10px; margin-top: 20px; }
        form { display: flex; flex-direction: column; gap: 10px; }
        form label { font-weight: bold; }
        form input, form select, form textarea {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 100%;
        }
        .btn { background-color: #4CAF50; color: white; padding: 10px; border: none; cursor: pointer; border-radius: 5px; width: 100%; font-size: 14px; }
        .btn:hover { background-color: #45a049; }
        .message { color: green; text-align: center; font-weight: bold; margin: 20px 0; }
    </style>
</head>
<body>

<div class="navbar">
    <h1>मंडी मित्र</h1>
    <div class="links">
        <a href="dashboard.html" class="home-link">Home</a>
        <a href="cart.php" class="cart-link">Cart</a>
    </div>
</div>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <p class="message"><?php echo htmlspecialchars($message); ?></p>
<?php else: ?>
    <h1 style="text-align: center; margin-top: 20px;">Product Details</h1>
    <div class="product-container">
        <img src="farmar/uploads/<?php echo htmlspecialchars($product['crop_image']); ?>" alt="<?php echo htmlspecialchars($product['crop_name']); ?>" class="product-image">
        <div class="product-info">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($product['crop_name']); ?></p>
            <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($product['min_price']); ?> - ₹<?php echo htmlspecialchars($product['max_price']); ?></p>
            <p><strong>Quantity Available:</strong> <?php echo htmlspecialchars($product['crop_quantity']); ?> kg</p>

            <!-- Purchase form -->
            <form method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" min="1" max="<?php echo $product['crop_quantity']; ?>" value="1" required>
                <label for="quantity_unit">Unit:</label>
                <select name="quantity_unit" id="quantity_unit" required>
                    <option value="kg">kg</option>
                    <option value="quintal">quintal</option>
                </select>
                <label for="buyer_name">Your Name:</label>
                <input type="text" name="buyer_name" id="buyer_name" required>
                <label for="buyer_phone">Phone Number:</label>
                <input type="tel" name="buyer_phone" id="buyer_phone" required>
                <label for="billing_address">Billing Address:</label>
                <textarea name="billing_address" id="billing_address" rows="3" required></textarea>
                <button type="submit" class="btn">Proceed to Buy</button>
            </form>
        </div>
    </div>
<?php endif; ?>

</body>
</html>
