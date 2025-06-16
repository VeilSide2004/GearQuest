<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = trim($_POST['new_username']);
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']); // Plain text password
    $role = "user"; // Fixed role as 'user'

    // Check if username exists
    $check_sql = "SELECT id FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $new_username);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>alert('Username already exists! Choose a different one.');</script>";
    } else {
        // Insert new user into the database (STORING PLAIN PASSWORD - NOT RECOMMENDED)
        $insert_sql = "INSERT INTO users (username, email_id, password, role) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ssss", $new_username, $email, $new_password, $role);

        if ($insert_stmt->execute()) {
            // Redirect to login page after successful signup
            header("Location: login_form.php");
            exit;
        } else {
            echo "<script>alert('Error: Could not register user.');</script>";
        }

        $insert_stmt->close();
    }

    $check_stmt->close();
}

$conn->close();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/signup.css">
    <script src="assets/js/signup.js"></script>
    <title>Signup - GearQuest</title>
</head>
<body>

<div class="container">
    <!-- Left Image Section -->
    <div class="left-box">
        <img src="assets/images/signup-image.jpg" alt="Signup Image">
    </div>

    <!-- Right Signup Form -->
    <div class="right-box">
        <div class="content">
            <h1 class="center-title">GearQuest</h1>
            <p class="center-text">Create an account and start your adventure!</p>

            <!-- Signup Form -->
            <div class="signup-box"> 
                <h2>Signup</h2>
                <form method="POST">
                    <div class="input-group">
                        <input type="text" name="new_username" placeholder="Username" required>
                    </div>
                    <div class="input-group">
                        <input type="email" name="email" placeholder="Email ID" required>
                    </div>
                    <div class="input-group">
                        <input type="password" name="new_password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="signup-btn">Signup</button>
                </form>
                <p>Already have an account? <a href="login_form.php">Login here</a></p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
