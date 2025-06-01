<?php
include 'db_connection.php';

$sql = "SELECT * FROM notifications WHERE status = 'Pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests - Farmers Market</title>
</head>
<body>
    <h1>Pending Requests</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Buyer Name</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['product_name']; ?></td>
                    <td><?php echo $row['quantity']; ?> kg</td>
                    <td><?php echo $row['buyer_name']; ?></td>
                    <td><?php echo $row['buyer_phone']; ?></td>
                    <td>
                        <form action="accept_request.php" method="POST">
                            <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                            <button type="submit">Accept</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No pending requests.</p>
    <?php endif; ?>

</body>
</html>
