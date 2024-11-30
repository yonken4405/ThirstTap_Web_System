<?php
header('Content-Type: application/json');
require 'db.php'; // Ensure this file contains your database connection code

// Get POST data
$user_id = $_POST['user_id'];
$delivery_date = $_POST['delivery_date']; // Delivery date from the frontend
$total_price = $_POST['total_price'];
$order_items_json = $_POST['order_items'];
$station_id = $_POST['station_id'];
$station_name = $_POST['station_name'];
$delivery_address = $_POST['delivery_address'];
$payment_method = $_POST['payment_method'];
$address_id = $_POST['address_id'];
$additional_info = $_POST['additional_info'];

// Validate input
if (!isset($user_id, $delivery_date, $total_price, $order_items_json)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
    exit;
}

// Decode JSON order items
$order_items = json_decode($order_items_json, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON for order items.']);
    exit;
}

// Parse and format delivery date to MySQL DATE format
$timestamp = strtotime($delivery_date);
if ($timestamp === false) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid date format.']);
    exit;
}
$formatted_date = date('Y-m-d', $timestamp); // Format to "YYYY-MM-DD"

// Insert order into orders table
$stmt = $conn->prepare("INSERT INTO orders(userid, delivery_date, total_price, station_id, station_name, delivery_address, payment_method, address_id, additional_info) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isdssssis", $user_id, $formatted_date, $total_price, $station_id, $station_name, $delivery_address, $payment_method, $address_id, $additional_info); // Use the formatted date here
if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to place order.']);
    exit;
}

// Get the last inserted order ID
$order_id = $stmt->insert_id;
$stmt->close();

// Insert each order item into order_items table
foreach ($order_items as $item) {
    $water_type = $item['water_type'];
    $gallon_size = $item['gallon_size']; // Store the entire string
    $quantity = intval($item['quantity']);
    $is_new_container = $item['is_new_container'] ? 1 : 0;
    $item_total_price = $item['total_price']; // No need for floatval here

    $stmt = $conn->prepare("INSERT INTO order_items (order_id, water_type, gallon_size, quantity, is_new_container, total_price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issidi", $order_id, $water_type, $gallon_size, $quantity, $is_new_container, $item_total_price);
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to insert order item.']);
        $stmt->close();
        exit;
    }
    $stmt->close();
}

// Return success response
echo json_encode(['status' => 'success', 'message' => 'Order placed successfully!']);

// Close connection
$conn->close();
?>
