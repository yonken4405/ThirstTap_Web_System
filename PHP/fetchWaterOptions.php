<?php
    // Include your database connection file
    require 'db.php';

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Get the station ID from the POST request
    $station_id = $_POST['station_id'];

    // SQL query to select all option flags for a given station_id
    $query = "SELECT 
                is_alkaline_enabled, 
                is_distilled_enabled, 
                is_round_5gal_enabled, 
                is_slim_5gal_enabled, 
                is_slim_2_5gal_enabled, 
                is_new_container_enabled, 
                is_exchange_container_enabled 
              FROM station_options 
              WHERE station_id = ?";

    // Prepare the SQL statement
    $stmt = $conn->prepare($query);
    
    // Bind the station_id to the query
    $stmt->bind_param("i", $station_id);
    
    // Execute the statement
    $stmt->execute();
    
    // Get the result set from the executed query
    $result = $stmt->get_result();

    // Initialize an array to hold the options
    $options = array();

    // Fetch the result (assuming one row since station_id is unique)
    if ($row = $result->fetch_assoc()) {
        // Check each option and add it to the array if enabled
        if ($row['is_alkaline_enabled']) {
            $options[] = 'Alkaline Water';
        }
        if ($row['is_distilled_enabled']) {
            $options[] = 'Distilled Water';
        }
        if ($row['is_round_5gal_enabled']) {
            $options[] = 'Round 5 Gallon';
        }
        if ($row['is_slim_5gal_enabled']) {
            $options[] = 'Slim 5 Gallon';
        }
        if ($row['is_slim_2_5gal_enabled']) {
            $options[] = 'Slim 2.5 Gallon';
        }
        if ($row['is_new_container_enabled']) {
            $options[] = 'New Container';
        }
        if ($row['is_exchange_container_enabled']) {
            $options[] = 'Exchange Container';
        }
    }

    // Encode the options array as JSON and output it
    echo json_encode($options);
?>
