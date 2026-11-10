<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_GET['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID required']);
    exit();
}

$order_id = clean_input($_GET['order_id']);

// Get order details
$order_query = "SELECT o.*, u.name as customer_name, u.phone as customer_phone 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                WHERE o.id = '$order_id'";
$order_result = mysqli_query($conn, $order_query);

if (!$order_result || mysqli_num_rows($order_result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

$order = mysqli_fetch_assoc($order_result);

// Get order items
$items = get_order_items($order_id);

echo json_encode([
    'success' => true,
    'data' => [
        'order' => $order,
        'items' => $items
    ]
]);
?>