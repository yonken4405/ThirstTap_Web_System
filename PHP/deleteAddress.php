<?php
    require 'db.php';

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $addressId = $_POST['address_id'];

        $stmt = $conn->prepare("DELETE FROM user_addresses WHERE address_id = ?");
        $stmt->bind_param("i", $addressId);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => $stmt->error]);
        }

        $stmt->close();
    }

    $conn->close();
?>
