<?php
require 'db.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

$sql = "SELECT user_addresses.latitude, user_addresses.longitude
        FROM orders 
        JOIN user_addresses ON orders.address_id = user_addresses.address_id
        WHERE orders.orderid = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode([
        'latitude' => (float)$data['latitude'], // Cast to float
        'longitude' => (float)$data['longitude']  // Cast to float
    ]);
} else {
    echo json_encode(['latitude' => null, 'longitude' => null]);
}

$stmt->close();
$conn->close();
?>
