<?php
// Database configuration settings
$servername = 'localhost';
$username = 'root';
$password = '';
$database = 'hangar_management';
// Establish a connection to the database
$conn = new mysqli($servername, $username, $password, $database);
// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Set the timezone
date_default_timezone_set('Asia/Kolkata');
?>