<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../db_connect.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo '<script> window.location.href="/hello/components_admin/error/error.php";</script>';
    exit;
}

$upload_dir = __DIR__ . "/../uploads/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Handle banner deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_banner'])) {
    $banner_id = intval($_POST['delete_banner']);

    // Fetch image URL from database
    $stmt = $conn->prepare("SELECT image_url FROM banners WHERE id = ?");
    $stmt->bind_param("i", $banner_id);
    $stmt->execute();
    $stmt->bind_result($image_url);
    $stmt->fetch();
    $stmt->close();

    if ($image_url) {
        // Delete the image file
        $file_path = __DIR__ . '/../' . $image_url;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Delete record from database
        $stmt = $conn->prepare("DELETE FROM banners WHERE id = ?");
        $stmt->bind_param("i", $banner_id);
        if ($stmt->execute()) {
            echo "<script>alert('Banner deleted successfully!'); window.location.href='banner_mg.php';</script>";
        } else {
            echo "<script>alert('Error deleting banner!');</script>";
        }
    }
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["banner_image"])) {
    $file_name = basename($_FILES["banner_image"]["name"]);
    $target_file = $upload_dir . time() . "_" . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    $allowed_types = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowed_types)) {
        echo "<script>alert('Only JPG, JPEG, PNG & GIF files are allowed.'); window.history.back();</script>";
        exit;
    }

    if (move_uploaded_file($_FILES["banner_image"]["tmp_name"], $target_file)) {
        $image_url = "uploads/" . time() . "_" . $file_name;
        $stmt = $conn->prepare("INSERT INTO banners (image_url) VALUES (?)");
        $stmt->bind_param("s", $image_url);
        if ($stmt->execute()) {
            echo "<script>alert('Banner uploaded successfully!'); window.location.href='banner_mg.php';</script>";
        } else {
            echo "<script>alert('Database error!');</script>";
        }
    } else {
        echo "<script>alert('Error uploading file!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Banners</title>
    <link rel="stylesheet" href="\hello\assets\css\banner_mg.css">
    <script src="\hello\assets\js\banner_mg.js"></script>
</head>
<body>

<h2>Upload Banner Image</h2>
<form action="" method="POST" enctype="multipart/form-data">
    <label>Select Banner Image:</label>
    <input type="file" name="banner_image" required>
    <button type="submit">Upload</button>
</form>

<h2>Current Banners</h2>
<div class="banner-container">
    <?php
    $result = $conn->query("SELECT id, image_url FROM banners ORDER BY uploaded_at DESC");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="banner-item">';
            echo '<img src="../' . htmlspecialchars($row['image_url']) . '" alt="Banner Image">';
            echo '<form action="" method="POST" style="display:inline;">';
            echo '<input type="hidden" name="delete_banner" value="' . $row['id'] . '">';
            echo '<button type="submit" class="delete-btn" onclick="return confirm(\'Are you sure you want to delete this banner?\');">❌</button>';
            echo '</form>';
            echo '</div>';  
        }
    } else {
        echo "<p>No banners uploaded yet.</p>";
    }
    ?>
</div>

</body>
</html>
