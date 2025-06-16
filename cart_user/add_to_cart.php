<?php
require_once __DIR__ . '/../db_connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to add items to the cart.");
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? null;

if (!$product_id) {
    die("Invalid product.");
}

// Check if the product exists
$product_query = $conn->prepare("SELECT * FROM products WHERE id = ?");
$product_query->bind_param("i", $product_id);
$product_query->execute();
$product_result = $product_query->get_result();

if ($product_result->num_rows == 0) {
    die("Product not found.");
}

$product = $product_result->fetch_assoc();

// Check if the item is already in the cart
$cart_check_query = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
$cart_check_query->bind_param("ii", $user_id, $product_id);
$cart_check_query->execute();
$cart_check_result = $cart_check_query->get_result();

if ($cart_check_result->num_rows > 0) {
    // Update quantity
    $update_cart = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
    $update_cart->bind_param("ii", $user_id, $product_id);
    $update_cart->execute();
} else {
    // Insert into cart
    $insert_cart = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $insert_cart->bind_param("ii", $user_id, $product_id);
    $insert_cart->execute();
}

// Update session cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['quantity'] += 1;
} else {
    $_SESSION['cart'][$product_id] = [
        'name' => $product['name'],
        'price' => $product['price'],
        'image' => $product['image'],
        'quantity' => 1
    ];
}

header("Location: cart.php");
exit;
?>
