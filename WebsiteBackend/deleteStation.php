<?php
require 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the station ID from POST data
$stationId = (int) $_POST['station_id'];

// Prepare and execute the delete statement
$stmt = $conn->prepare("DELETE FROM stations WHERE station_id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $stationId);

$response = [];
if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['success'] = false;
    $response['message'] = 'Execution failed: ' . $stmt->error;
}

$stmt->close();
$conn->close();

// Return JSON response
header('Content-Type: application/json'); // Ensure the correct content type is set
echo json_encode($response);
exit;
