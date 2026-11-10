<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

$status = $_GET['status'] ?? 'all';

if ($status === 'active') {
    $sql = "SELECT o.*, u.name as customer_name, u.phone 
            FROM orders o 
            JOIN users u ON o.user_id = u.user_id 
            WHERE o.status IN ('pending', 'received', 'preparing', 'ready')
            ORDER BY o.created_at DESC";
} else {
    $sql = "SELECT o.*, u.name as customer_name 
            FROM orders o 
            JOIN users u ON o.user_id = u.user_id 
            ORDER BY o.created_at DESC";
}

$orders = fetch_all(query($sql));

// Get items for each order
foreach ($orders as &$order) {
    $items_sql = "SELECT oi.*, f.name as food_name
                  FROM order_items oi
                  JOIN foods f ON oi.food_id = f.id
                  WHERE oi.order_id = " . $order['id'];
    $order['items'] = fetch_all(query($items_sql));
}

echo json_encode(['success' => true, 'orders' => $orders]);
?>