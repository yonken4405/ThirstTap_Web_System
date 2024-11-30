<?php
    require 'db.php'; // Your database connection file

    // Check if userId is provided in the request 
    if (isset($_GET['userId'])) {
        $userId = $_GET['userId']; // Fetch userId from the request
    } else {
        die("userId parameter is missing");
    }

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $stmt = $conn->prepare("SELECT * FROM user_addresses WHERE userid = ?");
    $stmt->bind_param("s", $userId); 

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    $addresses = array();

    while ($row = $result->fetch_assoc()) {
        $addresses[] = $row;
    }

    // Return the data as JSON
    header('Content-Type: application/json');
    echo json_encode($addresses);
    
    $stmt->close();
    $conn->close(); // Close the database connection
?>
