<?php
/**
 * Create Exam
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$pageTitle = 'Create Exam';
$activeMenu = 'exams';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $examTypeId = intval($_POST['exam_type_id'] ?? 0);
        $classId = intval($_POST['class_id'] ?? 0);
        $sessionYear = sanitize($_POST['session_year'] ?? getCurrentSession());
        $semester = sanitize($_POST['semester_name'] ?? '');
        $examName = sanitize($_POST['exam_name'] ?? '');
        $startDate = sanitize($_POST['start_date'] ?? '');
        $endDate = sanitize($_POST['end_date'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');
        
        if ($examTypeId && $classId && $examName) {
            $stmt = $conn->prepare("INSERT INTO exams (exam_type_id, class_id, session_year, semester_name, exam_name, start_date, end_date, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissssssi", $examTypeId, $classId, $sessionYear, $semester, $examName, $startDate, $endDate, $notes, getCurrentUserId());
            $stmt->execute();
            $examId = $conn->insert_id;
            
            // Auto-add subjects
            if (isset($_POST['auto_subjects']) && $_POST['auto_subjects']) {
                $subjects = $conn->query("SELECT subject_id, full_marks FROM subjects WHERE class_id=$classId");
                while ($sub = $subjects->fetch_assoc()) {
                    $stmt2 = $conn->prepare("INSERT INTO exam_subjects (exam_id, subject_id, total_marks) VALUES (?, ?, ?)");
                    $stmt2->bind_param("iii", $examId, $sub['subject_id'], $sub['full_marks']);
                    $stmt2->execute();
                }
            }
            
            logActivity(getCurrentUserId(), 'Exam Created', "Created: $examName");
            redirect(BASE_URL.'modules/exams/', 'success', 'Exam created successfully.');
        }
    }
}

$examTypes = $conn->query("SELECT * FROM exam_types WHERE status=1");
$classes = $conn->query("SELECT * FROM classes WHERE status=1 ORDER BY class_name_numeric");

include '../../includes/header.php';
?>
<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-plus-circle me-2 text-primary"></i>Create Exam</h1>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <?php echo csrfField(); ?>
                        <div class="row g-3">
                            <div class="col-md-4"><label class="form-label">Exam Type *</label><select class="form-select" name="exam_type_id" required><option value="">Select</option><?php while($et=$examTypes->fetch_assoc()): ?><option value="<?php echo $et['exam_type_id']; ?>"><?php echo $et['exam_name']; ?> (<?php echo $et['percentage_weight']; ?>%)</option><?php endwhile; ?></select></div>
                            <div class="col-md-4"><label class="form-label">Class *</label><select class="form-select" name="class_id" required><option value="">Select</option><?php while($c=$classes->fetch_assoc()): ?><option value="<?php echo $c['class_id']; ?>"><?php echo $c['class_name']; ?></option><?php endwhile; ?></select></div>
                            <div class="col-md-4"><label class="form-label">Exam Name *</label><input type="text" class="form-control" name="exam_name" required placeholder="e.g. First Semester Exam 2025"></div>
                            <div class="col-md-3"><label class="form-label">Session Year</label><input type="text" class="form-control" name="session_year" value="<?php echo getCurrentSession(); ?>"></div>
                            <div class="col-md-3"><label class="form-label">Semester</label><input type="text" class="form-control" name="semester_name" placeholder="e.g. 1st Semester"></div>
                            <div class="col-md-3"><label class="form-label">Start Date</label><input type="date" class="form-control" name="start_date"></div>
                            <div class="col-md-3"><label class="form-label">End Date</label><input type="date" class="form-control" name="end_date"></div>
                            <div class="col-12"><label class="form-label">Notes</label><textarea class="form-control" name="notes" rows="2"></textarea></div>
                            <div class="col-12">
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="auto_subjects" value="1" checked id="auto"><label class="form-check-label" for="auto">Auto-add all subjects from this class</label></div>
                            </div>
                            <div class="col-12"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Create Exam</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</div>
<?php include '../../includes/footer.php'; ?>