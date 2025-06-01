<?php
// cart.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        /* Inline CSS Styles */
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

        .cart-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .cart-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 200px;
            padding: 10px;
            text-align: center;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            background-color: #fff;
        }

        .cart-image {
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
            font-size: 14px;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .remove-btn {
            background-color: #f44336;
        }

        .remove-btn:hover {
            background-color: #e53935;
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

    <h1 class="page-title">Your Cart</h1>
    <div class="cart-container" id="cartItems">
        <!-- Cart items will be injected here by JavaScript -->
    </div>

    <script>
        window.onload = function () {
            // Load cart data from localStorage
            let cart = JSON.parse(localStorage.getItem('cart')) || [];

            // Check if cart has items
            if (cart.length > 0) {
                cart.forEach((item, index) => {
                    // Dynamically generate the correct image path
                    const imagePath = item.image.startsWith('uploads/')
                        ? item.image
                        : `uploads/${item.image}`;

                    // Generate cart item HTML
                    const cartItemHTML = `
                        <div class="cart-card">
                            <img src="${imagePath}" alt="${item.productName}" class="cart-image">
                            <h2>${item.productName}</h2>
                            <p>Price: ₹${item.price}</p>
                            <button class="btn remove-btn" onclick="removeFromCart(${index})">Remove</button>
                            <button class="btn buy-now" onclick="buyNow(${item.productId})">Buy Now</button>
                        </div>
                    `;
                    document.getElementById('cartItems').innerHTML += cartItemHTML;
                });
            } else {
                document.getElementById('cartItems').innerHTML = '<p>Your cart is empty.</p>';
            }
        };

        // Function to remove item from cart
        function removeFromCart(index) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            cart.splice(index, 1); // Remove the item from the cart array
            localStorage.setItem('cart', JSON.stringify(cart)); // Update localStorage

            // Reload the cart to reflect changes
            window.location.reload();
        }

        // Function to handle Buy Now button (redirect to buy_product.php with the product ID)
        function buyNow(productId) {
            window.location.href = `buy_product.php?id=${productId}`;
        }
    </script>
</body>
</html>
