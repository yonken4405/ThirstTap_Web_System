<?php
session_start(); // Start the session
require 'db.php';

$filter = 'monthly'; // Default filter
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filter = $_POST['date-filter'];
}

// Ensure that the station_id is set in the session
$station_id = isset($_SESSION['station_id']) ? $_SESSION['station_id'] : null;

// Prepare SQL query based on the selected filter
$sql = "SELECT orderid, total_price, delivery_date 
        FROM orders 
        WHERE station_id = ? AND status = 'Completed'";

// Add date filter to the SQL query
switch ($filter) {
    case 'today':
        $sql .= " AND DATE(STR_TO_DATE(delivery_date, '%M %d, %Y')) = CURDATE()";
        break;
    case 'weekly':
        $sql .= " AND STR_TO_DATE(delivery_date, '%M %d, %Y') >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case 'monthly':
        $sql .= " AND MONTH(STR_TO_DATE(delivery_date, '%M %d, %Y')) = MONTH(CURDATE()) AND YEAR(STR_TO_DATE(delivery_date, '%M %d, %Y')) = YEAR(CURDATE())";
        break;
    case 'yearly':
        $sql .= " AND YEAR(STR_TO_DATE(delivery_date, '%M %d, %Y')) = YEAR(CURDATE())";
        break;
}

// Prepare the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $station_id);
$stmt->execute();
$result = $stmt->get_result();

$totalSales = 0;
$totalOrders = 0;

while ($row = $result->fetch_assoc()) {
    $totalSales += (float)$row['total_price'];
    $totalOrders++;
}

$stmt->close();
$conn->close();

// Return the total sales and orders as JSON
header('Content-Type: application/json');
echo json_encode(['totalSales' => $totalSales, 'totalOrders' => $totalOrders]);
?>