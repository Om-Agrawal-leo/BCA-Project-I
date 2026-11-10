<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name']);
    $description = isset($_POST['description']) ? clean_input($_POST['description']) : '';
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Category name is required']);
        exit();
    }
    
    $query = "INSERT INTO categories (name, description) VALUES ('$name', '$description')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Category added successfully',
            'id' => mysqli_insert_id($conn)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>