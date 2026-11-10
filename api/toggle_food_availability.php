<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['food_id']) && isset($_POST['available'])) {
    $food_id = clean_input($_POST['food_id']);
    $available = clean_input($_POST['available']);
    
    $query = "UPDATE foods SET available = '$available' WHERE id = '$food_id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Availability updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>