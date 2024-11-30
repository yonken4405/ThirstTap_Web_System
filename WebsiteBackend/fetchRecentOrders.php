<?php
header('Content-Type: application/json');
require 'db.php';
session_start();

$station_id = $_SESSION['station_id'];

// Get the current time and subtract 1 day (86400 seconds) to filter orders from the last 24 hours
$yesterday = date('Y-m-d H:i:s', strtotime('-1 day'));

$sql = "SELECT orderid, delivery_date, status FROM orders WHERE station_id = ? AND delivery_date >= ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $station_id, $yesterday);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode($orders);
?>
