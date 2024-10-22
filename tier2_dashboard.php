<?php

// Name: Adam Drea
// File: Tier 2 Agent Dashboard
// Purpose: Web Project
// Due Date: 25th Oct 2024

session_start();
include 'db_connection.php'; // Include database connection file

// Check if the agent is logged in and is a Tier 2 agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'agent' || $_SESSION['tier'] != '2') {
    header('Location: login.php');
    exit();
}

// Handle Resolve Ticket
if (isset($_POST['resolve_ticket'])) {
    $_SESSION['ticket_id'] = $_POST['ticket_id'];
    header('Location: resolve_ticket.php');
    exit();
}


// Fetch elevated tickets (i.e., tickets with tier 2 and status 'Open' or 'Elevated') from the database
$query = "SELECT * FROM tickets WHERE tier = 2 AND (status = 'Open' OR status = 'Elevated')";
$result = mysqli_query($conn, $query);

if (!$result) {
    // Handle query error
    die("Error fetching elevated tickets: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tier 2 Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            width: 90%;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2, h3 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        a {
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            color: white;
        }

        .resolve-btn {
            background-color: green;
            margin-top: 20px;
            padding: 10px 20px;
        }

        .logout-btn {
            background-color: red;
            margin-top: 20px;
            padding: 10px 20px;
        }

        /* Search bar styling */
        #searchBar {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .logout-container {
            text-align: center;
            margin-top: 20px;
        }
    </style>
    <script>
        // JavaScript function to filter tickets
        function filterTickets() {
            const searchInput = document.getElementById('searchBar').value.toLowerCase();
            const ticketRows = document.getElementById('ticketTable').getElementsByTagName('tr');

            for (let i = 1; i < ticketRows.length; i++) { // Skip header row
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
        <h2>Tier 2 Agent Dashboard</h2>
        <h3>Elevated Tickets</h3>

        <!-- Search Bar -->
        <input type="text" id="searchBar" onkeyup="filterTickets()" placeholder="Search tickets...">

        <table id="ticketTable">
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0) 
                { 
                    while ($ticket = mysqli_fetch_assoc($result)) { 
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($ticket['ticket_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($ticket['subject']) . "</td>";
                        echo "<td>" . htmlspecialchars($ticket['description']) . "</td>";
                        echo "<td>" . htmlspecialchars($ticket['status']) . "</td>";
                        echo "<td>" . htmlspecialchars($ticket['date_submitted']) . "</td>";
                        
                        // Resolve button
                        echo "<td>";
                        echo "<form action='' method='post'>";
                        echo "<input type='hidden' name='ticket_id' value='" . htmlspecialchars($ticket['ticket_id']) . "'>";
                        echo "<button type='submit' class='resolve-btn' name='resolve_ticket'>Resolve</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    } 
                 } 
                 else 
                 { 
                    echo "<tr><td colspan='6'>No elevated tickets found.</td></tr>";
                 } ?>
            </tbody>
        </table>

        <!-- Logout Button -->
        <p class="logout-container">
            <a href="logout.php" class="logout-btn">Logout</a>
        </p>
    </div>
</body>
</html>