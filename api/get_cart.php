<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in', 'items' => []]);
    exit();
}

$cart_items = array();
$total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $food_id => $quantity) {
        $food = get_food_by_id($food_id);
        if ($food) {
            $food['quantity'] = $quantity;
            $food['subtotal'] = $food['price'] * $quantity;
            $cart_items[] = $food;
            $total += $food['subtotal'];
        }
    }
}

echo json_encode([
    'success' => true,
    'items' => $cart_items,
    'total' => $total,
    'count' => count($cart_items)
]);
?>
