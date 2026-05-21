<?php
/**
 * Advanced School Management System ERP
 * Database Configuration File
 */

// Database credentials - Change these according to your XAMPP setup
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Default XAMPP MySQL username
define('DB_PASS', '');           // Default XAMPP MySQL password (empty)
define('DB_NAME', 'school_management');
define('DB_CHARSET', 'utf8mb4');

// Application settings
define('BASE_URL', 'http://localhost/school-management-system/');
define('SITE_NAME', 'Excellence Public School');
define('VERSION', '1.0');
define('TIMEZONE', 'UTC');

// Error reporting - Set to 0 for production, E_ALL for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set(TIMEZONE);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create database connection
$conn = null;

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset
    $conn->set_charset(DB_CHARSET);
    
} catch (Exception $e) {
    die("<div style='padding:20px; background:#f8d7da; color:#721c24; border-radius:5px;'>
        <h3>Database Connection Error</h3>
        <p>" . $e->getMessage() . "</p>
        <p>Please make sure:</p>
        <ol>
            <li>XAMPP Apache and MySQL services are running</li>
            <li>Database 'school_management' has been created</li>
            <li>Import the SQL file from /database/school_management.sql</li>
        </ol>
    </div>");
}

// Helper function to get connection
function getDB() {
    global $conn;
    return $conn;
}

// Function to close connection
function closeDB() {
    global $conn;
    if ($conn) {
        $conn->close();
    }
}
?>
