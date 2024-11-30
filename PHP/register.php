    <?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    require 'vendor/autoload.php'; // Load PHPMailer dependencies
    require 'db.php'; // MySQLi connection

    // Enable error reporting for debugging
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $response = ['success' => 0, 'message' => ''];

    // Sanitize inputs
    $email = strtolower(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $password = $_POST['password'] ?? '';
    $name = htmlspecialchars($_POST['name'] ?? '');
    $phone_num = htmlspecialchars($_POST['phone_num'] ?? '');

    // Validate input
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password) || empty($name) || empty($phone_num)) {
        $response['message'] = 'Invalid input. Please check your details.';
        echo json_encode($response);
        exit;
    }

    // Check if email already exists in user_data
    $stmt = $conn->prepare("SELECT email FROM user_data WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $response['message'] = 'Email already registered.';
        echo json_encode($response);
        exit;
    }

    // Generate a random verification code
    $verification_code = (string) random_int(1000, 9999); // Cast to string explicitly

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Prepare and insert the new user into user_data with "pending" verification status
    $stmt = $conn->prepare("INSERT INTO user_data (email, password, name, phone_num, verification_code, verification_status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("sssss", $email, $hashedPassword, $name, $phone_num, $verification_code);

    if ($stmt->execute()) {
        // PHPMailer setup to send the verification email
        try {
            // Create a new PHPMailer instance
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
            $mail->Subject = 'Email Verification Code';
            $mail->Body = "Your verification code is: <b>$verification_code</b>";

            // Send the email
            $mail->send();

            // Respond with success
            $response['success'] = 1;
            $response['message'] = 'Registration successful. Please verify your email.';
        } catch (Exception $e) {
            $response['message'] = 'Mail could not be sent. Error: ' . $mail->ErrorInfo;
        }
    } else {
        $response['message'] = 'Registration failed. Please try again.';
    }

    // Output response
    echo json_encode($response);
?>
