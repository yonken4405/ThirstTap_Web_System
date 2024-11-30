<?php
header('Content-Type: application/json');
require 'db.php';
session_start(); // Start the session to access session variables

$station_id = $_SESSION['station_id']; // Get the station ID from the session

// Prepare SQL to get counts of different statuses
$sqlStatus = "SELECT status, COUNT(*) as count FROM orders WHERE station_id = ? GROUP BY status";
$stmtStatus = $conn->prepare($sqlStatus);
$stmtStatus->bind_param('s', $station_id); // Bind station_id
$stmtStatus->execute();
$resultStatus = $stmtStatus->get_result();

$orderCounts = [
    'Pending' => 0,
    'Ongoing' => 0,
    'Completed' => 0,
    'Cancelled' => 0
];

// Map statuses to appropriate fields
while ($row = $resultStatus->fetch_assoc()) {
    $status = $row['status'];
    $count = (int)$row['count'];
    
    // Update the count for each status
    if (isset($orderCounts[$status])) {
        $orderCounts[$status] = $count;
    }
}

// Prepare SQL to get sales trends (sum of sales grouped by month)
$sqlSales = "SELECT MONTH(order_date) as month, SUM(total_price) as total_sales 
             FROM orders 
             WHERE station_id = ? 
             GROUP BY MONTH(order_date) 
             ORDER BY MONTH(order_date)";
             
$stmtSales = $conn->prepare($sqlSales);
$stmtSales->bind_param('s', $station_id); // Bind station_id
$stmtSales->execute();
$resultSales = $stmtSales->get_result();

$salesTrends = array_fill(0, 12, 0); // Initialize array for 12 months (Jan-Dec)

// Map sales data to the correct month
while ($row = $resultSales->fetch_assoc()) {
    $month = (int)$row['month']; // Get month as integer (1 = Jan, 2 = Feb, etc.)
    $total_sales = (float)$row['total_sales'];
    
    // Store total sales in the correct month index (month - 1 for zero-based index)
    $salesTrends[$month - 1] = $total_sales;
}

// Combine both datasets into a single response
$response = [
    'orderCounts' => $orderCounts,
    'salesTrends' => $salesTrends
];

echo json_encode($response);
?>
