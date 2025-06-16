<?php
require_once __DIR__ . '/../db_connect.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$upload_dir = __DIR__ . '/../product_img/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Function to display product cards
function displayProductCard($product) {
    $image_url = 'http://localhost/hello/' . htmlspecialchars($product['image']);

    echo '<div class="product-card">';
    echo '<img src="' . $image_url . '" alt="' . htmlspecialchars($product['name']) . '">';
    
    echo '<h3>' . htmlspecialchars($product['name']) . '</h3>';
    echo '<p class="price">₹' . number_format($product['price'], 2) . '</p>';
    
    echo '<p class="description">' . nl2br(htmlspecialchars($product['description'])) . '</p>';
    
    echo '<form method="POST" action="cart_user/add_to_cart.php">';
    echo '<input type="hidden" name="product_id" value="' . $product['id'] . '">';
    echo '<button type="submit" class="add-to-cart">🛒 Add to Cart</button>';
    echo '</form>';
    echo '</div>';
}

// Limit to 4 products
$query = "SELECT * FROM products LIMIT 4";
$result = $conn->query($query);

echo '<div class="product-container">';

if ($result->num_rows > 0) {
    while ($product = $result->fetch_assoc()) {
        displayProductCard($product);
    }
} else {
    echo "<p>No products available.</p>";
}

echo '</div>';

$conn->close();
?>
