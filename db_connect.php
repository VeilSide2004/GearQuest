<?php
session_start(); // Start session for user authentication

$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "e commerce";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
