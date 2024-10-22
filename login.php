<?php

// Name: Adam Drea
// File: Login
// Purpose: Web Project
// Due Date: 25th Oct 2024

session_start();
include 'db_connection.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $username = trim($_POST['username']);
    $password = $_POST['password']; // Plain password for hashing

    // Check if the fields are empty
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Both fields are required!';
    } else {
        // Query the database to fetch user data based on the username
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        // If the user exists, verify the password
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {
                // Password matches, start the session and store user data
                $_SESSION['user_id'] = $user['user_id']; // Store the user ID in the session
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['tier'] = isset($user['tier']) ? $user['tier'] : null; // This is for agents only

                // Redirect based on user role
                if ($user['role'] == 'user') {
                    header('Location: user_dashboard.php');
                } else if ($user['role'] == 'agent') {
                    if ($user['tier'] == '2') {
                        header('Location: tier2_dashboard.php');
                    } else {
                        header('Location: agent_dashboard.php');
                    }
                }
                exit();
            } else {
                $_SESSION['error'] = 'Incorrect password!';
            }
        } else {
            $_SESSION['error'] = 'No such user found!';
        }
    }

    // Redirect back to the login form if there was an error
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* Global CSS for consistency across pages */
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
            width: 80%;
            max-width: 400px;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 14px;
            color: #555;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            background-color: green;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: darkgreen;
        }

        .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        p {
            font-size: 14px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>

        <!-- Display success or error messages -->
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p class="alert">' . htmlspecialchars($_SESSION['error']) . '</p>';
            unset($_SESSION['error']);
        }
        ?>

        <form action="login.php" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit">Login</button>
        </form>

        <p>Not registered yet? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
