<?php
/**
 * Unauthorized Access Page
 */
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$pageTitle = 'Unauthorized Access';
$school = getSchoolInfo();
$schoolName = $school['school_name'] ?? SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized | <?php echo htmlspecialchars($schoolName); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .error-card { background: white; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); padding: 50px; text-align: center; max-width: 500px; width: 100%; margin: 20px; }
        .error-icon { font-size: 5rem; color: #e74a3b; margin-bottom: 20px; }
        .error-code { font-size: 4rem; font-weight: 800; color: #e74a3b; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon"><i class="fas fa-lock"></i></div>
        <div class="error-code">403</div>
        <h3 class="mb-3">Access Denied</h3>
        <p class="text-muted mb-4">You do not have permission to access this page. Please contact your administrator if you believe this is an error.</p>
        <div class="d-grid gap-2">
            <a href="<?php echo BASE_URL; ?>dashboard.php" class="btn btn-primary"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            <a href="<?php echo BASE_URL; ?>logout.php" class="btn btn-outline-secondary"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
        </div>
    </div>
</body>
</html>
