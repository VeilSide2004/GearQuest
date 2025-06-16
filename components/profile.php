<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
$initial = strtoupper(substr($username, 0, 1)); // Get first letter
?>
<div class="profile-container">
    <div class="profile-icon" title="<?php echo htmlspecialchars($username); ?>">
        <?php echo htmlspecialchars($initial); ?>
    </div>
</div>