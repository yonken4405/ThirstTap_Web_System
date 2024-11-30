<?php
    require 'db.php'; // Include MySQLi connection

    $response = ['success' => 0, 'message' => ''];

    // Get the verification code and email from POST
    $verification_code = $_POST['verification_code'] ?? '';
    $email = $_POST['email'] ?? '';

    // Validate input
    if (empty($verification_code) || empty($email)) {
        $response['message'] = 'Both email and verification code are required.';
        echo json_encode($response);
        exit;
    }

    // Check if the verification code and email are valid in the user_data table
    $stmt = $conn->prepare("SELECT * FROM user_data WHERE email = ? AND verification_code = ?");
    $stmt->bind_param("ss", $email, $verification_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
            // Success: verification status updated
            $response['success'] = 1;
            $response['message'] = 'Email verified successfully.';
       
    } else {
        // Invalid verification code or email
        $response['message'] = 'Invalid verification code or email.';
    }

    // Return the response
    echo json_encode($response);
?>
