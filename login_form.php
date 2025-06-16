<?php
require_once 'db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error in SQL statement: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $db_username, $db_password, $role);
        $stmt->fetch();

        // ✅ Compare passwords directly (plain text)
        if ($password === $db_password) { 
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $db_username;
            $_SESSION['role'] = $role;

            $_SESSION['admin_id'] = $id; // Set admin session variable
            header("Location: " . ($role === 'admin' ? "./admin/admin_page.php" : "user_page.php"));
            exit;
        } else {
            echo "<script>alert('Invalid username or password!');</script>";
        }
    } else {
        echo "<script>alert('User not found!');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets\css\login.css">
    <!-- <script src="assets/js/login.js"></script> -->
</head>
<body>

<div class="container">
    <div class="left-box">
        <img src="assets/Images/image.jpg" alt="Adventure Image">
    </div>
    <div class="right-box">
        <h1>GearQuest</h1>
        <p>Welcome to the Shopping mart who wishes to adventure</p>
        <div class="login-box"> 
            <form method="POST">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="login-btn">Login</button>
                <p><a href="signup_form.php">Create Account</a></p>
            </form>
        </div>
    </div>
</div>

</body>
</html>
