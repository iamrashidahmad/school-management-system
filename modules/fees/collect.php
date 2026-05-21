<?php
/**
 * Collect Fee
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$pageTitle = 'Collect Fee';
$activeMenu = 'fees';

$studentId = intval($_GET['student_id'] ?? 0);
$feeStructureId = intval($_GET['fee_structure_id'] ?? 0);

$students = $conn->query("SELECT s.student_id, s.first_name, s.last_name, s.roll_no, c.class_name FROM students s JOIN classes c ON s.class_id=c.class_id WHERE s.status='active' ORDER BY s.first_name");

$student = null;
$feeDetails = null;
$pendingFees = [];

if ($studentId) {
    $stmt = $conn->prepare("SELECT s.*, c.class_name, p.father_name FROM students s JOIN classes c ON s.class_id=c.class_id LEFT JOIN parents p ON s.parent_id=p.parent_id WHERE s.student_id=?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    
    // Get pending fees
    $pendingStmt = $conn->prepare("SELECT sf.*, ft.fee_name FROM student_fees sf JOIN fee_types ft ON sf.fee_type_id=ft.fee_type_id WHERE sf.student_id=? AND sf.status IN ('Unpaid','Partial','Overdue')");
    $pendingStmt->bind_param("i", $studentId);
    $pendingStmt->execute();
    $pendingFees = $pendingStmt->get_result();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $feeId = intval($_POST['fee_id'] ?? 0);
        $amount = floatval($_POST['amount'] ?? 0);
        $method = sanitize($_POST['payment_method'] ?? 'Cash');
        $notes = sanitize($_POST['notes'] ?? '');
        
        if ($feeId && $amount > 0) {
            $receiptNo = 'RCP-' . time() . '-' . mt_rand(100,999);
            
            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("INSERT INTO fee_payments (fee_id, student_id, amount, payment_method, receipt_no, payment_date, collected_by, notes) VALUES (?, ?, ?, ?, ?, CURDATE(), ?, ?)");
                $stmt->bind_param("iidssis", $feeId, $studentId, $amount, $method, $receiptNo, getCurrentUserId(), $notes);
                $stmt->execute();
                
                // Update fee record
                $upd = $conn->prepare("UPDATE student_fees SET paid_amount = paid_amount + ?, balance_amount = balance_amount - ? WHERE fee_id=?");
                $upd->bind_param("ddi", $amount, $amount, $feeId);
                $upd->execute();
                
                // Update status
                $conn->query("UPDATE student_fees SET status = CASE WHEN balance_amount <= 0 THEN 'Paid' WHEN paid_amount > 0 THEN 'Partial' ELSE status END WHERE fee_id=$feeId");
                
                $conn->commit();
                logActivity(getCurrentUserId(), 'Fee Collected', "Collected fee: $amount, Receipt: $receiptNo");
                redirect(BASE_URL.'modules/fees/collect.php?student_id='.$studentId, 'success', "Payment received! Receipt: $receiptNo");
            } catch (Exception $e) {
                $conn->rollback();
                $error = 'Error: ' . $e->getMessage();
            }
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
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-dollar-sign me-2 text-primary"></i>Collect Fee</h1>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
            </div>
            <?php showFlashMessage(); ?>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Select Student *</label>
                            <select class="form-select select2" name="student_id" required onchange="this.form.submit()">
                                <option value="">Choose Student</option>
                                <?php $students->data_seek(0); while($st=$students->fetch_assoc()): ?>
                                <option value="<?php echo $st['student_id']; ?>" <?php echo $studentId==$st['student_id']?'selected':''; ?>><?php echo $st['first_name'].' '.$st['last_name'].' ('.$st['roll_no'].') - '.$st['class_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php if ($student): ?>
            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <img src="../../assets/uploads/students/<?php echo $student['photo']; ?>" class="rounded-circle mb-2" style="width:80px;height:80px;object-fit:cover;" onerror="this.src='../../assets/images/default-user.png'">
                            <h5><?php echo $student['first_name'].' '.$student['last_name']; ?></h5>
                            <p class="text-muted mb-1"><?php echo $student['class_name']; ?></p>
                            <p class="text-muted small">Father: <?php echo $student['father_name'] ?? 'N/A'; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header"><h6 class="mb-0 fw-bold text-primary"><i class="fas fa-money-bill me-2"></i>Pending Fees</h6></div>
                        <div class="card-body p-0">
                            <?php if ($pendingFees && $pendingFees->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead><tr><th>Fee Type</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Due Date</th><th>Pay</th></tr></thead>
                                    <tbody>
                                        <?php while($pf=$pendingFees->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $pf['fee_name']; ?></td>
                                            <td><?php echo formatCurrency($pf['amount']); ?></td>
                                            <td><?php echo formatCurrency($pf['paid_amount']); ?></td>
                                            <td class="fw-bold text-danger"><?php echo formatCurrency($pf['balance_amount']); ?></td>
                                            <td><?php echo formatDate($pf['due_date']); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#payModal<?php echo $pf['fee_id']; ?>"><i class="fas fa-dollar-sign"></i></button>
                                            </td>
                                        </tr>
                                        <!-- Payment Modal -->
                                        <div class="modal fade" id="payModal<?php echo $pf['fee_id']; ?>" tabindex="-1">
                                            <div class="modal-dialog"><div class="modal-content">
                                                <div class="modal-header"><h5 class="modal-title">Pay - <?php echo $pf['fee_name']; ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                <form method="POST">
                                                    <?php echo csrfField(); ?>
                                                    <input type="hidden" name="fee_id" value="<?php echo $pf['fee_id']; ?>">
                                                    <div class="modal-body">
                                                        <div class="mb-3"><label class="form-label">Amount Due</label><input type="text" class="form-control" value="<?php echo formatCurrency($pf['balance_amount']); ?>" disabled></div>
                                                        <div class="mb-3"><label class="form-label">Pay Amount *</label><input type="number" class="form-control" name="amount" max="<?php echo $pf['balance_amount']; ?>" step="0.01" required></div>
                                                        <div class="mb-3"><label class="form-label">Payment Method</label><select class="form-select" name="payment_method"><option value="Cash">Cash</option><option value="Bank Transfer">Bank Transfer</option><option value="Check">Check</option><option value="Online">Online</option></select></div>
                                                        <div class="mb-3"><label class="form-label">Notes</label><textarea class="form-control" name="notes" rows="2"></textarea></div>
                                                    </div>
                                                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Pay Now</button></div>
                                                </form>
                                            </div></div>
                                        </div>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4 text-success"><i class="fas fa-check-circle fa-2x mb-2"></i><p>No pending fees!</p></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
       
    </div>
</div>
 <?php include '../../includes/footer.php'; ?>