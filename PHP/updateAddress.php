<?php
    require 'db.php';

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $addressId = $_POST['address_id'];
        $street = $_POST['street'];
        $building = $_POST['building'];
        $unit = $_POST['unit'];
        $city = $_POST['city'];
        $postalCode = $_POST['postal_code'];

        $stmt = $conn->prepare("UPDATE user_addresses SET street = ?, building = ?, unit = ?, city = ?, postal_code = ? WHERE address_id = ?");
        $stmt->bind_param("sssssi", $street, $building, $unit, $city, $postalCode, $addressId);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => $stmt->error]);
        }

        $stmt->close();
    }

    $conn->close();
?>
