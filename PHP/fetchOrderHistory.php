<?php
include 'db.php';  // Connection to the database

// Fetch parameters from the URL
$status = $_GET['status'];  
$userid = $_GET['userid'];  
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'asc'; // Default sort to ascending if not provided

// Validate sort parameter
if ($sort !== 'asc' && $sort !== 'desc') {
    $sort = 'asc'; // Default to ascending if invalid value is given
}

// Prepare SQL statement
$sql = "SELECT orders.orderid, orders.total_price, orders.delivery_date, orders.delivery_address, user_data.name 
        FROM orders 
        INNER JOIN user_data ON orders.userid = user_data.userid 
        WHERE orders.status = ? AND orders.userid = ? 
        ORDER BY orders.delivery_date $sort"; // Add ORDER BY clause based on sort parameter

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $status, $userid); // Bind the status and userid
$stmt->execute();
$result = $stmt->get_result();

$orders = array();
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode($orders);
?>
