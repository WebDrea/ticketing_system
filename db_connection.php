<?php

// Name: Adam Drea
// File: Database Connection
// Purpose: Web Project
// Due Date: 25th Oct 2024

// Database connection details
$servername = "localhost"; // Host
$username = "root";        // Database username
$password = "";            // Database password
$dbname = "ticketing_system"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
