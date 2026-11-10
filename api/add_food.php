<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = clean_input($_POST['category_id']);
    $name = clean_input($_POST['name']);
    $description = isset($_POST['description']) ? clean_input($_POST['description']) : '';
    $price = clean_input($_POST['price']);
    $available = isset($_POST['available']) ? 1 : 0;
    
    // Validation
    if (empty($category_id) || empty($name) || empty($price)) {
        echo json_encode(['success' => false, 'message' => 'Required fields missing']);
        exit();
    }
    
    if (!is_numeric($price) || $price < 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid price']);
        exit();
    }
    
    $query = "INSERT INTO foods (category_id, name, description, price, available) 
              VALUES ('$category_id', '$name', '$description', '$price', '$available')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Food item added successfully',
            'id' => mysqli_insert_id($conn)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>