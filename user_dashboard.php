<?php

// Name: Adam Drea
// File: Standard User Dashboard
// Purpose: Web Project
// Due Date: 25th Oct 2024

session_start();

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit();
}

// Include the database connection
include 'db_connection.php';

// Fetch user's tickets from the database using prepared statements
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM tickets WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Generate CSRF token for ticket operations
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h2, h3 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        a {
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .logout {
            text-align: right;
        }
        .logout a {
            background-color: #dc3545;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
        }
        .logout a:hover {
            background-color: #c82333;
        }
        #searchBar {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
    <script>
        // JavaScript function to filter tickets
        function filterTickets() {
            const searchInput = document.getElementById('searchBar').value.toLowerCase();
            const ticketRows = document.getElementById('ticketTable').getElementsByTagName('tr');
            
            for (let i = 1; i < ticketRows.length; i++) { // start from 1 to skip the table header
                let cells = ticketRows[i].getElementsByTagName('td');
                let matchFound = false;
                
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j]) {
                        const cellText = cells[j].innerText.toLowerCase();
                        if (cellText.indexOf(searchInput) > -1) {
                            matchFound = true;
                            break;
                        }
                    }
                }

                // Show or hide the row based on the match
                ticketRows[i].style.display = matchFound ? '' : 'none';
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        <h3>Your Tickets</h3>

        <!-- Search Bar -->
        <input type="text" id="searchBar" onkeyup="filterTickets()" placeholder="Search your tickets...">

        <table id="ticketTable">
            <tr>
                <th>Ticket ID</th>
                <th>Subject</th>
                <th>Description</th>
                <th>Status</th>
                <th>Date Submitted</th>
                <th>Close Ticket</th>
            </tr>
            <?php
            while ($ticket = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($ticket['ticket_id']) . "</td>";
                echo "<td>" . htmlspecialchars($ticket['subject']) . "</td>";
                echo "<td>" . htmlspecialchars($ticket['description']) . "</td>";
                echo "<td>" . htmlspecialchars($ticket['status']) . "</td>";
                echo "<td>" . htmlspecialchars($ticket['date_submitted']) . "</td>";
                
                if ($ticket['status'] == 'Resolved') {
                    echo "<td><a href='close_ticket.php?ticket_id=" . $ticket['ticket_id'] . "&csrf_token=" . $_SESSION['csrf_token'] . "' onclick='return confirm(\"Are you sure you want to close this ticket?\");'>Close</a></td>";
                } else {
                    echo "<td>-</td>";
                }
                echo "</tr>";
            }
            ?>
        </table>

        <h3>Open a New Ticket</h3>
        <form action="open_ticket.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <label for="subject">Subject:</label>
            <input type="text" name="subject" id="subject" required><br>
            <label for="description">Description:</label>
            <textarea name="description" id="description" required></textarea><br>
            <input type="submit" value="Submit Ticket">
        </form>
    </div>
</body>
</html>
