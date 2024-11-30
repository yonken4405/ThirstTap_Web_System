<?php
header('Content-Type: application/json');

require 'db.php';
// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

$admin_id = isset($_GET['admin_id']) ? intval($_GET['admin_id']) : 0;

if ($admin_id > 0) {
    $stmt = $conn->prepare("SELECT admin_id, station_name, admin_name, contact_number, email FROM admins WHERE admin_id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        echo json_encode($admin);
    } else {
        echo json_encode(null); // No admin found
    }

    $stmt->close();
} else {
    echo json_encode(null); // Invalid ID
}

$conn->close();
?>
