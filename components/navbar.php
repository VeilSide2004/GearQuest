<div class="navbar">
    <div class="logo">GearQuest</div>
    <!-- <input type="text" placeholder="Search for products..."> -->
     <div class="navbar-center-text">
        <!-- <p>Adventure Begins Here</p> -->
        <span id="dynamic-text"></span>
        <span class="cursor">|</span>
     </div>

    <div class="icons">
        <?php include 'components/profile.php'; ?> <!-- Profile before Cart -->
        
        <a href="/hello/cart_user/cart.php" class="cart">🛒 Cart</a>
        <a href="logout.php" class="logout">🚪 Logout</a>
    </div>
</div>
<script src="assets/js/navbar.js"></script>