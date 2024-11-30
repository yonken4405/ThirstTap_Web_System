    <?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    require 'vendor/autoload.php'; // Load PHPMailer dependencies
    require 'db.php'; // Include MySQLi connection

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $response = ['success' => 0, 'message' => ''];

    // Get the email from POST
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        $response['message'] = 'Email is required.';
        echo json_encode($response);
        exit;
    }

    // Log email for debugging
    file_put_contents('debug.log', "Email received: " . $email . "\n", FILE_APPEND);

    // Check if the user exists in the user_data table
    $stmt = $conn->prepare("SELECT * FROM user_data WHERE email = ?");
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

        // Update the verification code in the database
        $stmt = $conn->prepare("UPDATE user_data SET verification_code = ? WHERE email = ?");
        $stmt->bind_param("ss", $verification_code, $email);

        if ($stmt->execute()) {
            // Log successful update
            file_put_contents('debug.log', "Code updated successfully for " . $email . "\n", FILE_APPEND);

            // Send the new code via email 
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
                $mail->Subject = 'New Email Verification Code';
                $mail->Body = "Your new verification code is: <b>$verification_code</b>";
        
                // Send the email
                $mail->send();
                
            } catch (Exception $e) {
                file_put_contents('debug.log', "PHPMailer Error: " . $mail->ErrorInfo . "\n", FILE_APPEND);
            }

            $response['success'] = 1;
            $response['message'] = 'Verification code has been resent.';
        } else {
            $response['message'] = 'Failed to resend verification code. Please try again.';
            file_put_contents('debug.log', "Error updating code: " . $stmt->error . "\n", FILE_APPEND);
        }
    } else {
        $response['message'] = 'Email not found or already verified.';
    }

    echo json_encode($response);
?>
