<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!is_logged_in()) {
    echo json_encode([
        'success' => false,
        'message' => 'Not logged in'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

if (!isset($_POST['food_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Food ID missing'
    ]);
    exit;
}

$food_id = (string)$_POST['food_id'];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$food_id])) {
    unset($_SESSION['cart'][$food_id]);
    session_write_close();

    echo json_encode([
        'success' => true,
        'message' => 'Item removed'
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Item not found in cart'
]);
exit;
?>