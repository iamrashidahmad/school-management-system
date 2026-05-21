<?php
/**
 * Exam Results
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$pageTitle = 'Exam Results';
$activeMenu = 'exams';

$examId = intval($_GET['exam_id'] ?? 0);

$exam = $conn->query("SELECT e.*, c.class_name, et.exam_name as type_name FROM exams e JOIN classes c ON e.class_id=c.class_id JOIN exam_types et ON e.exam_type_id=et.exam_type_id WHERE e.exam_id=$examId")->fetch_assoc();

$where = $examId ? "WHERE r.exam_id=$examId" : "";
$results = $conn->query("SELECT r.*, s.first_name, s.last_name, s.roll_no, c.class_name FROM results r JOIN students s ON r.student_id=s.student_id JOIN classes c ON r.class_id=c.class_id $where ORDER BY r.class_position ASC, r.percentage DESC");

include '../../includes/header.php';
?>
<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chart-bar me-2 text-primary"></i>Exam Results</h1>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
            </div>
            <?php showFlashMessage(); ?>
            <?php if ($exam): ?>
            <div class="alert alert-info"><strong><?php echo $exam['exam_name']; ?></strong> | <?php echo $exam['class_name']; ?> | <?php echo $exam['type_name']; ?> | Session: <?php echo $exam['session_year']; ?></div>
            <?php endif; ?>
            
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Position</th><th>Roll #</th><th>Student</th><th>Obtained</th><th>Total</th><th>Percentage</th><th>Grade</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php while($r=$results->fetch_assoc()): ?>
                                <tr>
                                    <td><span class="badge bg-<?php echo $r['class_position']==1?'warning':($r['class_position']==2?'secondary':($r['class_position']==3?'info':'light')); ?>"><?php echo $r['class_position']; ?></span></td>
                                    <td><?php echo $r['roll_no']; ?></td>
                                    <td><?php echo $r['first_name'].' '.$r['last_name']; ?></td>
                                    <td><?php echo $r['total_obtained']; ?></td>
                                    <td><?php echo $r['total_marks']; ?></td>
                                    <td><?php echo $r['percentage']; ?>%</td>
                                    <td class="fw-bold"><?php echo $r['grade']; ?></td>
                                    <td><?php echo getStatusBadge($r['status']); ?></td>
                                    <td><a href="report-cards.php?student_id=<?php echo $r['student_id']; ?>" class="btn btn-sm btn-primary btn-action"><i class="fas fa-file-alt"></i></a></td>
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