<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', ''); // Your MySQL username
define('DB_PASS', '');     // Your MySQL password
define('DB_NAME', ''); // The database name you created

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start the session on every page that includes this file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>