<?php

// Name: Adam Drea
// File: Elevate Ticket
// Purpose: Web Project
// Due Date: 25th Oct 2024

session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Check if ticket ID is available in the session
if (!isset($_SESSION['ticket_id'])) {
    $_SESSION['error'] = "No ticket selected to elevate.";
    header('Location: agent_dashboard.php');
    exit();
}

// Include the database connection
include 'db_connection.php';

// Retrieve the ticket ID from the session
$ticket_id = $_SESSION['ticket_id'];

// Prepare the SQL query using prepared statements to update both the status and tier
$query = "UPDATE tickets SET status = 'Elevated', tier = 2 WHERE ticket_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $ticket_id); // "i" for integer

if (mysqli_stmt_execute($stmt)) {
    // If the update was successful, clear the ticket ID from the session
    unset($_SESSION['ticket_id']);
    $_SESSION['success'] = "Ticket elevated successfully!";
} else {
    // Handle query error
    $_SESSION['error'] = "Error elevating ticket: " . mysqli_error($conn);
}

// Close the prepared statement and the database connection
mysqli_stmt_close($stmt);
mysqli_close($conn);

// Redirect back to the agent dashboard with a session message
header('Location: agent_dashboard.php');
exit();
?>
