<?php

// Name: Adam Drea
// File: Open A Ticket
// Purpose: Web Project
// Due Date: 25th Oct 2024

session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Invalid CSRF token!";
        header('Location: user_dashboard.php');
        exit();
    }

    // Get the user input
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $user_id = $_SESSION['user_id'];

    // Insert the ticket into the database
    $query = "INSERT INTO tickets (user_id, subject, description, status, tier, date_submitted) VALUES (?, ?, ?, 'Open', 1, NOW())";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iss", $user_id, $subject, $description);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Ticket opened successfully!";
    } else {
        $_SESSION['error'] = "Error opening ticket: " . mysqli_error($conn);
    }

    // Redirect back to the user dashboard
    header('Location: user_dashboard.php');
    exit();
}
?>
