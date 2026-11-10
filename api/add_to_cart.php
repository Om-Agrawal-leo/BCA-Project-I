<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    // DEBUG: Output session for troubleshooting
    error_log('SESSION: ' . print_r($_SESSION, true));
    echo json_encode(['success' => false, 'message' => 'Please login first', 'session' => $_SESSION]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['food_id'])) {
    $food_id = clean_input($_POST['food_id']);
    
    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    
    // Add or increment quantity
    if (isset($_SESSION['cart'][$food_id])) {
        $_SESSION['cart'][$food_id]++;
    } else {
        $_SESSION['cart'][$food_id] = 1;
    }
    
    echo json_encode(['success' => true, 'message' => 'Item added to cart']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>