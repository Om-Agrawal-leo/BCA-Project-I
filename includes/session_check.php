<?php
// ================================
// Session Check File
// Validates user session and redirects if not logged in
// ================================

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function require_login($redirect_to = 'login.php') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . $redirect_to);
        exit();
    }
}

// Check session timeout (30 minutes)
function check_session_timeout() {
    $timeout_duration = 1800; // 30 minutes in seconds
    
    if (isset($_SESSION['last_activity'])) {
        $elapsed_time = time() - $_SESSION['last_activity'];
        
        if ($elapsed_time > $timeout_duration) {
            // Session expired
            session_unset();
            session_destroy();
            return true;
        }
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    return false;
}

// Regenerate session ID periodically for security
function regenerate_session() {
    if (!isset($_SESSION['regenerated'])) {
        $_SESSION['regenerated'] = time();
    } else {
        if (time() - $_SESSION['regenerated'] > 600) { // Every 10 minutes
            session_regenerate_id(true);
            $_SESSION['regenerated'] = time();
        }
    }
}

// Check if session is valid
function is_session_valid() {
    if (check_session_timeout()) {
        return false;
    }
    
    regenerate_session();
    return isset($_SESSION['user_id']);
}
?>