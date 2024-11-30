<?php
require 'db.php'; // Include your database connection file

// Fetching admin details
$query = "SELECT admin_id, admin_name, contact_number, email, station_name FROM admins"; // Selecting required fields

$result = $conn->query($query);

// Prepare an array to hold the admin data
$admins = [];

if ($result->num_rows > 0) {
    // Fetch all results into the $admins array
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }
}

// Close the database connection
$conn->close();

// Return the admin data as a JSON object
header('Content-Type: application/json'); // Set header for JSON response
echo json_encode($admins);
?>
