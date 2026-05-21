<?php
/**
 * Exam Types Module
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$pageTitle = 'Exam Types';
$activeMenu = 'exams';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $name = sanitize($_POST['exam_name'] ?? '');
        $weight = floatval($_POST['percentage_weight'] ?? 0);
        $desc = sanitize($_POST['description'] ?? '');
        if ($name) {
            $stmt = $conn->prepare("INSERT INTO exam_types (exam_name, percentage_weight, description) VALUES (?, ?, ?)");
            $stmt->bind_param("sds", $name, $weight, $desc);
            $stmt->execute();
            logActivity(getCurrentUserId(), 'Exam Type Added', "Added: $name");
            redirect(BASE_URL.'modules/exams/types.php', 'success', 'Exam type added.');
        }
    }
}

$types = $conn->query("SELECT * FROM exam_types ORDER BY exam_type_id DESC");

include '../../includes/header.php';
?>
<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-list me-2 text-primary"></i>Exam Types</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-2"></i>Add Type</button>
            </div>
            <?php showFlashMessage(); ?>
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0"><thead><tr><th>Name</th><th>Weight (%)</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php while($t=$types->fetch_assoc()): ?>
                            <tr><td class="fw-bold"><?php echo $t['exam_name']; ?></td><td><?php echo $t['percentage_weight']; ?>%</td><td><?php echo $t['description']; ?></td><td><?php echo getStatusBadge($t['status']?'active':'inactive'); ?></td>
                            <td><a href="edit-type.php?id=<?php echo $t['exam_type_id']; ?>" class="btn btn-sm btn-primary btn-action"><i class="fas fa-edit"></i></a></td></tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
</div>
<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Add Exam Type</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST"><?php echo csrfField(); ?>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Exam Name *</label><input type="text" class="form-control" name="exam_name" required placeholder="e.g. 1st Semester"></div>
            <div class="mb-3"><label class="form-label">Percentage Weight *</label><input type="number" class="form-control" name="percentage_weight" step="0.01" min="0" max="100" required placeholder="e.g. 40"></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="2"></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
</div></div></div>
<?php include '../../includes/footer.php'; ?>