<?php
require_once(__DIR__ . '/../db_connect.php');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch total users count
$userQuery = "SELECT COUNT(*) AS total_users FROM users";
$userResult = $conn->query($userQuery);
$userRow = $userResult->fetch_assoc();
$total_users = $userRow['total_users']; // Get the total user count


// Fetch total products count
$productQuery = "SELECT COUNT(*) AS total_products FROM products";
$productResult = $conn->query($productQuery);
$productRow = $productResult->fetch_assoc();
$total_products = $productRow['total_products'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
<link rel="stylesheet" href="../assets/css/admin.
css">
<script src="../assets/js/theme_admin.js" defer></script>




</head>
<body>
    <nav>
        <h2>Admin Panel</h2>
        <a href="\hello\components_admin\users.php">User Management</a>

        <a href="\hello\components_admin\product_mg.php">Product Management</a>

        <a href="\hello\components_admin\banner_mg.php">Manage Banners</a>

        <a href="/hello/login_form.php">Logout</a>   

        <button id="dark-mode-toggle">🌙</button>

    </nav>

    <section>
    <h2>Dashboard</h2>
    <div class="dashboard-box">
        <p class="user-box">
            <img src="../assets/images/woman-raising.gif" alt="Waving User" class="wave-img">
            Total Users: <?php echo $total_users; ?>
        </p>
        <p class="product-box">
            <img src="../assets/images/empty-box.gif" alt="Product Icon" class="product-img">
            Total Products: <?php echo $total_products; ?>
        </p>
    </div>
</section>

</body>
</html>
