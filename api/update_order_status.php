<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

error_log("update_order_status: Request received");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];

    error_log("update_order_status: order_id=$order_id, status=$status");

    // Validate status
    $allowed_statuses = array('Pending', 'Received', 'Preparing', 'Cooking', 'Ready', 'Served', 'Completed', 'Cancelled');

    if (!in_array($status, $allowed_statuses)) {
        error_log("update_order_status: Invalid status $status");
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit();
    }

    $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'si', $status, $order_id);
    if (mysqli_stmt_execute($stmt)) {
        error_log("update_order_status: Status updated successfully");
        echo json_encode(['success' => true, 'message' => 'Status updated']);
    } else {
        error_log("update_order_status: Database error: " . mysqli_stmt_error($stmt));
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    mysqli_stmt_close($stmt);
} else {
    error_log("update_order_status: Invalid request");
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>