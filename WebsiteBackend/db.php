
<?php
// db.php
$host = 'localhost';
$db = 'thirsttap'; // Your database name
$user = 'root'; // MySQL username (default is root for XAMPP)
$pass = ''; // MySQL password (leave empty if no password for XAMPP)

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>