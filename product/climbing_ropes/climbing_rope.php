<?php
// Include your database connection file which defines $conn
require_once __DIR__ . '/../../db_connect.php';

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * LEFT COLUMN: Fetch rope products from the "products" table
 */
$searchTerm = "%rope%";
$sql = "SELECT id, name, price, image
        FROM products
        WHERE name LIKE ?
           OR description LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

/**
 * RIGHT COLUMN: Fetch from the "climbing_ropes" table
 * (assuming `id`, `title`, `price`, `image_url` exist)
 */
$ropeStmt = $conn->query("SELECT id, title, price, image_url FROM climbing_ropes limit 4");
$climbingRopes = [];
while ($row = $ropeStmt->fetch_assoc()) {
    $climbingRopes[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Products Display with Persistent Dropdown</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/hello/assets/css/style.css"> 
  <style>

    /************************************
     * PRODUCT CARDS (existing styles)
     ************************************/
    .container {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
      gap: 20px;
    }
    .product-container, .rope-container {
      width: 45%;
      display: grid;
      grid-template-columns: 1fr;
      gap: 20px;
    }
    .card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15);
      overflow: hidden;
      padding: 20px;
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.25);
    }
    .card img {
      width: 100%;
      height: auto;
      object-fit: contain;
      border-radius: 8px;
      margin-bottom: 15px;
      transition: transform 0.3s ease-in-out;
    }
    .card img:hover {
      transform: scale(1.1);
    }
    .card h3 {
      font-size: 22px;
      color: #4B0082;
      margin: 15px 0 10px;
    }
    .card p {
      font-size: 18px;
      color: #6A5ACD;
      font-weight: bold;
      margin: 10px 0;
    }
    .add-to-cart {
      background: #333;
      color: #fff;
      border: none;
      padding: 12px 20px;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
      margin-top: 20px;
      text-transform: uppercase;
    }
    .add-to-cart:hover {
      background: #4B0082;
    }
    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        align-items: center;
      }
      .product-container, .rope-container {
        width: 90%;
      }
    }
    @media (max-width: 480px) {
      .card h3 {
        font-size: 18px;
      }
      .card p {
        font-size: 16px;
      }
      .add-to-cart {
        font-size: 14px;
        padding: 10px;
      }
      .card img {
        object-fit: contain;
      }
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
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
<!-- END NAVBAR -->

<!-- MAIN CONTENT (PRODUCT CARDS) -->
<div class="container">
  <!-- LEFT COLUMN: Rope products from "products" table -->
  <div class="product-container">
    <?php foreach ($products as $product): ?>
      <?php 
        // Build the full URL for the product image
        $product_image_url = '/hello/' . htmlspecialchars($product['image']);
      ?>
      <div class="card">
        <img src="<?= $product_image_url ?>" alt="Product Image">
        <h3><?= htmlspecialchars($product['name']) ?></h3>
        <p>Price: ₹<?= htmlspecialchars($product['price']) ?></p>
        <!-- "Add to Cart" form -->
        <form action="/hello/cart_user/add_to_cart.php" method="POST" style="margin-top: 20px;">
          <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
          <button type="submit" class="add-to-cart">Add to Cart</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- RIGHT COLUMN: Items from "climbing_ropes" table -->
  <div class="rope-container">
    <?php foreach ($climbingRopes as $rope): ?>
      <div class="card">
        <img src="<?= htmlspecialchars($rope['image_url']) ?>" alt="Climbing Rope">
        <h3><?= htmlspecialchars($rope['title']) ?></h3>
        <p>Price: ₹<?= htmlspecialchars($rope['price']) ?></p>
        <!-- "Add to Cart" form -->
        <form action="/hello/add_to_cart.php" method="POST" style="margin-top: 20px;">
          <input type="hidden" name="product_id" value="<?= $rope['id'] ?>">
          <button type="submit" class="add-to-cart">Add to Cart</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
</div>

</body>
</html>

