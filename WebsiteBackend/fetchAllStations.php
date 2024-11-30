<?php
require 'db.php';

$sql = "SELECT station_id, name FROM stations";
$result = $conn->query($sql);

$stations = []; // Initialize an array to hold station data

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $stations[] = $row; // Add each station to the array
    }

    // Check if stations array is empty and return appropriate JSON response
    if (empty($stations)) {
        echo json_encode(['error' => 'No stations found']);
    } else {
        echo json_encode($stations); // Return stations in JSON format
    }
} else {
    echo json_encode(['error' => 'Query error: ' . $conn->error]); // Provide detailed error info
}

$conn->close();
?>
