    <?php
    require 'db.php'; // Include MySQLi connection

    $response = ['success' => 0, 'message' => ''];

    // Get the verification code from POST
    $verification_code = $_POST['verification_code'] ?? '';
    $email = $_POST['email'] ?? '';

    // Validate input
    if (empty($verification_code)) {
        $response['message'] = 'Verification code is required.';
        echo json_encode($response);
        exit;
    }

    // Check if the verification code is valid in the password_reset_requests table
    $stmt = $conn->prepare("SELECT * FROM password_reset_requests WHERE email = ? AND verification_code = ?");
    $stmt->bind_param("ss", $email, $verification_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        //correct verification code
        $response['success'] = 1;
        $response['message'] = 'Email verication successfully.';
        
    } else {
        $response['message'] = 'Invalid verification code or email.';
    }

    echo json_encode($response);
?>
