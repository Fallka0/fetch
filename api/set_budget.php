<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['budget'])) {
    $user_id = $_SESSION['user_id'];
    $budget = (float)$_POST['budget'];
    
    // Store budget in session and database
    $_SESSION['budget'] = $budget;
    
    $stmt = $conn->prepare("REPLACE INTO user_budgets (user_id, budget) VALUES (?, ?)");
    $stmt->bind_param("id", $user_id, $budget);
    $stmt->execute();
    $stmt->close();
    
    header("Location: index.php");
    exit();
}
?>