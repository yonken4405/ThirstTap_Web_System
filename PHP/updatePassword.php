<?php
require 'db.php';

$user_id = $_POST['user_id'];
$old_password = $_POST['old_password'];
$new_password = $_POST['new_password'];

// Check if user exists
$stmt = $conn->prepare("SELECT password FROM user_data WHERE userid = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verify old password
    if (password_verify($old_password, $user['password'])) {
        // Hash the new password before storing it
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update the password
        $update_stmt = $conn->prepare("UPDATE user_data SET password = ? WHERE userid = ?");
        $update_stmt->bind_param("ss", $hashed_new_password, $user_id);
        
        if ($update_stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update password."]);
        }
        $update_stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Old password is incorrect."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "User not found."]);
}

$stmt->close();
$conn->close();
?>
