<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_GET['food_id'])) {
    echo json_encode(['success' => false, 'message' => 'Food ID required']);
    exit();
}

$food_id = clean_input($_GET['food_id']);

// Get food details with category name
$query = "SELECT f.*, c.name as category_name 
          FROM foods f 
          LEFT JOIN categories c ON f.category_id = c.id 
          WHERE f.id = '$food_id'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Food item not found']);
    exit();
}

$food = mysqli_fetch_assoc($result);

echo json_encode([
    'success' => true,
    'data' => $food
]);
?>