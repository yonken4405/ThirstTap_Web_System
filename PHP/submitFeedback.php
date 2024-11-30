<?php
require 'db.php';

// Only process the form if it was a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    // Prepare and bind the SQL query
    $stmt = $conn->prepare("INSERT INTO feedback (userid, rating, feedback) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $rating, $feedback);

    // Execute the query
    if ($stmt->execute()) {
        echo "Feedback submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and the connection
    $stmt->close();
    $conn->close();
}
?>
