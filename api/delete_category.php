<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $category_id = clean_input($_POST['category_id']);
    
    // Check if category has food items
    $check_query = "SELECT COUNT(*) as count FROM foods WHERE category_id = '$category_id'";
    $check_result = mysqli_query($conn, $check_query);
    $check = mysqli_fetch_assoc($check_result);
    
    if ($check['count'] > 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'Cannot delete category with existing food items'
        ]);
        exit();
    }
    
    $query = "DELETE FROM categories WHERE id = '$category_id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>