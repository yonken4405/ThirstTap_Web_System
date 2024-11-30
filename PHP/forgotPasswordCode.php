    <?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    require 'vendor/autoload.php'; // Load PHPMailer dependencies
    require 'db.php'; // Ensure 'db.php' path is correct

    $response = ['success' => 0, 'message' => ''];
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        $response['message'] = 'Email is required.';
        echo json_encode($response);
        exit;
    }

    // Check if the user exists in the user_data table
    $stmt = $conn->prepare("SELECT email FROM user_data WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $lastSent = strtotime($row['registered_at']);
        if (time() - $lastSent < 60) {
            $response['message'] = 'Please wait before resending the code.';
            echo json_encode($response);
            exit;
        }
    }

    if ($result->num_rows > 0) {
        // Generate a new verification code
        $verification_code = rand(1000, 9999); // Generate a random 4-digit code

        // Upload user email and verification code to the database
        $stmt = $conn->prepare("INSERT INTO password_reset_requests (email, verification_code) VALUES (?, ?)");
        if (!$stmt) {
            file_put_contents('debug.log', "Prepare failed: " . $conn->error . "\n", FILE_APPEND);
            $response['message'] = 'Database prepare error.';
            echo json_encode($response);
            exit;
        }
        $stmt->bind_param("ss", $email, $verification_code);

        if ($stmt->execute()) {
            // Log successful update
            file_put_contents('debug.log', "Code sent successfully for " . $email . "\n", FILE_APPEND);

            // Send the code via email
            try {
                $mail = new PHPMailer(true);

                // SMTP configuration
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
                $mail->SMTPAuth = true;
                $mail->Username = 'thirsttapmail@gmail.com'; // Gmail username
                $mail->Password = 'avfw ruso nkye mhrq'; // Gmail app-specific password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
                $mail->Port = 587; // TCP port to connect to

                // Email settings
                $mail->setFrom('thirsttapmail@gmail.com', 'ThirstTap'); // Sender's email and name
                $mail->addAddress($email); // Add recipient's email

                // Email content
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Password Reset Verification Code';
                $mail->Body = "Your verification code is: <b>$verification_code</b>";

                // Send the email
                $mail->send();
                
                $response['success'] = 1;
                $response['message'] = 'Verification code has been sent.';
            } catch (Exception $e) {
                file_put_contents('debug.log', "PHPMailer Error: " . $mail->ErrorInfo . "\n", FILE_APPEND);
                $response['message'] = 'Failed to send email.';
            }
        } else {
            file_put_contents('debug.log', "Error sending code: " . $stmt->error . "\n", FILE_APPEND);
            $response['message'] = 'Failed to send verification code. Please try again.';
        }
    } else {
        $response['message'] = 'Email not found.';
        echo json_encode($response);
        exit;
    }

    echo json_encode($response);
?>
