<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $user_id = $_SESSION['user_id'];
    $item_id = $_POST['item_id'];

    // Verify the item belongs to the user before deleting
    $stmt = $conn->prepare("SELECT items_picture_path FROM items WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $item_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image_path = $row['items_picture_path'];
        
        // Delete the item from database
        $delete_stmt = $conn->prepare("DELETE FROM items WHERE id = ? AND user_id = ?");
        $delete_stmt->bind_param("ii", $item_id, $user_id);
        $delete_stmt->execute();
        
        // Delete the associated image file
        if (file_exists($image_path) && $image_path !== 'img/default.jpg') {
            unlink($image_path);
        }
        
        $_SESSION['success'] = "Item deleted successfully!";
    } else {
        $_SESSION['error'] = "Item not found or you don't have permission to delete it.";
    }
    
    header("Location: index.php");
    exit();
}
?>