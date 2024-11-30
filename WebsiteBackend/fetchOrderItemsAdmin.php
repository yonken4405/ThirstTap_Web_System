<?php
require 'db.php'; // Include your database connection

// Get the order ID from the query parameters
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Initialize order items array
$orderItems = [];

// Fetch order items
$orderItemsQuery = "SELECT * FROM order_items WHERE order_id = ?";
$itemStmt = $conn->prepare($orderItemsQuery);
$itemStmt->bind_param("i", $order_id);
$itemStmt->execute();
$itemResult = $itemStmt->get_result();

while ($item = $itemResult->fetch_assoc()) {
    $orderItems[] = $item;
}

// Output JSON response
header('Content-Type: application/json');
echo json_encode($orderItems);

// Close the connection
$itemStmt->close();
$conn->close();
?>
