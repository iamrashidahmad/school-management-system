<?php
/**
 * Pending Fees
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$pageTitle = 'Pending Fees';
$activeMenu = 'fees';

$pending = $conn->query("SELECT sf.*, s.first_name, s.last_name, s.roll_no, c.class_name, ft.fee_name FROM student_fees sf JOIN students s ON sf.student_id=s.student_id JOIN classes c ON s.class_id=c.class_id JOIN fee_types ft ON sf.fee_type_id=ft.fee_type_id WHERE sf.status IN ('Unpaid','Partial','Overdue') ORDER BY sf.due_date ASC");

include '../../includes/header.php';
?>
<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-exclamation-circle me-2 text-primary"></i>Pending Fees</h1>
                <a href="collect.php" class="btn btn-success"><i class="fas fa-dollar-sign me-2"></i>Collect Fee</a>
            </div>
            <?php showFlashMessage(); ?>
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover datatable mb-0">
                            <thead><tr><th>Student</th><th>Roll #</th><th>Class</th><th>Fee Type</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Due Date</th><th>Status</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php while($p=$pending->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $p['first_name'].' '.$p['last_name']; ?></td>
                                    <td><?php echo $p['roll_no']; ?></td>
                                    <td><?php echo $p['class_name']; ?></td>
                                    <td><?php echo $p['fee_name']; ?></td>
                                    <td><?php echo formatCurrency($p['amount']); ?></td>
                                    <td><?php echo formatCurrency($p['paid_amount']); ?></td>
                                    <td class="fw-bold text-danger"><?php echo formatCurrency($p['balance_amount']); ?></td>
                                    <td><?php echo formatDate($p['due_date']); ?></td>
                                    <td><?php echo getStatusBadge($p['status']); ?></td>
                                    <td><a href="collect.php?student_id=<?php echo $p['student_id']; ?>" class="btn btn-sm btn-success"><i class="fas fa-dollar-sign"></i></a></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
<?php include '../../includes/footer.php'; ?>