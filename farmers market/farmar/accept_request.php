<?php
include 'db_connection.php';

$request_id = $_POST['request_id'];

// Update notification status
$sql = "UPDATE notifications SET status = 'Accepted' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $request_id);
$stmt->execute();

header("Location: requests.php");
?>
