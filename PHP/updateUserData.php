<?php
    require 'db.php';
    
    $email = $_POST['email']; // Original email (for WHERE clause)
    $new_email = $_POST['new_email']; // New email to update
    $user_name = $_POST['new_name'];
    $phoneNum = $_POST['new_number'];
    
    $stmt = $conn->prepare("UPDATE user_data SET name = ?, email = ?, phone_num = ? WHERE email = ?");
    $stmt->bind_param("ssss", $user_name, $new_email, $phoneNum, $email); // Bind new_email and original email separately
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }

    $stmt->close();
?>
