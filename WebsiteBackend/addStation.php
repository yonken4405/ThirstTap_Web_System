<?php
require 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get POST data
$stationName = $_POST['stationname'];
$address = $_POST['address'];
$latitude = (float) $_POST['latitude'];
$longitude = (float) $_POST['longitude'];
$contactNum = $_POST['contactnum'];
$email = $_POST['email'];
$openHrs = $_POST['openhrs'];

// Check for required fields
if (empty($stationName) || empty($address) || empty($contactNum) || empty($email) || empty($openHrs)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Prepare and execute the statement
$stmt = $conn->prepare("INSERT INTO stations (name, station_address, latitude, longitude, contact_number, email, opening_hours) VALUES (?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ssddsss", $stationName, $address, $latitude, $longitude, $contactNum, $email, $openHrs);

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
