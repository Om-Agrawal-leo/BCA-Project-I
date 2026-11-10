<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = get_user_id();
$orders = get_user_orders($user_id);

echo json_encode(['success' => true, 'orders' => $orders]);
?>