<?php
/**
 * Authentication & Authorization
 * Handles login, logout, role-based access control
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/session.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Check if user has specific role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Check if user has any of the specified roles
function hasAnyRole($roles) {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], (array)$roles);
}

// Get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Get current user role
function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

// Get current user name
function getCurrentUserName() {
    return $_SESSION['username'] ?? 'Guest';
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "login.php");
        exit();
    }
}

// Redirect if not authorized
function requireRole($role) {
    requireLogin();
    if (!hasRole($role) && !hasRole('admin')) {
        header("Location: " . BASE_URL . "unauthorized.php");
        exit();
    }
}

// Redirect if not any of specified roles
function requireAnyRole($roles) {
    requireLogin();
    if (!hasAnyRole($roles) && !hasRole('admin')) {
        header("Location: " . BASE_URL . "unauthorized.php");
        exit();
    }
}

// Login function
function login($email, $password, $remember = false) {
    global $conn;
    
    $email = sanitize($email);
    
    $stmt = $conn->prepare("SELECT user_id, username, email, password, role, profile_image, is_active FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Check if account is active
        if ($user['is_active'] != 1) {
            return ['success' => false, 'message' => 'Your account has been deactivated. Please contact administrator.'];
        }
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['profile_image'] = $user['profile_image'];
            $_SESSION['last_activity'] = time();
            $_SESSION['created'] = time();
            $_SESSION['initialized'] = true;
            
            // Remember me functionality
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $hashed_token = hash('sha256', $token);
                
                $stmt2 = $conn->prepare("UPDATE users SET remember_token = ? WHERE user_id = ?");
                $stmt2->bind_param("si", $hashed_token, $user['user_id']);
                $stmt2->execute();
                
                setcookie('remember_token', $token, time() + 30 * 24 * 60 * 60, '/', '', false, true);
                setcookie('remember_user', $user['user_id'], time() + 30 * 24 * 60 * 60, '/', '', false, true);
            }
            
            // Update last login
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
            $stmt3 = $conn->prepare("UPDATE users SET last_login = NOW(), login_ip = ? WHERE user_id = ?");
            $stmt3->bind_param("si", $ip, $user['user_id']);
            $stmt3->execute();
            
            // Log activity
            logActivity($user['user_id'], 'Login', 'User logged in successfully');
            
            return ['success' => true, 'role' => $user['role'], 'user_id' => $user['user_id']];
        }
    }
    
    return ['success' => false, 'message' => 'Invalid email or password.'];
}

// Auto-login with remember token
function autoLogin() {
    global $conn;
    
    if (!isLoggedIn() && isset($_COOKIE['remember_token']) && isset($_COOKIE['remember_user'])) {
        $token = $_COOKIE['remember_token'];
        $user_id = intval($_COOKIE['remember_user']);
        $hashed_token = hash('sha256', $token);
        
        $stmt = $conn->prepare("SELECT user_id, username, email, role, profile_image, is_active FROM users WHERE user_id = ? AND remember_token = ?");
        $stmt->bind_param("is", $user_id, $hashed_token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if ($user['is_active'] == 1) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['profile_image'] = $user['profile_image'];
                $_SESSION['last_activity'] = time();
                
                // Generate new token
                $new_token = bin2hex(random_bytes(32));
                $new_hashed = hash('sha256', $new_token);
                
                $stmt2 = $conn->prepare("UPDATE users SET remember_token = ?, last_login = NOW() WHERE user_id = ?");
                $stmt2->bind_param("si", $new_hashed, $user['user_id']);
                $stmt2->execute();
                
                setcookie('remember_token', $new_token, time() + 30 * 24 * 60 * 60, '/', '', false, true);
                
                logActivity($user['user_id'], 'Auto Login', 'User auto-logged in via remember token');
                
                return true;
            }
        }
        
        // Clear invalid cookies
        setcookie('remember_token', '', time() - 3600, '/');
        setcookie('remember_user', '', time() - 3600, '/');
    }
    
    return false;
}

// Logout function
function logout() {
    global $conn;
    
    $user_id = getCurrentUserId();
    
    if ($user_id) {
        logActivity($user_id, 'Logout', 'User logged out');
        
        // Clear remember token
        $stmt = $conn->prepare("UPDATE users SET remember_token = NULL WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
    
    // Clear cookies
    setcookie('remember_token', '', time() - 3600, '/');
    setcookie('remember_user', '', time() - 3600, '/');
    
    // Clear session
    session_unset();
    session_destroy();
    
    // Start new session for flash messages
    session_start();
}

// Generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validate CSRF token
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Get CSRF token field
function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}

// Sanitize input
function sanitize($input) {
    global $conn;
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    if ($conn) {
        $input = $conn->real_escape_string($input);
    }
    return $input;
}

// Log activity
function logActivity($user_id, $action, $description = '') {
    global $conn;
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $action, $description, $ip, $user_agent);
    $stmt->execute();
}

// Redirect with message
function redirect($url, $type = '', $message = '') {
    if (!empty($message)) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: " . $url);
    exit();
}

// Show flash message
function showFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'] ?? 'info';
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        $alertClass = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ][$type] ?? 'alert-info';
        
        $icon = [
            'success' => 'check-circle',
            'error' => 'exclamation-circle',
            'warning' => 'exclamation-triangle',
            'info' => 'info-circle'
        ][$type] ?? 'info-circle';
        
        echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">
                <i class="fas fa-' . $icon . ' me-2"></i>' . $message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
    }
}

// Get user details
function getUserDetails($user_id = null) {
    global $conn;
    
    if (!$user_id) {
        $user_id = getCurrentUserId();
    }
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Get role-specific details
function getRoleDetails($user_id = null, $role = null) {
    global $conn;
    
    if (!$user_id) {
        $user_id = getCurrentUserId();
    }
    if (!$role) {
        $role = getCurrentUserRole();
    }
    
    switch ($role) {
        case 'teacher':
            $stmt = $conn->prepare("SELECT t.*, u.email, u.username FROM teachers t JOIN users u ON t.user_id = u.user_id WHERE u.user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
            
        case 'student':
            $stmt = $conn->prepare("SELECT s.*, u.email, u.username, c.class_name, sec.section_name, p.father_name, p.mother_name 
                                    FROM students s 
                                    JOIN users u ON s.user_id = u.user_id 
                                    LEFT JOIN classes c ON s.class_id = c.class_id 
                                    LEFT JOIN sections sec ON s.section_id = sec.section_id 
                                    LEFT JOIN parents p ON s.parent_id = p.parent_id 
                                    WHERE u.user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
            
        case 'parent':
            $stmt = $conn->prepare("SELECT p.*, u.email, u.username FROM parents p JOIN users u ON p.user_id = u.user_id WHERE u.user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
            
        default:
            return getUserDetails($user_id);
    }
}

// Check permission for a specific action
function canAccess($permission) {
    $role = getCurrentUserRole();
    
    $permissions = [
        'admin' => ['all'],
        'teacher' => ['view_students', 'mark_attendance', 'enter_marks', 'view_reports', 'manage_assignments'],
        'student' => ['view_profile', 'view_results', 'view_attendance', 'view_fees'],
        'parent' => ['view_child', 'view_results', 'view_attendance', 'view_fees', 'pay_fees'],
        'accountant' => ['manage_fees', 'view_reports', 'collect_fees', 'manage_expenses']
    ];
    
    return $role === 'admin' || in_array($permission, $permissions[$role] ?? []);
}
?>
