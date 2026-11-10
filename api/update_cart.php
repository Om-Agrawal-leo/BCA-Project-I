<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['food_id']) || !isset($_POST['change'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit();
    }
    
    $food_id = clean_input($_POST['food_id']);
    $change = intval($_POST['change']);
    
    // Debug logging
    error_log('Updating quantity - food_id: ' . $food_id . ', change: ' . $change);
    error_log('Current cart before: ' . json_encode($_SESSION['cart']));
    
    if (isset($_SESSION['cart'][$food_id])) {
        $_SESSION['cart'][$food_id] += $change;
        
        if ($_SESSION['cart'][$food_id] <= 0) {
            unset($_SESSION['cart'][$food_id]);
            error_log('Quantity was 0 or less, item removed');
        }
    }
    
    error_log('Cart after: ' . json_encode($_SESSION['cart']));
    
    // Ensure session is written to disk
    session_write_close();
    
    echo json_encode(['success' => true, 'message' => 'Cart updated']);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid request method']);
?>