<?php
require_once __DIR__ . '/../db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to continue checkout.");
}

$user_id = $_SESSION['user_id'];

// Fetch cart details
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

// Redirect if cart is empty
if (empty($cart_items)) {
    header("Location: /hello/components/error_u/Empty_Cart.php");
    exit;
}

// Fetch user address
$addr_query = $conn->prepare("SELECT * FROM user_addresses WHERE user_id = ? LIMIT 1");
$addr_query->bind_param("i", $user_id);
$addr_query->execute();
$addr_result = $addr_query->get_result();
$user_address = $addr_result->fetch_assoc();

// Additional charges
$delivery_charge = 30.00;
$convenience_charge = 20.00;
$total_with_charges = $total + $delivery_charge + $convenience_charge;

// Store in session for payment processing
$_SESSION['total_with_charges'] = $total_with_charges;
$_SESSION['user_address'] = $user_address;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .checkout-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        h2 {
            color: #333;
        }
        .btn-pay {
            background-color: #3399cc;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        .btn-pay:hover {
            background-color: #287bb5;
        }
    </style>
</head>
<body>

<div class="checkout-container">
    <h2>Checkout</h2>
    <p>Total Amount: ₹<?php echo number_format($total_with_charges, 2); ?></p>
    <button id="rzp-button1" class="btn-pay">Pay Now</button>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var totalAmount = <?php echo $total_with_charges * 100; ?>; // Amount in paisa

        if (isNaN(totalAmount) || totalAmount <= 0) {
            alert("Error: Invalid total amount!");
            return;
        }

        var options = {
            "key": "rzp_test_QNZ100uWneVfMf", // Replace with your Razorpay Key ID
            "amount": totalAmount,
            "currency": "INR",
            "name": "My E-Commerce",
            "description": "Order Payment",
            "image": "/hello/assets/images/logo.png",
            "handler": function (response) {
                alert("Payment successful! Payment ID: " + response.razorpay_payment_id);
                window.location.href = "user_page.php";
            },
            "prefill": {
                "name": "<?php echo htmlspecialchars($user_address['full_name'] ?? ''); ?>",
                "email": "<?php echo htmlspecialchars($user_address['email'] ?? ''); ?>",
                "contact": "<?php echo htmlspecialchars($user_address['phone'] ?? ''); ?>"
            },
            "notes": {
                "address": "<?php echo htmlspecialchars($user_address['address_line1'] ?? ''); ?>"
            },
            "theme": {
                "color": "#3399cc"
            }
        };

        var rzp1 = new Razorpay(options);

        document.getElementById('rzp-button1').addEventListener("click", function (e) {
            e.preventDefault();
            rzp1.open();
        });
    });
</script>

</body>
</html>
