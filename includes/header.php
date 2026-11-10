<?php
// ================================
// Common Header File
// Include this at the top of pages that need user authentication
// ================================

require_once 'config.php';
require_once 'functions.php';
require_once 'session_check.php';

// Check if session is valid
if (!is_session_valid() && basename($_SERVER['PHP_SELF']) != 'login.php' && basename($_SERVER['PHP_SELF']) != 'register.php') {
    set_message("Your session has expired. Please login again.", "warning");
    redirect('login.php');
}

// Get current user data if logged in
$current_user = null;
if (isset($_SESSION['user_id'])) {
    $current_user = get_user_data();
}
?>