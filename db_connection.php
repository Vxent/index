<?php
// Database configuration
$host = 'localhost'; // Change if your host is different
$username = 'root'; // Your database username
$password = ''; // Your database password
$database = 'capstoneloginver2'; // Your database name

// Create a connection
$db = new mysqli($host, $username, $password, $database);

// Check the connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>
