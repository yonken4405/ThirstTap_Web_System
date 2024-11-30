<?php
require 'db.php'; // Include your database connection

// Get the order ID from the query parameters
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Initialize response data
$response = [
    'orderid' => null,
    'order_date' => null,
    'customer_name' => null,
    'payment_method' => null,
    'delivery_address' => null,
    'total_price' => null,
    'order_date' => null,
    'status' => null, // Add status here
];

// Fetch order details
$orderDetailsQuery = "SELECT * FROM orders WHERE orderid = ?";
$stmt = $conn->prepare($orderDetailsQuery);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$orderResult = $stmt->get_result();

if ($orderResult->num_rows > 0) {
    $orderData = $orderResult->fetch_assoc();

    // Fetch user data based on userid in the order
    $userId = $orderData['userid'];
    $userQuery = "SELECT * FROM user_data WHERE userid = ?";
    $userStmt = $conn->prepare($userQuery);
    $userStmt->bind_param("i", $userId);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    
    $userData = $userResult->num_rows > 0 ? $userResult->fetch_assoc() : null;

    // Combine order data and user data
    $response['orderid'] = $orderData['orderid'];
    $response['order_date'] = $orderData['delivery_date'];
    $response['customer_name'] = $userData ? $userData['name'] : 'Unknown';
    $response['payment_method'] = $orderData['payment_method'];
    $response['delivery_address'] = $orderData['delivery_address'];
    $response['total_price'] = $orderData['total_price'];
    $response['order_date'] = $orderData['order_date'];
    $response['status'] = $orderData['status']; // Fetch status from order data
}

// Output JSON response
header('Content-Type: application/json');
echo json_encode($response);

// Close the connections
$stmt->close();
$conn->close();
?>
