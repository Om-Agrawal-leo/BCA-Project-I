<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Please login']);
    exit();
}

if (!isset($_GET['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID required']);
    exit();
}

$order_id = clean_input($_GET['order_id']);
$user_id = get_user_id();

// Get order (must belong to current user)
$query = "SELECT * FROM orders WHERE id = '$order_id' AND user_id = '$user_id'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

$order = mysqli_fetch_assoc($result);

echo json_encode([
    'success' => true,
    'data' => $order
]);
?>