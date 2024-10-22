<?php

// Name: Adam Drea
// File: Resolve a ticket
// Purpose: Web Project
// Due Date: 25th Oct 2024

session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Check if ticket ID is available in the session
if (!isset($_SESSION['ticket_id'])) {
    $_SESSION['error'] = "No ticket selected to resolve.";
    header('Location: agent_dashboard.php'); // Default redirection
    exit();
}

// Include the database connection
include 'db_connection.php';

// Retrieve the ticket ID from the session
$ticket_id = $_SESSION['ticket_id'];

// Prepare the SQL query using prepared statements to update the status to 'Resolved'
$query = "UPDATE tickets SET status = 'Resolved' WHERE ticket_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $ticket_id); // "i" for integer

if (mysqli_stmt_execute($stmt)) {
    // If the update was successful, clear the ticket ID from the session
    unset($_SESSION['ticket_id']);
    $_SESSION['success'] = "Ticket resolved successfully!";
} else {
    // Handle query error
    $_SESSION['error'] = "Error resolving ticket: " . mysqli_error($conn);
}

// Close the prepared statement and the database connection
mysqli_stmt_close($stmt);
mysqli_close($conn);

// Redirect based on user role/tier
if (isset($_SESSION['tier']) && $_SESSION['tier'] == 2) {
    header('Location: tier2_dashboard.php'); // Redirect to Tier 2 dashboard
} else {
    header('Location: agent_dashboard.php'); // Default to Tier 1 dashboard
}

exit();
