<?php
/**
 * Fee Types
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$pageTitle = 'Fee Types';
$activeMenu = 'fees';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $name = sanitize($_POST['fee_name'] ?? '');
        $code = sanitize($_POST['fee_code'] ?? '');
        $desc = sanitize($_POST['description'] ?? '');
        if ($name) {
            $stmt = $conn->prepare("INSERT INTO fee_types (fee_name, fee_code, description) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $code, $desc);
            $stmt->execute();
            redirect(BASE_URL.'modules/fees/types.php', 'success', 'Fee type added.');
        }
    }
}

$types = $conn->query("SELECT * FROM fee_types ORDER BY fee_type_id");
include '../../includes/header.php';
?>
<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-tags me-2 text-primary"></i>Fee Types</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-2"></i>Add</button>
            </div>
            <?php showFlashMessage(); ?>
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0"><thead><tr><th>Name</th><th>Code</th><th>Description</th><th>Status</th></tr></thead>
                        <tbody><?php while($t=$types->fetch_assoc()): ?><tr><td><?php echo $t['fee_name']; ?></td><td class="font-monospace"><?php echo $t['fee_code']; ?></td><td><?php echo $t['description']; ?></td><td><?php echo getStatusBadge($t['status']?'active':'inactive'); ?></td></tr><?php endwhile; ?></tbody>
                    </table>
                </div>
            </div>
        </div>
       
    </div>
</div>
<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5>Add Fee Type</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST"><?php echo csrfField(); ?>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Name *</label><input type="text" class="form-control" name="fee_name" required></div>
            <div class="mb-3"><label class="form-label">Code</label><input type="text" class="form-control" name="fee_code"></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="2"></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
</div></div></div>
 <?php include '../../includes/footer.php'; ?>