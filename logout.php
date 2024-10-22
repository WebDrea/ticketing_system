
<?php

// Name: Adam Drea
// File: Logout
// Purpose: Web Project
// Due Date: 25th Oct 2024

session_start();
session_destroy();
header('Location: login.php');
exit();
?>
