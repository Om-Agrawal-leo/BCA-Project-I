<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

$category_id = isset($_GET['category_id']) ? clean_input($_GET['category_id']) : null;
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';

$sql = "SELECT f.*, c.name as category_name FROM foods f LEFT JOIN categories c ON f.category_id = c.id WHERE f.available = 1";

$conditions = [];
if ($category_id) {
    $conditions[] = "f.category_id = '$category_id'";
}
if ($search) {
    $conditions[] = "(f.name LIKE '%$search%' OR f.description LIKE '%$search%')";
}

if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY f.name ASC";

$result = mysqli_query($conn, $sql);
$foods = [];

while ($row = mysqli_fetch_assoc($result)) {
    $foods[] = $row;
}

echo json_encode(['success' => true, 'foods' => $foods]);
?>
