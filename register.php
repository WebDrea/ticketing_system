
<body><?php

// Name: Adam Drea
// File: Register a User/Agent
// Purpose: Web Project
// Due Date: 25th Oct 2024

session_start();
include 'db_connection.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data and sanitize it
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Plain password for hashing
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']); // 'user' or 'agent'
    
    // If the role is 'agent', get the tier (1 or 2)
    $tier = ($role === 'agent') ? mysqli_real_escape_string($conn, $_POST['tier']) : null;

    // Check if any fields are empty
    if (empty($username) || empty($password) || empty($full_name) || empty($role)) {
        $_SESSION['error'] = 'All fields are required!';
    } else {
        // Check if the username already exists
        $check_query = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $_SESSION['error'] = 'Username already exists!';
        } else {
            // Hash the password before storing it
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user or agent into the 'users' table
            $query = "INSERT INTO users (username, password, full_name, role, tier) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssss", $username, $hashed_password, $full_name, $role, $tier);

            // Execute the query and check for success
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = 'Registration successful! You can now login.';
                header('Location: register.php');
                exit();
            } else {
                $_SESSION['error'] = 'Database error: Could not register.';
            }
        }
    }

    // Redirect back to the registration form
    header('Location: register.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User/Agent Registration</title>
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

        input, select {
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

        .alert-success {
            background-color: #d4edda;
            color: #155724;
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

        /* Additional Styling for the role selection */
        select {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>

        <!-- Display success or error messages -->
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p class="alert">' . htmlspecialchars($_SESSION['error']) . '</p>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<p class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</p>';
            unset($_SESSION['success']);
        }
        ?>

        <form action="register.php" method="post" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" name="full_name" id="full_name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required>
            </div>

            <!-- Role Selection: User or Agent -->
            <div class="form-group">
                <label for="role">Register as:</label>
                <select name="role" id="role" required onchange="toggleTier()">
                    <option value="user" <?php if (isset($_POST['role']) && $_POST['role'] == 'user') echo 'selected'; ?>>User</option>
                    <option value="agent" <?php if (isset($_POST['role']) && $_POST['role'] == 'agent') echo 'selected'; ?>>Support Agent</option>
                </select>
            </div>

            <!-- Tier Selection for Agents (hidden by default, shown when 'Agent' is selected) -->
            <div class="form-group" id="tier-group" style="display: none;">
                <label for="tier">Select Tier (For Agents):</label>
                <select name="tier" id="tier">
                    <option value="1" <?php if (isset($_POST['tier']) && $_POST['tier'] == '1') echo 'selected'; ?>>Tier 1</option>
                    <option value="2" <?php if (isset($_POST['tier']) && $_POST['tier'] == '2') echo 'selected'; ?>>Tier 2</option>
                </select>
            </div>

            <button type="submit">Register</button>
        </form>

        <p>Already registered? <a href="login.php">Login here</a></p>

    </div>

    <script>
        // Form validation function
        function validateForm() {
            let username = document.getElementById('username').value;
            let password = document.getElementById('password').value;
            let fullName = document.getElementById('full_name').value;

            if (username === "" || password === "" || fullName === "") {
                alert("All fields are required.");
                return false;
            }

            if (password.length < 6) {
                alert("Password must be at least 6 characters long.");
                return false;
            }

            return true;
        }

        // Toggle tier selection based on role
        function toggleTier() {
            let role = document.getElementById('role').value;
            let tierGroup = document.getElementById('tier-group');
            
            if (role === 'agent') {
                tierGroup.style.display = 'block';
            } else {
                tierGroup.style.display = 'none';
            }
        }

        // Trigger the toggle when the page loads if form was partially filled
        toggleTier();
    </script>
</body>
</html>

    