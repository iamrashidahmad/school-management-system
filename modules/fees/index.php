<?php
/**
 * Fees Dashboard
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$pageTitle = 'Fee Management';
$activeMenu = 'fees';

$feeSummary = getFeeSummary();
$pendingCount = $conn->query("SELECT COUNT(*) as c FROM student_fees WHERE status IN ('Unpaid','Overdue')")->fetch_assoc()['c'];
$overdueCount = $conn->query("SELECT COUNT(*) as c FROM student_fees WHERE status='Overdue'")->fetch_assoc()['c'];

include '../../includes/header.php';
?>
<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-dollar-sign me-2 text-primary"></i>Fee Management</h1>
                <a href="collect.php" class="btn btn-success"><i class="fas fa-plus me-2"></i>Collect Fee</a>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6"><div class="card stat-card stat-card-success h-100"><div class="card-body"><div class="stat-label text-success">Total Collected</div><div class="stat-value"><?php echo formatCurrency($feeSummary['total_paid']??0); ?></div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="card stat-card stat-card-danger h-100"><div class="card-body"><div class="stat-label text-danger">Total Pending</div><div class="stat-value"><?php echo formatCurrency($feeSummary['total_balance']??0); ?></div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="card stat-card stat-card-warning h-100"><div class="card-body"><div class="stat-label text-warning">Unpaid</div><div class="stat-value"><?php echo $pendingCount; ?></div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="card stat-card stat-card-info h-100"><div class="card-body"><div class="stat-label text-info">Overdue</div><div class="stat-value"><?php echo $overdueCount; ?></div></div></div></div>
            </div>
            
            <div class="row g-3">
                <div class="col-md-3"><a href="structure.php" class="btn btn-outline-primary w-100 py-3"><i class="fas fa-list-alt d-block mb-1 fa-lg"></i><span class="small">Fee Structure</span></a></div>
                <div class="col-md-3"><a href="types.php" class="btn btn-outline-info w-100 py-3"><i class="fas fa-tags d-block mb-1 fa-lg"></i><span class="small">Fee Types</span></a></div>
                <div class="col-md-3"><a href="collect.php" class="btn btn-outline-success w-100 py-3"><i class="fas fa-dollar-sign d-block mb-1 fa-lg"></i><span class="small">Collect Fee</span></a></div>
                <div class="col-md-3"><a href="pending.php" class="btn btn-outline-danger w-100 py-3"><i class="fas fa-exclamation-circle d-block mb-1 fa-lg"></i><span class="small">Pending Fees</span></a></div>
            </div>
        </div>
        
    </div>
</div>
<?php include '../../includes/footer.php'; ?>