<?php
require_once __DIR__ . '/../db_connect.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo '<script> window.location.href="/hello/components_admin/error/error.php";</script>';
    exit;
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set upload directory
$upload_dir = __DIR__ . '/../product_img/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Handle Add Product Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    // Check if an image was uploaded
    if (!empty($_FILES['image']['name'])) {
        $file_name = basename($_FILES["image"]["name"]);
        $target_file = $upload_dir . time() . "_" . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Allowed file types
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowed_types)) {
            echo "<script>alert('Only JPG, JPEG, PNG & GIF files are allowed.'); window.history.back();</script>";
            exit;
        }

        // Move the uploaded file to the product_img directory
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = "product_img/" . time() . "_" . $file_name; // Store relative path in DB
        } else {
            echo "<script>alert('Error uploading file!');</script>";
            exit;
        }
    } else {
        $image_url = ""; // If no image is uploaded, store an empty string
    }

    // Insert product into database
    $sql = "INSERT INTO products (name, price, stock, description, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdiss", $name, $price, $stock, $description, $image_url);

    if ($stmt->execute()) {
        echo "<script>alert('Product added successfully!'); window.location.href='product_mg.php';</script>";
    } else {
        echo "<script>alert('Error adding product');</script>";
    }
}

// Handle Remove Product Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_product'])) {
    $product_id = $_POST['product_id'];

    // Fetch the image file path from database before deleting the product
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($image_url);
    $stmt->fetch();
    $stmt->close();

    // Delete the image file from the folder if it exists
    if (!empty($image_url)) {
        $file_path = __DIR__ . '/../' . $image_url;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // Delete the product from the database
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        echo "<script>alert('Product removed successfully!');window.location.href='product_mg.php'</script>";
    } else {
        echo "<script>alert('Error removing product');</script>";
    }
}

// Fetch all products from the database
$products = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Products</title>
    <link rel="stylesheet" href="/hello/assets/css/product_mgmt.css">
</head>
<body>

    <h2>Admin Panel - Manage Products</h2>

    <!-- Add Product Form -->
    <form method="POST" action="" enctype="multipart/form-data">
        <h3>Add New Product</h3>
        <label>Name:</label>
        <input type="text" name="name" required>
        
        <label>Price:</label>
        <input type="number" step="0.01" name="price" required>
        
        <label>Stock:</label>
        <input type="number" name="stock" required>

        <label>Description:</label>
        <textarea name="description" required></textarea>

        <label>Image:</label>
        <input type="file" name="image" required>

        <button type="submit" name="add_product">Add Product</button>
    </form>

    <hr>

    <!-- Remove & Edit Product Section -->
    <div class="table-container">
        <h3>Existing Products</h3>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price (₹)</th>
                <th>Stock</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
            <?php while ($product = $products->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $product['id']; ?></td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td>₹<?php echo number_format($product['price'], 2); ?></td>
                <td><?php echo $product['stock']; ?></td>
                <td>
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?php echo '../' . $product['image']; ?>" alt="Product Image" width="50">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td>
                    <!-- Edit Button -->
                    <a href="edit_product.php?product_id=<?php echo $product['id']; ?>">
                        <button type="button">Edit</button>
                    </a>
                    <!-- Remove Button -->
                    <form method="POST" action="" style="display:inline-block;">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit" name="remove_product">Remove</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

</body>
</html>
