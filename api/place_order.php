<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$items = $data['items'] ?? [];
$notes = escape($data['notes'] ?? '');
$user_id = get_user_id();

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if (empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

// Calculate total
$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Insert order
$sql = "INSERT INTO orders (user_id, total_amount, notes, status) 
        VALUES ('$user_id', '$total', '$notes', 'pending')";

if (query($sql)) {
    $order_id = last_id();
    
    // Insert order items
    foreach ($items as $item) {
        $food_id = escape($item['id']);
        $quantity = escape($item['quantity']);
        $price = escape($item['price']);

        query("INSERT INTO order_items (order_id, food_id, quantity, price)
               VALUES ('$order_id', '$food_id', '$quantity', '$price')");
    }
    
    echo json_encode(['success' => true, 'order_id' => $order_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Order failed']);
}
?>