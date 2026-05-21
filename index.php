<?php
/**
 * Index - Entry Point
 * Redirects to dashboard if logged in, otherwise to login page
 */
require_once 'config/database.php';
require_once 'includes/auth.php';

// Auto-login check
autoLogin();

if (isLoggedIn()) {
    header("Location: " . BASE_URL . "dashboard.php");
} else {
    header("Location: " . BASE_URL . "login.php");
}
exit();
