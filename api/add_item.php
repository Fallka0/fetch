<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $user_id = $_SESSION['user_id'];
    $items_name = trim($_POST['name'] ?? '');
    $items_price = $_POST['price'] ?? 0;

    // Validate inputs
    if (empty($items_name) || !is_numeric($items_price) || $items_price < 0) {
        $_SESSION['error'] = "Invalid input data";
        header("Location: index.php");
        exit();
    }

    // Handle file upload
    $upload_dir = 'uploads/';
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    // Check if file was uploaded without errors
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $image_tmp = $_FILES['image']['tmp_name'];
        $image_info = getimagesize($image_tmp);
        $image_type = $image_info['mime'] ?? '';
        $image_size = $_FILES['image']['size'];
        $original_name = basename($_FILES['image']['name']);
        $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        // Validate file
        if ($image_size > $maxFileSize) {
            $_SESSION['error'] = "Image is too large. Max 5MB.";
            header("Location: index.php");
            exit();
        }

        if (!in_array($image_type, $allowedTypes)) {
            $_SESSION['error'] = "Only JPG, PNG, GIF, and WebP images are allowed.";
            header("Location: index.php");
            exit();
        }

        // Generate unique filename while preserving extension
        $image_name = uniqid('img_', true) . '.' . $extension;
        $target_path = $upload_dir . $image_name;

        // Move the file
        if (move_uploaded_file($image_tmp, $target_path)) {
            // Save to database with relative path
            $relative_path = $upload_dir . $image_name;
            $stmt = $conn->prepare("INSERT INTO items (user_id, items_name, items_price, items_picture_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isds", $user_id, $items_name, $items_price, $relative_path);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Item added successfully!";
            } else {
                $_SESSION['error'] = "Database error: " . $conn->error;
                // Remove the uploaded file if DB insert failed
                if (file_exists($target_path)) {
                    unlink($target_path);
                }
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Failed to move uploaded file.";
        }
    } else {
        $error_code = $_FILES['image']['error'] ?? -1;
        $_SESSION['error'] = "File upload error: " . uploadErrorToString($error_code);
    }

    header("Location: index.php");
    exit();
}

// Helper function to translate error codes to messages
function uploadErrorToString($error) {
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'File is too large (server limit)',
        UPLOAD_ERR_FORM_SIZE => 'File is too large (form limit)',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
    ];
    return $errors[$error] ?? "Unknown upload error ($error)";
}
?>