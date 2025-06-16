<?php
require_once 'db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GearQuest</title>
    <link rel="stylesheet" href="/hello/assets/css/style.css"> 
    <link rel="stylesheet" href="/hello/assets/css/product_card.css">
    <link rel="stylesheet" href="/hello/assets/css/footer.css">
    <link rel="stylesheet" href="/hello/assets/css/brand.css">
    <script src="assets/js/sidebar.js"></script>
    <script src="assets/js/auto-switch.js"></script>

    <!-- Include JavaScript File -->
</head>

<body>
    <!-- Include Sidebar -->
    <?php include 'components/sidebar.php'; ?>
    <!-- Navbar -->
    <?php include 'components/navbar.php'; ?>
    <!-- Scrollable Categories with Search Bar -->
    <div class="categories">
    <div class="menu-icon">
           <div></div>
           <div></div>
           <div></div>
        </div>

    <a href="#">Home</a>
    <a href="#">Products</a>


    <ul class="menu">
        <li class="dropdown">
            <a href="#" class="dropbtn">Sale! ▼</a>
            <div class="dropdown-content">
                <div class="dropdown-row">
                    <div class="column">
                        <h3>Camping Gear</h3>
                        <a href="/hello/product/Backpack/backpack.php">Sleeping bags</a>
                        <a href="/hello/product/Tent/tent.php">Tents & Shelters</a>
                        <a href="#">bags</a>
                    </div>
                    <div class="column">
                        <h3>Climbing Gear</h3>
                        <a href="/hello/product/climbing_gear/climbing_gear.php">climbing_gear</a>
                        <a href="#">Harnesses</a>
                        <a href="/hello/product/climbing_ropes/climbing_rope.php">Ropes</a>
                    </div>
                    <div class="column">
                        <h3>Clothing</h3>
                        <a href="#">Men's Clothing</a>
                        <a href="#">Women's Clothing</a>
                    </div>
                    <div class="column">
                        <h3>Footwear</h3>
                        <a href="#">Men's Footwear</a>
                        <a href="#">Women's Footwear</a>
                    </div>
                </div>
            </div>
        </li>
    </ul>
    <a href="#">Best Sellers</a>
    <a href="cart_user/cart.php">My Order</a>
    <a href="cart_user/info.php">Info</a>
    <input type="text" placeholder="Search for products..." class="categories-search">
</div>

    <?php include 'components/banner.php'; ?>
    <?php include 'components/brand.php'; ?>
    <?php include 'components/product_card.php'; ?>

    <?php include 'components/footer.php'; ?>
</body>
</html>