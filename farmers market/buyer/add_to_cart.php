<?php
// products.php

// Include database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "farmers_market_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for a successful connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Start the session
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect the user to the login page if not logged in
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Farmers Market</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #4CAF50;
            padding: 15px;
            color: white;
        }
        .navbar h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .navbar .cart-link, .navbar .home-link {
            font-size: 18px;
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border: 1px solid white;
            border-radius: 5px;
        }
        .navbar .cart-link:hover, .navbar .home-link:hover {
            background-color: #45a049;
        }
        h1.page-title {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }
        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }
        .product-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 200px;
            padding: 10px;
            text-align: center;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .product-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }
        h2 {
            font-size: 18px;
            color: #333;
            margin: 10px 0;
        }
        p {
            font-size: 14px;
            color: #666;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 5px;
            width: 100%;
            font-size: 14px;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <!-- Navigation bar with Home and Cart option -->
    <div class="navbar">
        <h1>मंडी मित्र</h1>
        <div>
            <a href="dashboard.html" class="home-link">Home</a>
            <a href="cart.php" class="cart-link">Cart</a>
        </div>
    </div>

    <h1 class="page-title">Available Products</h1>
    <div class="product-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Directly use the stored image path
                $imagePath = $row['crop_image']; 
                $serverPath = $_SERVER['DOCUMENT_ROOT'] . "/02/farmar/" . $imagePath;

                // Check if file exists
                if (!file_exists($serverPath)) {
                    $imagePath = "uploads/default.png"; // Default image if file not found
                }

                $quantity = $row['crop_quantity'] >= 100 ? ($row['crop_quantity'] / 100) . ' quintal' : $row['crop_quantity'] . ' kg';
                ?>
                <div class="product-card">
                    <img src="<?php echo "/02/farmar/" . $imagePath; ?>" alt="<?php echo $row['crop_name']; ?>" class="product-image">
                    <h2><?php echo $row['crop_name']; ?></h2>
                    <p>Quantity: <?php echo $quantity; ?></p>
                    <p>Price: ₹<?php echo $row['min_price']; ?> - ₹<?php echo $row['max_price']; ?></p>
                    <p>Type: <?php echo $row['crop_type']; ?></p>
                    <button class="btn add-to-cart" onclick="addToCart(<?php echo $row['id']; ?>, '<?php echo $row['crop_name']; ?>', <?php echo $row['min_price']; ?>, '<?php echo $imagePath; ?>')">Add to Cart</button>
                    <button class="btn buy-now" onclick="buyNow(<?php echo $row['id']; ?>)">Buy Now</button>
                </div>
                <?php
            }
        } else {
            echo "<p>No products available.</p>";
        }
        $conn->close();
        ?>
    </div>

    <script>
        function addToCart(productId, productName, price, image) {
            console.log('Add to Cart button clicked');
            
            const data = {
                productId: productId,
                productName: productName,
                price: price,
                image: image
            };

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "add_to_cart.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert(xhr.responseText);
                }
            };

            const params = new URLSearchParams(data).toString();
            xhr.send(params);
        }

        function buyNow(productId) {
            window.location.href = "buy_product.php?id=" + productId;
        }
    </script>

</body>
</html>

<?php
// add_to_cart.php

// Include database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "farmers_market_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming the user is logged in and user_id is stored in session
session_start();
$user_id = $_SESSION['user_id']; // Get user ID from session

// Get product details from the POST request
$productId = $_POST['productId'];
$productName = $_POST['productName'];
$price = $_POST['price'];
$image = $_POST['image'];

// Debugging output
echo "Received POST Data: <br>";
echo "Product ID: $productId <br>";
echo "Product Name: $productName <br>";
echo "Price: $price <br>";
echo "Image: $image <br>";

// Insert product details into the cart_items table
$sql = "INSERT INTO cart_items (user_id, product_id, product_name, price, image) 
        VALUES ('$user_id', '$productId', '$productName', '$price', '$image')";

if ($conn->query($sql) === TRUE) {
    echo "Product added to cart successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
