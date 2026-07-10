<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'university_erp';

try {
    // Create connection
    $conn = new mysqli($host, $username, $password, $database);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set character set to UTF-8
    $conn->set_charset("utf8");
    
} catch (Exception $e) {
    die("Database Connection Error: " . $e->getMessage());
}
?>