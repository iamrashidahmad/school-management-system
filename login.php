<?php
/**
 * Login Page
 */
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';   // <-- Add this (adjust path as needed)


// Auto-login check
autoLogin();

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit();
}

$error = '';
$email = '';

// Process login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $result = login($email, $password, $remember);
        
        if ($result['success']) {
            header("Location: " . BASE_URL . "dashboard.php");
            exit();
        } else {
            $error = $result['message'];
        }
    }
}

// Check for timeout
if (isset($_GET['timeout'])) {
    $error = 'Your session has expired. Please login again.';
}

$school = getSchoolInfo();
$schoolName = $school['school_name'] ?? SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | <?php echo htmlspecialchars($schoolName); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/css/style.css" rel="stylesheet">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-school"></i>
            <h2><?php echo htmlspecialchars($schoolName); ?></h2>
            <p class="mb-0">School Management System ERP</p>
        </div>
        
        <div class="login-body">
            <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <form class="login-form" method="POST" action="" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($email); ?>" 
                               placeholder="Enter your email" required autofocus>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label fw-bold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter your password" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember me on this device</label>
                </div>
                
                <button type="submit" class="btn login-btn">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>
        </div>
        
        <div class="login-footer">
            <p class="text-muted mb-2"><i class="fas fa-info-circle me-1"></i>Sample Login Credentials</p>
            <div class="credential-box">
                <div class="small">
                    <span class="fw-bold">Admin:</span>
                    <span class="font-monospace">admin@school.com</span>
                </div>
                <div class="small">
                    <span class="fw-bold">Teacher:</span>
                    <span class="font-monospace">teacher@school.com</span>
                </div>
                <div class="small">
                    <span class="fw-bold">Student:</span>
                    <span class="font-monospace">student@school.com</span>
                </div>
                <div class="small">
                    <span class="fw-bold">Parent:</span>
                    <span class="font-monospace">parent@school.com</span>
                </div>
                <div class="small border-top mt-2 pt-1 text-muted">
                    <span class="fw-bold">Password:</span>
                    <span class="font-monospace">Same as username prefix + 123</span>
                </div>
            </div>
            
            <p class="text-muted mt-3 mb-0" style="font-size:0.75rem;">
                <i class="fas fa-lock me-1"></i>Secure Login &bull; All passwords are encrypted
            </p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
    $(document).ready(function() {
        // Toggle password
        $('.toggle-password').on('click', function() {
            var input = $($(this).data('target'));
            var icon = $(this).find('i');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    });
    </script>
</body>
</html>
