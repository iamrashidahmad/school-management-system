<?php
/**
 * Fee Structure
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$pageTitle = 'Fee Structure';
$activeMenu = 'fees';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $classId = intval($_POST['class_id'] ?? 0);
        $feeTypeId = intval($_POST['fee_type_id'] ?? 0);
        $amount = floatval($_POST['amount'] ?? 0);
        $frequency = sanitize($_POST['frequency'] ?? 'Monthly');
        $academicYear = sanitize($_POST['academic_year'] ?? getCurrentSession());
        
        if ($classId && $feeTypeId && $amount > 0) {
            $stmt = $conn->prepare("INSERT INTO fee_structure (class_id, fee_type_id, amount, frequency, academic_year) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iidss", $classId, $feeTypeId, $amount, $frequency, $academicYear);
            $stmt->execute();
            redirect(BASE_URL.'modules/fees/structure.php', 'success', 'Fee structure added.');
        }
    }
}

$structures = $conn->query("SELECT fs.*, c.class_name, ft.fee_name FROM fee_structure fs JOIN classes c ON fs.class_id=c.class_id JOIN fee_types ft ON fs.fee_type_id=ft.fee_type_id ORDER BY c.class_name_numeric, ft.fee_name");
$classes = $conn->query("SELECT * FROM classes WHERE status=1 ORDER BY class_name_numeric");
$feeTypes = $conn->query("SELECT * FROM fee_types WHERE status=1");

include '../../includes/header.php';
?>
<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-list-alt me-2 text-primary"></i>Fee Structure</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-2"></i>Add</button>
            </div>
            <?php showFlashMessage(); ?>
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0"><thead><tr><th>Class</th><th>Fee Type</th><th>Amount</th><th>Frequency</th><th>Academic Year</th></tr></thead>
                        <tbody><?php while($s=$structures->fetch_assoc()): ?><tr><td><?php echo $s['class_name']; ?></td><td><?php echo $s['fee_name']; ?></td><td class="fw-bold"><?php echo formatCurrency($s['amount']); ?></td><td><?php echo $s['frequency']; ?></td><td><?php echo $s['academic_year']; ?></td></tr><?php endwhile; ?></tbody>
                    </table>
                </div>
            </div>
        </div>
       
    </div>
</div>
<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5>Add Fee Structure</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST"><?php echo csrfField(); ?>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Class *</label><select class="form-select" name="class_id" required><option value="">Select</option><?php $classes->data_seek(0); while($c=$classes->fetch_assoc()): ?><option value="<?php echo $c['class_id']; ?>"><?php echo $c['class_name']; ?></option><?php endwhile; ?></select></div>
            <div class="mb-3"><label class="form-label">Fee Type *</label><select class="form-select" name="fee_type_id" required><option value="">Select</option><?php $feeTypes->data_seek(0); while($ft=$feeTypes->fetch_assoc()): ?><option value="<?php echo $ft['fee_type_id']; ?>"><?php echo $ft['fee_name']; ?></option><?php endwhile; ?></select></div>
            <div class="mb-3"><label class="form-label">Amount *</label><input type="number" class="form-control" name="amount" step="0.01" required></div>
            <div class="mb-3"><label class="form-label">Frequency</label><select class="form-select" name="frequency"><option value="Monthly">Monthly</option><option value="Quarterly">Quarterly</option><option value="Half Yearly">Half Yearly</option><option value="Yearly">Yearly</option><option value="One Time">One Time</option></select></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
</div></div></div>
 <?php include '../../includes/footer.php'; ?>