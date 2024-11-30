    <?php
    // Database connection settings
    $host = 'localhost';   // Replace with your database host
    $db   = 'u843230181_ThirstTap';    // Replace with your database name
    $user = 'u843230181_ThirstTap';         // Replace with your database username
    $pass = 'ThirstTap123';             // Replace with your database password (if applicable)
    $charset = 'utf8mb4';
    
    // Create connection using MySQLi
    $conn = new mysqli($host, $user, $pass, $db);

    // Check connection
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
?>
