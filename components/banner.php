<?php
require_once __DIR__ . '/../db_connect.php';

// Fetch all banner images
$result = $conn->query("SELECT image_url FROM banners ORDER BY uploaded_at DESC");

$images = [];
while ($row = $result->fetch_assoc()) {
    $images[] = 'hello/' . ltrim(htmlspecialchars($row['image_url']), '/');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banner Slideshow</title>
    <link rel="stylesheet" href="assets/css/banner_user.css">
</head>
<body>

<div class="banner-container">
    <?php if (!empty($images)): ?>
        <img id="bannerImage" src="http://localhost/<?php echo $images[0]; ?>" alt="Banner Image">
        <a id="shopNowBtn" href="#" class="shop-now-btn">Shop Now</a> <!-- Button inside banner -->
    <?php else: ?>
        <p>No banner available.</p>
    <?php endif; ?>
</div>

<script>
    let images = <?php echo json_encode($images); ?>;
</script>
<script src="assets/js/banner_user.js"></script> 
</body>
</html>
