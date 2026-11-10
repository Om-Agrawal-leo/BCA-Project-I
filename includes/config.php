<?php
// ================================
// Database Configuration File
// ================================

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Ensure session cookie is available for the whole project
    ini_set('session.cookie_path', '/food-ordering-system/');
    if (!session_start()) {
        error_log('Session start failed');
    }
}

// Database credentials for XAMPP
define('DB_HOST', 'localhost');      // Database host
define('DB_USER', 'root');           // Default XAMPP MySQL username
define('DB_PASS', '');               // Default XAMPP MySQL password (empty)
define('DB_NAME', 'food_ordering');  // Database name

// Create database connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8 to support all characters
mysqli_set_charset($conn, "utf8");

// Base URL for the application
define('BASE_URL', '/food-ordering-system/');

// Function to safely escape strings for SQL queries
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

// Function to redirect to another page
function redirect($page) {
    header("Location: " . $page);
    exit();
}

// Function to display alert message
function set_message($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

// Function to display stored message
function display_message() {
    if (isset($_SESSION['message'])) {
        $type = $_SESSION['message_type'];
        $message = $_SESSION['message'];
        
        echo "<div class='alert alert-{$type}'>{$message}</div>";
        
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

// Timezone setting
date_default_timezone_set('Asia/Kathmandu');

?>