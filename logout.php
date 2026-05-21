<?php
/**
 * Logout Script
 */
require_once 'config/database.php';
require_once 'includes/auth.php';

logout();

$_SESSION['flash_message'] = 'You have been logged out successfully.';
$_SESSION['flash_type'] = 'success';

header("Location: " . BASE_URL . "login.php");
exit();
?>
