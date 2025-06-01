<?php
header('Content-Type: application/json');
include 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);

$productId = $data['productId'];
$quantity = $data['quantity'];
$buyerName = $data['buyerName'];
$buyerPhone = $data['buyerPhone'];
$productName = $data['productName'];

// Insert notification data into the `notifications` table
$sql = "INSERT INTO notifications (product_id, product_name, quantity, buyer_name, buyer_phone, status) VALUES (?, ?, ?, ?, ?, 'Pending')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isiss", $productId, $productName, $quantity, $buyerName, $buyerPhone);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Notification sent successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to send notification"]);
}

$conn->close();
?>
