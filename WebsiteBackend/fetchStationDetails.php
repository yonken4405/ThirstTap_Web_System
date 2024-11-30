<?php
require 'db.php';

if (isset($_GET['station_id'])) {
    $station_id = intval($_GET['station_id']); // Ensure the station_id is an integer

    // Prepare and execute SQL query
    $sql = "SELECT * FROM stations WHERE station_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $station_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the station details
        $station = $result->fetch_assoc();
        echo json_encode($station);
    } else {
        echo json_encode(['error' => 'Station not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Station ID not provided']);
}

$conn->close();
?>
