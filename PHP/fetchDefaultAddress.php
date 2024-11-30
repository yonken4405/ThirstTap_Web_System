<?php
    require 'db.php';
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $user_id = $_POST['user_id']; // Assuming you're passing user_id

    // Query to get the default address
    $query = "SELECT barangay, street, building, unit, house_num, additional, city, state, postal_code, latitude, longitude, address_id 
              FROM user_addresses 
              WHERE is_default = 1 AND userid = ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $address = $result->fetch_assoc();
        echo json_encode($address);
    } else {
        echo json_encode(["error" => "No default address found"]);
    }
?>
