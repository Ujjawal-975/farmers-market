

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Crops</title>
    <link rel="stylesheet" href="buy_crops.css">
</head>
<body>

<h1>Available Crops and Vegetables</h1>

<h2>Crops</h2>
<div class="product-grid">
    <?php while ($row = $crops_result->fetch_assoc()) { ?>
        <div class="product-card">
            <img src="<?php echo $row['crop_image']; ?>" alt="<?php echo $row['crop_name']; ?>" />
            <h3><?php echo $row['crop_name']; ?></h3>
            <p>Quantity: <?php echo $row['crop_quantity']; ?> kg</p>
            <p>Price: ₹<?php echo $row['min_price']; ?> - ₹<?php echo $row['max_price']; ?></p>
            <form action="cart.php" method="post">
                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>" />
                <label for="quantity">Select Quantity:</label>
                <input type="number" name="quantity" min="1" required />
                <button type="submit" name="add_to_cart">Add to Cart</button>
                <button type="submit" name="buy_now">Buy Now</button>
            </form>
        </div>
    <?php } ?>
</div>

<h2>Vegetables</h2>
<div class="product-grid">
    <?php while ($row = $vegetables_result->fetch_assoc()) { ?>
        <div class="product-card">
            <img src="<?php echo $row['crop_image']; ?>" alt="<?php echo $row['crop_name']; ?>" />
            <h3><?php echo $row['crop_name']; ?></h3>
            <p>Quantity: <?php echo $row['crop_quantity']; ?> kg</p>
            <p>Price: ₹<?php echo $row['min_price']; ?> - ₹<?php echo $row['max_price']; ?></p>
            <form action="cart.php" method="post">
                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>" />
                <label for="quantity">Select Quantity:</label>
                <input type="number" name="quantity" min="1" required />
                <button type="submit" name="add_to_cart">Add to Cart</button>
                <button type="submit" name="buy_now">Buy Now</button>
            </form>
        </div>
    <?php } ?>
</div>

</body>
</html>
