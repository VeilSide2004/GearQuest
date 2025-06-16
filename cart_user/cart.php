<?php
require_once __DIR__ . '/../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view the cart.");
}

$user_id = $_SESSION['user_id'];

// 1. Fetch cart items from database
$cart_query = $conn->prepare("
    SELECT cart.product_id, cart.quantity, products.name, products.price, products.image
    FROM cart
    JOIN products ON cart.product_id = products.id
    WHERE cart.user_id = ?
");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_result = $cart_query->get_result();

$cart_items = [];
$total = 0;

while ($row = $cart_result->fetch_assoc()) {
    $cart_items[$row['product_id']] = $row;
    $total += $row['price'] * $row['quantity'];
}

// 2. If cart is empty, redirect to the "Empty Cart" page
if (empty($cart_items)) {
    header("Location: /hello/components/error_u/Empty Cart.php");
    exit;
}

// 3. Fetch user's saved address from user_addresses table
$addr_query = $conn->prepare("SELECT * FROM user_addresses WHERE user_id = ? LIMIT 1");
$addr_query->bind_param("i", $user_id);
$addr_query->execute();
$addr_result = $addr_query->get_result();
$user_address = $addr_result->fetch_assoc();

// 4. Define extra charges AFTER total is known
$delivery_charge = 30.00;     // Default delivery charge
$convenience_charge = 20.00;  // Default convenience charge
$total_with_charges = $total + $delivery_charge + $convenience_charge;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Shipping</title>
    <!-- Link to your external CSS -->
    <link rel="stylesheet" href="/hello/assets/css/cart.css"> 
    <style>
        /* Minimal styling overrides (for demo) */
        body { font-family: Arial, sans-serif; }
        .top-nav { text-align: right; padding: 10px; }
        .top-nav a { font-size: 16px; color: #3498db; text-decoration: none; }
        .top-nav a:hover { text-decoration: underline; }
        .cart-container { max-width: 800px; margin: auto; }
        .cart-item { display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid #ddd; }
        img { width: 50px; height: 50px; object-fit: cover; }
        .quantity-controls { display: flex; align-items: center; }
        .quantity-controls form { margin: 0 5px; }
        .quantity-controls button { padding: 5px 10px; }

        /* Steps indicator */
        .checkout-steps ul {
            display: flex;
            list-style: none;
            justify-content: center;
            margin-bottom: 20px;
            padding: 0;
        }
        .checkout-steps li {
            padding: 10px 20px;
            margin: 0 5px;
            border-radius: 20px;
            background-color: #e0e0e0;
            color: #777;
            font-weight: bold;
        }
        .checkout-steps li.completed,
        .checkout-steps li.active {
            background-color: #3498db;
            color: #fff;
        }

        /* Two-column layout container */
        .checkout-page {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            gap: 30px;
        }
        .shipping-section, .cart-summary {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            flex: 1;
        }
        .cart-summary {
            max-width: 400px;
        }
        .shipping-header h2 {
            margin-bottom: 10px;
        }
        .shipping-address {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .shipping-address h3 {
            margin-bottom: 10px;
        }
        .shipping-address p {
            line-height: 1.5;
        }
        .shipping-methods {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .shipping-methods h3 {
            margin-bottom: 10px;
        }
        .method-item {
            display: block;
            margin-bottom: 8px;
        }
        .shipping-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .continue-btn {
            background-color: #3498db;
            color: #fff;
            padding: 10px 15px;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .continue-btn:hover {
            background-color: #2980b9;
        }

        .cart-summary h3 {
            margin-bottom: 20px;
        }
        .cart-item-info h4 {
            margin-bottom: 5px;
        }
        .promo-code {
            display: flex;
            margin: 15px 0;
        }
        .promo-code input {
            flex: 1;
            padding: 8px;
        }
        .promo-code button {
            background-color: #27ae60;
            color: #fff;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
        }
        .totals p {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .payment-container h2 {
            margin: 20px 0 10px;
        }
        .payment-methods {
            display: flex;
            gap: 10px;
        }
        .payment-method {
            flex: 1;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            cursor: pointer;
        }
        @media (max-width: 900px) {
            .checkout-page {
                flex-direction: column;
            }
            .cart-summary {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- Top Navigation with Home Option -->
<div class="top-nav">
    <a href="/hello/user_page.php">Home</a>
</div>

<!-- Progress Steps (1: Info, 2: Shipping, 3: Payment) -->
<div class="checkout-steps">
    <ul>
        <li class="completed">1 Information</li>
        <li class="active">2 Shipping</li>
        <li>3 Payment</li>
    </ul>
</div>

<div class="checkout-page">
    <!-- LEFT COLUMN: Shipping Section -->
    <div class="shipping-section">
        <div class="shipping-header">
            <h2>Shipping Method</h2>
        </div>

        <!-- Display user's shipping address from the database -->
        <div class="shipping-address">
            <h3>Shipping Address</h3>
            <?php if ($user_address): ?>
                <p>
                    <?php echo htmlspecialchars($user_address['full_name']); ?><br>
                    <?php echo htmlspecialchars($user_address['email']); ?><br>
                    <?php echo htmlspecialchars($user_address['phone']); ?><br>
                    <?php echo htmlspecialchars($user_address['address_line1']); ?>
                    <?php if (!empty($user_address['address_line2'])): ?>
                        , <?php echo htmlspecialchars($user_address['address_line2']); ?>
                    <?php endif; ?><br>
                    <?php echo htmlspecialchars($user_address['city']); ?>,
                    <?php echo htmlspecialchars($user_address['state']); ?>,
                    <?php echo htmlspecialchars($user_address['postal_code']); ?><br>
                    <?php echo htmlspecialchars($user_address['country']); ?>
                </p>
            <?php else: ?>
                <p>No address found. Please <a href="info.php">add your address</a>.</p>
            <?php endif; ?>
            <a href="info.php">Change</a>
        </div>

        <!-- Example: Radio buttons for shipping methods -->
        <div class="shipping-methods">
            <h3>Shipping Method</h3>
            <label class="method-item">
                <input type="radio" name="shipping_method" value="ground" checked />
                Ground Shipping: $10.00
            </label>
            <label class="method-item">
                <input type="radio" name="shipping_method" value="overnight" />
                Overnight Shipping: $20.00
            </label>
            <label class="method-item">
                <input type="radio" name="shipping_method" value="free" />
                Free Shipping
            </label>
        </div>

        <!-- Actions -->
        <div class="shipping-actions">
            <a href="info.php">&larr; Return to information</a>
            <a href="checkout.php" class="continue-btn">Continue to payment</a>
        </div>
    </div>

    <!-- RIGHT COLUMN: Cart Summary -->
    <div class="cart-summary">
        <h3>Your Cart</h3>

        <!-- Loop through cart items -->
        <?php foreach ($cart_items as $id => $product) : ?>
            <div class="cart-item">
                <img src="<?php echo '/hello/' . htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" />
                <div class="cart-item-info">
                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                    <p>Price: ₹<?php echo number_format($product['price'], 2); ?></p>

                    <div class="quantity-controls">
                        <form method="POST" action="update_cart.php">
                            <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                            <input type="hidden" name="action" value="decrease">
                            <button type="submit">-</button>
                        </form>

                        <span><?php echo $product['quantity']; ?></span>

                        <form method="POST" action="update_cart.php">
                            <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                            <input type="hidden" name="action" value="increase">
                            <button type="submit">+</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Promo Code Input (Optional) -->
        <div class="promo-code">
            <input type="text" placeholder="Enter Promo Code" />
            <button type="button">Apply</button>
        </div>

        <!-- Totals -->
        <div class="totals">
            <p>Product Price <strong>₹<?php echo number_format($total, 2); ?></strong></p>
            <p>Delivery Charge <strong>₹<?php echo number_format($delivery_charge, 2); ?></strong></p>
            <p>Convenience Charge <strong>₹<?php echo number_format($convenience_charge, 2); ?></strong></p>
            <p>Total Cost <strong>₹<?php echo number_format($total_with_charges, 2); ?></strong></p>
        </div>

        <!-- Payment Options -->
        <div class="payment-container">
            <h2>Select Payment Method</h2>
            <div class="payment-methods">
                <div class="payment-method" onclick="selectPayment('cod')">
                    <img src="/hello/assets/images/cod_icon.png" alt="COD">
                    <span>Cash on Delivery</span>
                </div>
                <div class="payment-method" onclick="selectPayment('upi')">
                    <img src="/hello/assets/images/upi_icon.png" alt="UPI">
                    <span>UPI Payment</span>
                </div>
                <!-- Add more payment methods as needed -->
            </div>
        </div>
    </div>
</div>

<script>
function selectPayment(method) {
    if(method === 'upi'){
        // Set your UPI id and create a payment payload.
        const upiId = "ar6373354-1@okaxis";
        const payload = {
            amount: <?php echo $total_with_charges; ?>,
            upiId: upiId,
            orderId: "ORDER_<?php echo time(); ?>"
        };

        // Call a dummy free payment API endpoint. Replace with an actual endpoint if available.
        fetch("https://free-upi-payment-api.example.com/pay", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if(data.status === "success"){
                alert("Payment successful!");
                window.location.href = "payment_success.php";
            } else {
                alert("Payment failed. Please try again.");
            }
        })
        .catch(error => {
            console.error("Error processing payment:", error);
            alert("Error processing payment. Please try again later.");
        });
    } else {
        alert("You selected: " + method);
        // Implement your COD or other payment method logic here.
    }
}
</script>

</body>
</html>
