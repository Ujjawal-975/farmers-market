<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
$conn = new mysqli("localhost", "root", "", "farmers_market_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Accept/Reject/Deliver actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($request_id > 0 && in_array($action, ["accept", "reject", "deliver"])) {
        if ($action == "accept") {
            // Update the status to Accepted
            $update_sql = "UPDATE orders SET status = 'Accepted' WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            if ($stmt) {
                $stmt->bind_param("i", $request_id);
                $stmt->execute();
                $stmt->close();
                echo "<script>alert('Request has been accepted.');</script>";
            }
        } elseif ($action == "deliver" || $action == "reject") {
            // Delete the request after delivery or rejection
            $delete_sql = "DELETE FROM orders WHERE id = ?";
            $stmt = $conn->prepare($delete_sql);
            if ($stmt) {
                $stmt->bind_param("i", $request_id);
                $stmt->execute();
                $stmt->close();
                echo "<script>alert('Product has been " . ($action == "deliver" ? "delivered" : "rejected") . ".');</script>";
            }
        }
    }
}

// Fetch all purchase requests
$sql = "SELECT * FROM orders";
$result = $conn->query($sql);

if ($result === false) {
    die("Error fetching data: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Requests</title>
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
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; }
        .navbar { display: flex; justify-content: space-between; align-items: center; background-color: #4CAF50; padding: 15px; color: white; }
        .navbar h1 { margin: 0; font-size: 24px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; background-color: white; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #4CAF50; color: white; }
        .btn { padding: 5px 10px; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .accept-btn { background-color: #4CAF50; }
        .reject-btn { background-color: #f44336; }
        .deliver-btn { background-color: #2196F3; }
        .print-btn { background-color: #FFC107; color: black; }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="navbar">
    <span class="logo">मंडी मित्र</span>
    <a href="dashboard.html">Home</a>
    <a href="http://localhost/02/index.html">Logout</a>
</div>
<h2>Purchase Requests</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Product ID</th>
            <th>Quantity</th>
            <th>Quantity Unit</th>
            <th>Buyer Name</th>
            <th>Buyer Phone</th>
            <th>Billing Address</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['product_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity_unit']); ?></td>
                    <td><?php echo htmlspecialchars($row['buyer_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['buyer_phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['billing_address']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <?php if ($row['status'] == "Pending"): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="action" value="accept" class="btn accept-btn">Accept</button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="action" value="reject" class="btn reject-btn">Reject</button>
                            </form>
                        <?php elseif ($row['status'] == "Accepted"): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                <button type="button" class="btn print-btn" onclick="printReceipt(<?php echo $row['id']; ?>)">Print</button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="action" value="deliver" class="btn deliver-btn">Deliver</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="9">No purchase requests found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
    function printReceipt(requestId) {
        const url = `print_receipt.php?request_id=${requestId}`;
        const printWindow = window.open(url, "_blank");
        printWindow.focus();
    }
</script>

</body>
</html>

<?php $conn->close(); ?>
