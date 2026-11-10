<?php
// ================================
// Get Orders API for Kitchen
// Returns active orders in JSON format
// ================================

require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Get all active orders (not completed or cancelled)
$query = "SELECT o.*, u.name as customer_name, u.phone as customer_phone 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          WHERE o.status IN ('Pending', 'Received', 'Preparing', 'Cooking', 'Ready') 
          ORDER BY o.order_time ASC";

$result = mysqli_query($conn, $query);

$orders = array();

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Get items for this order
        $order_id = $row['id'];
        $items_query = "SELECT oi.*, f.name as food_name 
                       FROM order_items oi 
                       LEFT JOIN foods f ON oi.food_id = f.id 
                       WHERE oi.order_id = '$order_id'";
        $items_result = mysqli_query($conn, $items_query);
        
        $items = array();
        if ($items_result && mysqli_num_rows($items_result) > 0) {
            while ($item = mysqli_fetch_assoc($items_result)) {
                $items[] = $item;
            }
        }
        
        $row['items'] = $items;
        $orders[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'data' => $orders,
    'count' => count($orders),
    'timestamp' => date('Y-m-d H:i:s')
]);
?>