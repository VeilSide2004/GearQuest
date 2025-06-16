<?php
require_once __DIR__ . '/../db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to modify the cart.");
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? null;

if (!$product_id) {
    die("Invalid product.");
}

// Fetch the current quantity of the product in the cart
$query = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
$query->bind_param("ii", $user_id, $product_id);
$query->execute();
$result = $query->get_result();
$item = $result->fetch_assoc();

if ($item) {
    $new_quantity = $item['quantity'] - 1;
    if ($new_quantity > 0) {
        // Update the quantity in the cart
        $update_cart = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $update_cart->bind_param("iii", $new_quantity, $user_id, $product_id);
        $update_cart->execute();
    } else {
        // Remove the item from the cart
        $delete_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $delete_cart->bind_param("ii", $user_id, $product_id);
        $delete_cart->execute();
    }
}

// Redirect back to the cart
header("Location: cart.php");
exit;
?>
