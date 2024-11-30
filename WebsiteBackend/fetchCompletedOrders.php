<?php
session_start(); // Start the session
require 'db.php'; // Include your database connection file

// Default filter
$filter = 'monthly'; // Change as needed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filter = $_POST['date-filter'];
}

// Ensure that the station_id is set in the session
$station_id = isset($_SESSION['station_id']) ? $_SESSION['station_id'] : null;

// Prepare SQL query to fetch completed orders
$sql = "SELECT orderid, total_price, delivery_date FROM orders WHERE station_id = ? AND status = 'Completed'";

// Add date filter to the SQL query using STR_TO_DATE for 'delivery_date'
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

// Bind the station_id parameter
$stmt->bind_param("i", $station_id);

// Execute the statement
$stmt->execute();

// Fetch the result
$result = $stmt->get_result();
$completedOrders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $completedOrders[] = $row; // Store completed orders
    }
}
$stmt->close(); // Close the prepared statement
$conn->close(); // Close the database connection

// Return completed orders as JSON
header('Content-Type: application/json');
echo json_encode($completedOrders);
?>
