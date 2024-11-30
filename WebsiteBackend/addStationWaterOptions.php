<?php
// Database connection settings
require 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the station name from the POST request
$stationName = $_POST['stationname'];

// Prepare the statement to fetch the station_id
$fetchStationIdStmt = $conn->prepare("SELECT station_id FROM stations WHERE name = ?");
if (!$fetchStationIdStmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$fetchStationIdStmt->bind_param("s", $stationName);
$fetchStationIdStmt->execute();
$result = $fetchStationIdStmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the station ID
    $station = $result->fetch_assoc();
    $stationId = $station['station_id'];

    // Insert the fetched station_id into station_water_options
    $insertOptionStmt = $conn->prepare("INSERT INTO station_options (station_id) VALUES (?)");
    if (!$insertOptionStmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    $insertOptionStmt->bind_param("i", $stationId);
    
    if ($insertOptionStmt->execute()) {
        // Return success response
        echo json_encode(['success' => true, 'message' => 'Water options added successfully!']);
    } else {
        // Return error response if insertion fails
        echo json_encode(['success' => false, 'message' => 'Failed to add water options: ' . $insertOptionStmt->error]);
    }
} else {
    // Return error response if no station is found
    echo json_encode(['success' => false, 'message' => 'Station not found.']);
}

// Close the prepared statements and the database connection
$fetchStationIdStmt->close();
$insertOptionStmt->close();
$conn->close();
?>
