<?php
// Include your database connection file which defines $conn
require_once __DIR__ . '/../../db_connect.php';

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * LEFT COLUMN: Fetch rope products from the "products" table.
 * Note: We now select the "description" field as well.
 */
$searchTerm = "%tent%";
$sql = "SELECT id, name, price, image, description
        FROM products
        WHERE name LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

/**
 * RIGHT COLUMN: Fetch from the "tents" table.
 * This query now fetches id, title, price, image_url, description, and rating.
 */
$gearStmt = $conn->query("
    SELECT id, title, price, image_url, description, rating
    FROM tents
    LIMIT 7
");

$climbingGear = [];
while ($row = $gearStmt->fetch_assoc()) {
    $climbingGear[] = $row;
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
    .product-container, .gear-container {
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
      color:rgb(0, 0, 0);
      margin: 15px 0 10px;
    }
    .card p {
      font-size: 18px;
      color:rgb(0, 0, 0);
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
      .product-container, .gear-container {
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
  <!-- LEFT COLUMN: Rope products from the "products" table -->
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
        <?php if (!empty($product['description'])): ?>
          <p><?= htmlspecialchars($product['description']) ?></p>
        <?php endif; ?>
        <!-- "Add to Cart" form -->
        <form action="/hello/cart_user/add_to_cart.php" method="POST" style="margin-top: 20px;">
          <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
          <button type="submit" class="add-to-cart">Add to Cart</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- RIGHT COLUMN: Items from the "tents" table with new columns -->
  <div class="gear-container">
    <?php foreach ($climbingGear as $gear): ?>
      <div class="card">
        <img src="<?= htmlspecialchars($gear['image_url']) ?>" alt="Climbing Gear">
        <h3><?= htmlspecialchars($gear['title']) ?></h3>
        <p>Price: ₹<?= htmlspecialchars($gear['price']) ?></p>
        <?php if (!empty($gear['description'])): ?>
          <p><?= htmlspecialchars($gear['description']) ?></p>
        <?php endif; ?>
        <?php if (!empty($gear['rating'])): ?>
          <p>Rating: <?= htmlspecialchars($gear['rating']) ?> / 5</p>
        <?php endif; ?>
        <!-- "Add to Cart" (in your code, it links to Amazon, but adapt as needed) -->
        <form action="/hello/add_to_cart.php" method="POST" style="margin-top: 20px;">
          <input type="hidden" name="product_id" value="<?= $gear['id'] ?>">
          <button type="button" class="add-to-cart" onclick="window.location.href='https://www.amazon.in/b/ref=Sports_Halo_Camping&outdoors_Dec_PC_2?pf_rd_r=A26XAAXBVSQCRSPZNHFF&pf_rd_p=20ec67e4-9490-42f3-9928-db846e097322&pf_rd_m=A1VBAL9TL5WCBF&pf_rd_s=merchandised-search-2&pf_rd_t=&pf_rd_i=1984988031&node=62320647031'">On amazon</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
</div>

</body>
</html>