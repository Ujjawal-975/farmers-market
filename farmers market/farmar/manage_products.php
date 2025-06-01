<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "farmers_market_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete (Sold Out)
if (isset($_POST['delete'])) {
    $product_id = $_POST['product_id'];

    // Delete dependent rows in the orders table if any
    $conn->query("DELETE FROM orders WHERE product_id = $product_id");

    // Delete product from products table
    $delete_sql = "DELETE FROM products WHERE id = $product_id";
    if ($conn->query($delete_sql) === TRUE) {
        echo "<script>alert('Product deleted successfully!'); window.location='manage_products.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Handle update (Update Product)
if (isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $crop_name = $_POST['crop_name'];
    $crop_quantity = $_POST['crop_quantity'];
    $min_price = $_POST['min_price'];
    $max_price = $_POST['max_price'];
    $crop_type = $_POST['crop_type'];

    $update_sql = "UPDATE products SET 
                    crop_name='$crop_name', 
                    crop_quantity='$crop_quantity', 
                    min_price='$min_price', 
                    max_price='$max_price', 
                    crop_type='$crop_type' 
                    WHERE id=$product_id";

    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Product updated successfully!'); window.location='manage_products.php';</script>";
    } else {
        echo "Error updating product: " . $conn->error;
    }
}

// Fetch data from the products table
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <style>
        /* Navigation bar styling */
        body {
            font-family: Arial, sans-serif;
        }
        .navbar {
            display: flex;
            background-color: #28a745;
            padding: 10px 20px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
        }
        .navbar a:hover {
            background-color: #575757;
        }
        .navbar .logo {
            font-size: 20px;
            font-weight: bold;
            flex-grow: 1;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            text-align: center;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }

        /* Update form styling */
        #updateForm {
            display: none;
            border: 1px solid black;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
    <script>
        // JavaScript to handle Update Form
        function openUpdateForm(id, name, quantity, minPrice, maxPrice, type) {
            document.getElementById("updateForm").style.display = "block";
            document.getElementById("product_id").value = id;
            document.getElementById("crop_name").value = name;
            document.getElementById("crop_quantity").value = quantity;
            document.getElementById("min_price").value = minPrice;
            document.getElementById("max_price").value = maxPrice;
            document.getElementById("crop_type").value = type;
        }

        function closeUpdateForm() {
            document.getElementById("updateForm").style.display = "none";
        }
    </script>
</head>
<body>

<!-- Navigation Bar -->
<div class="navbar">
    <span class="logo">मंडी मित्र</span>
    <a href="dashboard.html">Home</a>
    <a href="http://localhost/02/index.html">Logout</a>
</div>

<h2>Manage Products</h2>

<?php
if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Crop Name</th>
                <th>Quantity</th>
                <th>Min Price</th>
                <th>Max Price</th>
                <th>Crop Type</th>
                <th>Crop Image</th>
                <th>Actions</th>
            </tr>";
    
    while ($row = $result->fetch_assoc()) {
        // Format quantity
        $quantity = $row['crop_quantity'] >= 100 ? $row['crop_quantity'] . " Quintal" : $row['crop_quantity'] . " kg";

        // Format prices
        $min_price = "₹" . $row['min_price'];
        $max_price = "₹" . $row['max_price'];

        // Define the image path dynamically from the database path
        $imagePath = "uploads/" . $row['crop_image']; // Assuming 'crop_image' holds the relative path

        // Use the image path from the database to display image
        $imageTag = file_exists($_SERVER['DOCUMENT_ROOT'] . "/02/farmar/" . $imagePath) ? 
            "<img src='$imagePath' alt='{$row['crop_name']}' width='100'>" : "Image not found!";

        echo "<tr>
                <td>{$row['crop_name']}</td>
                <td>$quantity</td>
                <td>$min_price</td>
                <td>$max_price</td>
                <td>{$row['crop_type']}</td>
                <td>$imageTag</td>
                <td>
                    <form method='post' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this product?\")'>
                        <input type='hidden' name='product_id' value='{$row['id']}'>
                        <button type='submit' name='delete'>Sold Out</button>
                    </form>
                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='product_id' value='{$row['id']}'>
                        <button type='button' onclick='openUpdateForm({$row['id']}, \"{$row['crop_name']}\", \"{$row['crop_quantity']}\", \"{$row['min_price']}\", \"{$row['max_price']}\", \"{$row['crop_type']}\")'>Update</button>
                    </form>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No products found.";
}
?>

<!-- Update Form (Hidden by default) -->
<div id="updateForm">
    <h3>Update Product</h3>
    <form method="post">
        <input type="hidden" name="product_id" id="product_id">
        <label>Crop Name:</label>
        <input type="text" name="crop_name" id="crop_name" required><br><br>
        <label>Quantity:</label>
        <input type="number" name="crop_quantity" id="crop_quantity" required><br><br>
        <label>Min Price:</label>
        <input type="number" step="0.01" name="min_price" id="min_price" required><br><br>
        <label>Max Price:</label>
        <input type="number" step="0.01" name="max_price" id="max_price" required><br><br>
        <label>Crop Type:</label>
        <select name="crop_type" id="crop_type" required>
            <option value="Vegetable">Vegetable</option>
            <option value="Crop">Crop</option>
        </select><br><br>
        <button type="submit" name="update_product">Update Product</button>
        <button type="button" onclick="closeUpdateForm()">Cancel</button>
    </form>
</div>

</body>
</html>
