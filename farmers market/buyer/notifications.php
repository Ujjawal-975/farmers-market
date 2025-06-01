<?php
// notifications.php
include 'db_connection.php';

$sql = "SELECT * FROM notifications ORDER BY date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Farmers Market</title>
</head>
<body>

    <h1>New Purchase Notifications</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Buyer Name</th>
                <th>Phone</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['buyer_name']}</td>
                            <td>{$row['buyer_phone']}</td>
                            <td>{$row['product_name']}</td>
                            <td>{$row['quantity']}</td>
                            <td>{$row['date']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No notifications</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>
