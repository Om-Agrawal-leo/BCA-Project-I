<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['food_id'])) {
    $food_id = clean_input($_POST['food_id']);
    
    // Check if food is in any pending orders
    $check_query = "SELECT COUNT(*) as count 
                   FROM order_items oi 
                   LEFT JOIN orders o ON oi.order_id = o.id 
                   WHERE oi.food_id = '$food_id' AND o.status IN ('Pending', 'Preparing', 'Cooking')";
    $check_result = mysqli_query($conn, $check_query);
    $check = mysqli_fetch_assoc($check_result);
    
    if ($check['count'] > 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'Cannot delete food item with pending orders'
        ]);
        exit();
    }
    
    $query = "DELETE FROM foods WHERE id = '$food_id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Food item deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>