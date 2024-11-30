<?php
    require 'db.php';

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve address ID from request
    $addressId = $_POST['address_id'];

    // Set all addresses to not default
    $conn->query("UPDATE user_addresses SET is_default = 0 WHERE userid = (SELECT userid FROM user_addresses WHERE address_id = $addressId)");

    // Set the selected address as default
    $stmt = $conn->prepare("UPDATE user_addresses SET is_default = 1 WHERE address_id = ?");
    $stmt->bind_param("i", $addressId);
    $stmt->execute();

    $stmt->close();
    $conn->close();
?>
