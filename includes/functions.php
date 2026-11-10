<?php
// ================================
// ADMIN PANEL HELPERS
// ================================
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']);
}

function get_all_suppliers() {
    global $conn;
    $suppliers = [];
    $result = mysqli_query($conn, "SELECT * FROM suppliers ORDER BY name ASC");
    while ($row = mysqli_fetch_assoc($result)) $suppliers[] = $row;
    return $suppliers;
}

function get_all_raw_materials() {
    global $conn;
    $materials = [];
    $sql = "SELECT rm.*, s.name as supplier_name FROM raw_materials rm LEFT JOIN suppliers s ON rm.supplier_id = s.id ORDER BY rm.purchase_date DESC, rm.id DESC";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) $materials[] = $row;
    return $materials;
}

function get_sales_report($type = 'daily') {
    global $conn;
    $data = [];
    switch ($type) {
        case 'weekly':
            $sql = "SELECT DATE_FORMAT(order_time, '%x-%v') as period, COUNT(*) as total_orders, SUM(total_amount) as total_sales FROM orders GROUP BY period ORDER BY period DESC";
            break;
        case 'monthly':
            $sql = "SELECT DATE_FORMAT(order_time, '%Y-%m') as period, COUNT(*) as total_orders, SUM(total_amount) as total_sales FROM orders GROUP BY period ORDER BY period DESC";
            break;
        case 'yearly':
            $sql = "SELECT YEAR(order_time) as period, COUNT(*) as total_orders, SUM(total_amount) as total_sales FROM orders GROUP BY period ORDER BY period DESC";
            break;
        case 'daily':
        default:
            $sql = "SELECT DATE(order_time) as period, COUNT(*) as total_orders, SUM(total_amount) as total_sales FROM orders GROUP BY period ORDER BY period DESC";
            break;
    }
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) $data[] = $row;
    return $data;
}

function get_categories() {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) $categories[] = $row;
    return $categories;
}



function get_cart_total() {
    $total = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $food_id => $qty) {
            $food = get_food_by_id($food_id);
            if ($food) {
                $total += $food['price'] * $qty;
            }
        }
    }
    return $total;
}
function get_foods_by_category($category_id) {
    global $conn;
    $category_id = clean_input($category_id);
    $sql = "SELECT f.*, c.name as category_name FROM foods f LEFT JOIN categories c ON f.category_id = c.id WHERE f.category_id = '$category_id' ORDER BY f.name ASC";
    $result = mysqli_query($conn, $sql);
    $foods = [];
    while ($row = mysqli_fetch_assoc($result)) $foods[] = $row;
    return $foods;
}

//


// ================================
// MISSING HELPER FUNCTION STUBS
// ================================

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function get_user_data() {
    if (!isset($_SESSION['user_id'])) return null;
    global $conn;
    $id = $_SESSION['user_id'];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id' LIMIT 1");
    return mysqli_fetch_assoc($result);
}

function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}



function get_all_foods() {
    global $conn;
    $sql = "SELECT f.*, c.name as category_name FROM foods f LEFT JOIN categories c ON f.category_id = c.id ORDER BY f.name ASC";
    $result = mysqli_query($conn, $sql);
    $foods = [];
    while ($row = mysqli_fetch_assoc($result)) $foods[] = $row;
    return $foods;
}

function get_food_by_id($id) {
    global $conn;
    $id = clean_input($id);
    $result = mysqli_query($conn, "SELECT * FROM foods WHERE id = '$id' LIMIT 1");
    return mysqli_fetch_assoc($result);
}

function format_price($price) {
    return 'Rs. ' . number_format((float)$price, 2);
}

function format_date($datetime) {
    return date('M d, Y h:i A', strtotime($datetime));
}

function get_cart_count() {
    return isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
}

function get_total_sales() {
    global $conn;
    $result = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders");
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function get_today_sales() {
    global $conn;
    $today = date('Y-m-d');
    $result = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE DATE(order_time) = '$today'");
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function count_customers() {
    global $conn;
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function count_pending_orders() {
    global $conn;
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status = 'Pending'");
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function get_all_orders() {
    global $conn;
    $sql = "SELECT o.*, u.name AS customer_name, u.phone AS customer_phone FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.order_time DESC";
    $result = mysqli_query($conn, $sql);
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) $orders[] = $row;
    return $orders;
}

function get_user_orders($user_id) {
    global $conn;
    $user_id = clean_input($user_id);
    $result = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY order_time DESC");
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) $orders[] = $row;
    return $orders;
}

function get_order_items($order_id) {
    global $conn;
    $order_id = clean_input($order_id);
    $result = mysqli_query($conn, "SELECT oi.*, f.name as food_name FROM order_items oi LEFT JOIN foods f ON oi.food_id = f.id WHERE oi.order_id = '$order_id'");
    $items = [];
    while ($row = mysqli_fetch_assoc($result)) $items[] = $row;
    return $items;
}

function get_status_color($status) {
    $map = [
        'Pending' => '#ff9800',
        'Received' => '#03a9f4',
        'Preparing' => '#ffb300',
        'Cooking' => '#ffb300',
        'Ready' => '#4caf50',
        'Served' => '#2196f3',
        'Completed' => '#4caf50',
        'Cancelled' => '#f44336',
    ];
    return $map[$status] ?? '#888';
}

// For API helpers
function fetch_all($result) {
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

function query($sql) {
    global $conn;
    return mysqli_query($conn, $sql);
}

function last_id() {
    global $conn;
    return mysqli_insert_id($conn);
}

function escape($str) {
    global $conn;
    return mysqli_real_escape_string($conn, $str);
}
