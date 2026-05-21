<?php
/**
 * Session Management
 * Handles all session-related operations
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session timeout in seconds (30 minutes)
define('SESSION_TIMEOUT', 1800);

// Check session timeout
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity'])) {
        $inactive = time() - $_SESSION['last_activity'];
        if ($inactive > SESSION_TIMEOUT) {
            // Session expired
            session_unset();
            session_destroy();
            header("Location: " . BASE_URL . "login.php?timeout=1");
            exit();
        }
    }
    $_SESSION['last_activity'] = time();
}

// Regenerate session ID periodically for security
function regenerateSession() {
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } else if (time() - $_SESSION['created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// Initialize session security
checkSessionTimeout();
regenerateSession();

// Prevent session fixation
if (!isset($_SESSION['initialized'])) {
    session_regenerate_id(true);
    $_SESSION['initialized'] = true;
}
?>
