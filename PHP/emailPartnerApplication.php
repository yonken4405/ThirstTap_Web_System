<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php'; // Adjust the path as needed

header('Content-Type: application/json'); // Set the content type to JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $company = htmlspecialchars($_POST['company']);
    $firstName = htmlspecialchars($_POST['first_name']);
    $lastName = htmlspecialchars($_POST['last_name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $mobile = htmlspecialchars($_POST['mobile']);
    $street = htmlspecialchars($_POST['street']);
    $unit = htmlspecialchars($_POST['unit']);
    $city = htmlspecialchars($_POST['city']);
    $region = htmlspecialchars($_POST['region']);
    $comments = htmlspecialchars($_POST['comments']);

    // Get base64 images from the request
    $imageBase64ID = isset($_POST['image_id']) ? $_POST['image_id'] : null;
    $imageBase64Permit = isset($_POST['image_permit']) ? $_POST['image_permit'] : null;
    $imageBase64Cert = isset($_POST['image_cert']) ? $_POST['image_cert'] : null;

    // Validate email
    if (!$email) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
        exit;
    }

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    try {
        // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'thirsttapmail@gmail.com'; // Gmail username
            $mail->Password = 'avfw ruso nkye mhrq'; // Gmail app-specific password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
            $mail->Port = 587; // TCP port to connect to

        // Recipients
        $mail->setFrom('thirsttapmail@gmail.com', 'ThirstTap');
        $mail->addAddress('thirsttapmail@gmail.com', 'Super Admin'); 

        // Content
        $mail->isHTML(true); 
        $mail->Subject = 'Refilling Station Partner Request';
        $mail->Body = "
            <h1>New Submission</h1>
            <p><strong>Company:</strong> $company</p>
            <p><strong>First Name:</strong> $firstName</p>
            <p><strong>Last Name:</strong> $lastName</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Mobile:</strong> $mobile</p>
            <p><strong>Street:</strong> $street</p>
            <p><strong>Unit:</strong> $unit</p>
            <p><strong>City:</strong> $city</p>
            <p><strong>Region:</strong> $region</p>
            <p><strong>Comments:</strong> $comments</p>
        ";

        // Attach images if available
        $imagePaths = []; // To keep track of image paths for cleanup

        function attachImage($mail, $imageBase64, $label) {
            if ($imageBase64) {
                // Decode base64 image data
                $imageBase64 = str_replace('data:image/jpeg;base64,', '', $imageBase64);
                $imageBase64 = str_replace(' ', '+', $imageBase64);
                $image = base64_decode($imageBase64);
                
                $imagePath = tempnam(sys_get_temp_dir(), $label) . '.jpg'; // Create a temporary file
                file_put_contents($imagePath, $image); // Save the image to the temporary file
                $mail->addAttachment($imagePath, "$label.jpg"); // Attach the image
                return $imagePath; // Return the path for later cleanup
            }
            return null; // No image to attach
        }

        // Attach all images and collect their paths
        $imagePaths[] = attachImage($mail, $imageBase64ID, 'ID_image');
        $imagePaths[] = attachImage($mail, $imageBase64Permit, 'Permit_image');
        $imagePaths[] = attachImage($mail, $imageBase64Cert, 'Cert_image');

        // Send the email
        $mail->send();

        // Clean up temporary image files
        foreach ($imagePaths as $imagePath) {
            if ($imagePath) {
                unlink($imagePath); // Delete temporary file
            }
        }
        
        echo json_encode(['status' => 'success', 'message' => 'Message has been sent']);
    } catch (Exception $e) {
        // Log the error message
        error_log("Mailer Error: {$mail->ErrorInfo}");
        echo json_encode(['status' => 'error', 'message' => 'Message could not be sent. Please try again later.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
