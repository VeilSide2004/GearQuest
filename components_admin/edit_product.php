<?php
require_once __DIR__ . '/../db_connect.php';


if (!isset($_SESSION['admin_id'])) {
    echo '<script> window.location.href="/hello/components_admin/error/error.php";</script>';
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['product_id'])) {
    echo "Product ID not provided.";
    exit;
}

$product_id = $_GET['product_id'];

// Fetch product details from the database
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    echo "Product not found.";
    exit;
}

// Handle form submission for updating product details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    // Check if a new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = __DIR__ . '/../product_img/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = basename($_FILES["image"]["name"]);
        $target_file = $upload_dir . time() . "_" . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowed_types)) {
            echo "<script>alert('Only JPG, JPEG, PNG & GIF files are allowed.'); window.history.back();</script>";
            exit;
        }
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $new_image_url = "product_img/" . time() . "_" . $file_name;
            // Delete the old image file if it exists
            if (!empty($product['image'])) {
                $old_image_path = __DIR__ . '/../' . $product['image'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
        } else {
            echo "<script>alert('Error uploading new image!'); window.history.back();</script>";
            exit;
        }
    } else {
        // No new image uploaded; keep the old image
        $new_image_url = $product['image'];
    }

    // Update the product in the database
    $update_sql = "UPDATE products SET name = ?, price = ?, stock = ?, description = ?, image = ? WHERE id = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("sdissi", $name, $price, $stock, $description, $new_image_url, $product_id);

    if ($stmt_update->execute()) {
        echo "<script>alert('Product updated successfully!'); window.location.href='product_mg.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error updating product.'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="/hello/assets/css/product_mgmt.css">
</head>
<body>
    <h2>Edit Product</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <label>Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

        <label>Price:</label>
        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>

        <label>Stock:</label>
        <input type="number" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>

        <label>Description:</label>
        <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>

        <label>Current Image:</label>
        <?php if (!empty($product['image'])): ?>
            <img src="<?php echo '../' . $product['image']; ?>" alt="Product Image" width="50">
        <?php else: ?>
            <p>No image available.</p>
        <?php endif; ?>
        <br>
        <label>New Image (optional):</label>
        <input type="file" name="image">

        <button type="submit" name="update_product">Update Product</button>
    </form>
    <br>
    <a href="product_mg.php">Cancel</a>
</body>
</html>
