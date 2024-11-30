    <?php
    require 'db.php'; // Include MySQLi connection

    $response = ['success' => 0, 'message' => ''];

    // Get email and password from POST
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($email)) {
        $response['message'] = 'Email is required.';
        echo json_encode($response);
        exit;
    }

    if (empty($password)) {
        $response['message'] = 'Password is required.';
        echo json_encode($response);
        exit;
    }

    // Check if the email exists in the password_reset_requests table
    $stmt = $conn->prepare("SELECT * FROM password_reset_requests WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, now hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the new password

        // Update the password in the user_data table
        $stmt = $conn->prepare("UPDATE user_data SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);

        if ($stmt->execute()) {
            // Password updated successfully
            $response['success'] = 1;
            $response['message'] = 'Password reset successful.';
        } else {
            // Error during the update
            $response['message'] = 'Password reset failed. Please try again.';
        }
    } else {
        // Email not found in password_reset_requests
        $response['message'] = 'Invalid email or reset request not found.';
    }

    // Return the JSON response
    echo json_encode($response);

?>
