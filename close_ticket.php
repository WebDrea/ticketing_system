<?php

// Name: Adam Drea
// File: Close Ticket for user
// Purpose: Web Project
// Due Date: 25th Oct 2024

session_start();

// Include the database connection
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ensure ticket_id is provided and is a valid number
if (!isset($_GET['ticket_id']) || !is_numeric($_GET['ticket_id'])) {
    $_SESSION['error'] = 'Invalid ticket ID.';
    header('Location: user_dashboard.php');
    exit();
}

$ticket_id = $_GET['ticket_id'];

// Prepare the SQL query using prepared statements to avoid SQL injection
$query = "UPDATE tickets SET status = 'Closed' WHERE ticket_id = ? AND status = 'Resolved'";

if ($stmt = mysqli_prepare($conn, $query)) {
    mysqli_stmt_bind_param($stmt, 'i', $ticket_id);

    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $_SESSION['success'] = 'Ticket closed successfully!';
        } else {
            $_SESSION['error'] = 'Ticket is not in Resolved status or invalid ticket ID.';
        }
    } else {
        $_SESSION['error'] = 'Failed to close the ticket.';
    }

    mysqli_stmt_close($stmt); // Close the prepared statement
} else {
    $_SESSION['error'] = 'Error preparing the statement.';
}

// Close the database connection
mysqli_close($conn);

// Redirect back to the user dashboard
header('Location: user_dashboard.php');
exit();
?>
