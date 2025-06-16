<?php
require_once __DIR__ . '/../db_connect.php'; // Using the existing database connection

// Ensure request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_POST['user_id']);  // Ensure integer values
    $product_id = intval($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1; // Default quantity = 1

    // Check if product already exists in the cart
    $check_query = "SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If product exists, update quantity
        $update_query = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
    } else {
        // Insert new product into cart
        $insert_query = "INSERT INTO cart (user_id, product_id, quantity, added_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    }

    // Execute query
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Product added to cart"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>

<form id="addToCartForm">
    <input type="hidden" name="user_id" value="1">  <!-- Example User ID -->
    <input type="hidden" name="product_id" value="101"> <!-- Example Product ID -->
    <input type="number" name="quantity" value="1" min="1">
    <button type="submit">Add to Cart</button>
</form>

<script>
document.getElementById("addToCartForm").addEventListener("submit", function(event) {
    event.preventDefault();
    let formData = new FormData(this);

    fetch("add_to_cart.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => alert(data.message)) // Show success or error message
    .catch(error => console.error("Error:", error));
});
</script>
