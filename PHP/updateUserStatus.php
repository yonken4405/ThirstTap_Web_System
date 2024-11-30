<?php
header('Content-Type: application/json');
require 'db.php'; // Include MySQLi connection

// Get POST data
$email = $_POST['email'] ?? '';

if (empty($email)) {
    echo json_encode(["success" => 0, "message" => "Email is required."]);
    exit;
}

// Update is_new_user field to 0 for the provided email
$stmt = $conn->prepare("UPDATE user_data SET is_new_user = 0 WHERE email = ?");
$stmt->bind_param("s", $email);

if ($stmt->execute()) {
    echo json_encode(["success" => 1, "message" => "User status updated to returning user."]);
} else {
    echo json_encode(["success" => 0, "message" => "Failed to update user status."]);
}

?>
