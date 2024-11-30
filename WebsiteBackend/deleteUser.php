<?php
// Include database connection
include 'db.php'; // Adjust the path as necessary

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the admin ID from the request
    $user_id = isset($_POST['userid']) ? intval($_POST['userid']) : 0;

    // Validate the admin ID
    if ($user_id > 0) {
        // Prepare the SQL statement
        $stmt = $conn->prepare("DELETE FROM user_data WHERE userid = ?");
        $stmt->bind_param("i", $user_id);

        // Execute the statement
        if ($stmt->execute()) {
            // Check if any rows were affected
            if ($stmt->affected_rows > 0) {
                // Admin deleted successfully
                echo json_encode(['success' => true]);
            } else {
                // Admin ID not found
                echo json_encode(['success' => false, 'message' => 'User not found.']);
            }
        } else {
            // Query execution failed
            echo json_encode(['success' => false, 'message' => 'Error executing query.']);
        }

        // Close the statement
        $stmt->close();
    } else {
        // Invalid admin ID
        echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
    }
} else {
    // Not a POST request
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

// Close the database connection
$conn->close();
?>
