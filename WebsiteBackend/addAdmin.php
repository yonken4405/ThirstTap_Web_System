<?php
require 'db.php';

// Get data from POST request
$station_id = $_POST['stationname']; // This now contains the station_id
$adminname = $_POST['adminname'];
$contactnum = $_POST['contactnum'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password for security
$role = $_POST['role'];

// Validate the station ID
$station_id_query = "SELECT name FROM stations WHERE station_id = ?";
$stmt = $conn->prepare($station_id_query);
$stmt->bind_param('i', $station_id); // Use 'i' for integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Station not found']);
    exit();
}

// Fetch the station name
$station_row = $result->fetch_assoc();
$station_name = $station_row['name']; // Get the station name

// Insert the new admin into the admins table
$insert_query = "INSERT INTO admins (station_id, admin_name, contact_number, email, password, role, station_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insert_query);
$stmt->bind_param('issssss', $station_id, $adminname, $contactnum, $email, $password, $role, $station_name); // Correct binding
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add admin.']);
}

$stmt->close();
$conn->close();
?>
