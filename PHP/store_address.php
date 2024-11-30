    <?php
    header('Content-Type: application/json'); // Set the correct header

    require 'db.php';

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
        // Retrieve POST data
        $user_id = $_POST['user_id'];
        $barangay = $_POST['barangay'];
        $street = $_POST['street'];
        $building = $_POST['building'];
        $unit = $_POST['unit'];
        $house = $_POST['house'];
        $additional = $_POST['additional'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $postal_code = $_POST['postal_code'];
        $country = $_POST['country'];
        $latitude = floatval($_POST['latitude']);
        $longitude = floatval($_POST['longitude']);
        $type = $_POST['type'];
        $is_default = $_POST['is_default'];

        // Log received values for debugging
        error_log("Received latitude: $latitude, longitude: $longitude");

        // Check if latitude and longitude are valid (non-zero)
        if (empty($latitude) || empty($longitude)) {
            echo json_encode(["success" => false, "message" => "Invalid latitude or longitude"]);
            exit;
        }

        // Check if the address is in the Philippines
        if (strtolower($country) !== 'philippines') {
            echo json_encode(["success" => false, "message" => "Address must be within the Philippines"]);
            exit;
        }

        // Prepare an SQL statement
        $stmt = $conn->prepare("INSERT INTO user_addresses 
            (userid, barangay, street, building, unit, house_num, additional, city, state, postal_code, country, latitude, longitude, type, is_default) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind parameters
        $stmt->bind_param("issssssssssddsi", $user_id, $barangay, $street, $building, $unit, $house, $additional, $city, $state, $postal_code, $country, $latitude, $longitude, $type, $is_default);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Error storing address: " . $stmt->error]); // Include error message
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request"]);
    }
?>
