<?php
require_once __DIR__ . '/../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}

$user_id = $_SESSION['user_id'];

// Check if the user already has an address saved
$query = $conn->prepare("SELECT * FROM user_addresses WHERE user_id = ? LIMIT 1");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$existing_address = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name     = $_POST['full_name'];
    $email         = $_POST['email'];
    $phone         = $_POST['phone'];
    $address_line1 = $_POST['address_line1'];
    $address_line2 = $_POST['address_line2'];
    $city          = $_POST['city'];
    $state         = $_POST['state'];
    $postal_code   = $_POST['postal_code'];
    $country       = $_POST['country'];

    if ($existing_address) {
        // Update existing address
        $update_query = $conn->prepare("
            UPDATE user_addresses
            SET full_name = ?, email = ?, phone = ?, address_line1 = ?, address_line2 = ?, 
                city = ?, state = ?, postal_code = ?, country = ?
            WHERE user_id = ?
        ");
        $update_query->bind_param(
            "sssssssssi",
            $full_name, $email, $phone, $address_line1, $address_line2,
            $city, $state, $postal_code, $country, $user_id
        );
        $update_query->execute();
    } else {
        // Insert new address
        $insert_query = $conn->prepare("
            INSERT INTO user_addresses (
                user_id, full_name, email, phone, address_line1, address_line2, 
                city, state, postal_code, country
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insert_query->bind_param(
            "isssssssss",
            $user_id, $full_name, $email, $phone, $address_line1, $address_line2,
            $city, $state, $postal_code, $country
        );
        $insert_query->execute();
    }

    // Redirect back to the user page after saving address
    header("Location: cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Information - Checkout</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* BASIC RESETS */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background: #f5f7fa;
            color: #333;
            padding: 20px;
        }
        a {
            color: #3498db;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        /* Top Navigation */
        .top-nav {
            text-align: right;
            margin-bottom: 20px;
        }
        .top-nav a {
            font-size: 16px;
        }
        /* Container */
        .info-container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .info-container h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        form label {
            display: block;
            margin: 10px 0 5px;
            font-weight: 600;
        }
        form input[type="text"],
        form input[type="email"],
        form input[type="tel"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .two-column {
            display: flex;
            gap: 20px;
        }
        .two-column .column {
            flex: 1;
        }
        .btn-submit {
            display: inline-block;
            margin-top: 20px;
            background-color: #3498db;
            color: #fff;
            padding: 12px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .btn-submit:hover {
            background-color: #2980b9;
        }
        /* Steps Indicator (Optional) */
        .checkout-steps {
            max-width: 600px;
            margin: 0 auto 30px auto;
        }
        .checkout-steps ul {
            display: flex;
            justify-content: center;
            list-style: none;
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
        @media (max-width: 600px) {
            .two-column {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<!-- Top Navigation with Home Option -->
<div class="top-nav">
    <a href="\hello\user_page.php">Home</a>
</div>

<!-- Optional: Steps Indicator -->
<div class="checkout-steps">
    <ul>
        <li class="active">1 Information</li>
        <li>2 Shipping</li>
        <li>3 Payment</li>
    </ul>
</div>

<div class="info-container">
    <h2>Enter Your Information</h2>
    <form method="POST" action="">
        <!-- Full Name -->
        <label for="full_name">Full Name</label>
        <input type="text" name="full_name" id="full_name" 
               value="<?php echo $existing_address['full_name'] ?? ''; ?>" required>
        <!-- Email & Phone -->
        <div class="two-column">
            <div class="column">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" 
                       value="<?php echo $existing_address['email'] ?? ''; ?>" required>
            </div>
            <div class="column">
                <label for="phone">Phone</label>
                <input type="tel" name="phone" id="phone" 
                       value="<?php echo $existing_address['phone'] ?? ''; ?>" required>
            </div>
        </div>
        <!-- Address Lines -->
        <label for="address_line1">Address Line 1</label>
        <input type="text" name="address_line1" id="address_line1"
               value="<?php echo $existing_address['address_line1'] ?? ''; ?>" required>
        <label for="address_line2">Address Line 2 (optional)</label>
        <input type="text" name="address_line2" id="address_line2"
               value="<?php echo $existing_address['address_line2'] ?? ''; ?>">
        <!-- City, State -->
        <div class="two-column">
            <div class="column">
                <label for="city">City</label>
                <input type="text" name="city" id="city"
                       value="<?php echo $existing_address['city'] ?? ''; ?>" required>
            </div>
            <div class="column">
                <label for="state">State</label>
                <input type="text" name="state" id="state"
                       value="<?php echo $existing_address['state'] ?? ''; ?>" required>
            </div>
        </div>
        <!-- Postal Code, Country -->
        <div class="two-column">
            <div class="column">
                <label for="postal_code">Postal Code</label>
                <input type="text" name="postal_code" id="postal_code"
                       value="<?php echo $existing_address['postal_code'] ?? ''; ?>" required>
            </div>
            <div class="column">
                <label for="country">Country</label>
                <input type="text" name="country" id="country"
                       value="<?php echo $existing_address['country'] ?? ''; ?>" required>
            </div>
        </div>
        <button type="submit" class="btn-submit">Save and Continue</button>
    </form>
</div>

</body>
</html>
