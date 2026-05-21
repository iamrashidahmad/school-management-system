<?php
/**
 * School Settings
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
requireRole('admin');

$pageTitle = 'School Settings';
$activeMenu = 'settings';

$school = getSchoolInfo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $name = sanitize($_POST['school_name'] ?? '');
        $address = sanitize($_POST['school_address'] ?? '');
        $phone = sanitize($_POST['school_phone'] ?? '');
        $email = sanitize($_POST['school_email'] ?? '');
        $website = sanitize($_POST['school_website'] ?? '');
        $principal = sanitize($_POST['principal_name'] ?? '');
        $session = sanitize($_POST['academic_session'] ?? '');
        $currency = sanitize($_POST['currency_symbol'] ?? '$');
        $theme = sanitize($_POST['theme_color'] ?? '#4e73df');
        
        $stmt = $conn->prepare("UPDATE school_info SET school_name=?, school_address=?, school_phone=?, school_email=?, school_website=?, principal_name=?, academic_session=?, currency_symbol=?, theme_color=?");
        $stmt->bind_param("sssssssss", $name, $address, $phone, $email, $website, $principal, $session, $currency, $theme);
        
        if ($stmt->execute()) {
            logActivity(getCurrentUserId(), 'Settings Updated', 'School information updated');
            redirect(BASE_URL.'modules/settings/school.php', 'success', 'School settings updated successfully.');
        }
    }
}

include '../../includes/header.php';
?>
<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-school me-2 text-primary"></i>School Settings</h1>
            </div>
            <?php showFlashMessage(); ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <?php echo csrfField(); ?>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">School Name *</label><input type="text" class="form-control" name="school_name" value="<?php echo $school['school_name']; ?>" required></div>
                            <div class="col-md-6"><label class="form-label">Principal Name</label><input type="text" class="form-control" name="principal_name" value="<?php echo $school['principal_name']; ?>"></div>
                            <div class="col-12"><label class="form-label">Address</label><textarea class="form-control" name="school_address" rows="2"><?php echo $school['school_address']; ?></textarea></div>
                            <div class="col-md-4"><label class="form-label">Phone</label><input type="text" class="form-control" name="school_phone" value="<?php echo $school['school_phone']; ?>"></div>
                            <div class="col-md-4"><label class="form-label">Email</label><input type="email" class="form-control" name="school_email" value="<?php echo $school['school_email']; ?>"></div>
                            <div class="col-md-4"><label class="form-label">Website</label><input type="text" class="form-control" name="school_website" value="<?php echo $school['school_website']; ?>"></div>
                            <div class="col-md-4"><label class="form-label">Academic Session</label><input type="text" class="form-control" name="academic_session" value="<?php echo $school['academic_session']; ?>"></div>
                            <div class="col-md-4"><label class="form-label">Currency Symbol</label><input type="text" class="form-control" name="currency_symbol" value="<?php echo $school['currency_symbol']; ?>"></div>
                            <div class="col-md-4"><label class="form-label">Theme Color</label><input type="color" class="form-control" name="theme_color" value="<?php echo $school['theme_color']; ?>"></div>
                            <div class="col-12"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Settings</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</div>
<?php include '../../includes/footer.php'; ?>