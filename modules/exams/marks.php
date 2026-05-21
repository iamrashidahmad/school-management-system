<?php
/**
 * Enter Marks
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$pageTitle = 'Enter Marks';
$activeMenu = 'exams';

$examId = intval($_GET['exam_id'] ?? 0);
$classId = intval($_GET['class_id'] ?? 0);

if (!$examId) {
    redirect(BASE_URL.'modules/exams/', 'error', 'Please select an exam.');
}

// Get exam info
$exam = $conn->query("SELECT e.*, c.class_name FROM exams e JOIN classes c ON e.class_id=c.class_id WHERE e.exam_id=$examId")->fetch_assoc();
if (!$exam) {
    redirect(BASE_URL.'modules/exams/', 'error', 'Exam not found.');
}

// Get exam subjects
$examSubjects = $conn->query("SELECT es.*, s.subject_name FROM exam_subjects es JOIN subjects s ON es.subject_id=s.subject_id WHERE es.exam_id=$examId ORDER BY s.subject_name");

// Save marks
if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $marks = $_POST['marks'] ?? [];
    foreach ($marks as $studentId => $subjects) {
        $sid = intval($studentId);
        foreach ($subjects as $examSubjectId => $data) {
            $esid = intval($examSubjectId);
            $obtained = floatval($data['obtained'] ?? 0);
            $remarks = sanitize($data['remarks'] ?? '');
            
            $esData = $conn->query("SELECT total_marks, subject_id FROM exam_subjects WHERE exam_subject_id=$esid")->fetch_assoc();
            $totalMarks = $esData['total_marks'];
            $subjectId = $esData['subject_id'];
            $percentage = $totalMarks > 0 ? round(($obtained / $totalMarks) * 100, 2) : 0;
            $grade = calculateGrade($percentage)['grade_name'];
            
            $check = $conn->prepare("SELECT mark_id FROM student_marks WHERE student_id=? AND exam_id=? AND exam_subject_id=?");
            $check->bind_param("iii", $sid, $examId, $esid);
            $check->execute();
            
            if ($check->get_result()->num_rows > 0) {
                $upd = $conn->prepare("UPDATE student_marks SET obtained_marks=?, total_marks=?, percentage=?, grade=?, remarks=?, entered_by=? WHERE student_id=? AND exam_id=? AND exam_subject_id=?");
                $upd->bind_param("ddddsiiii", $obtained, $totalMarks, $percentage, $grade, $remarks, getCurrentUserId(), $sid, $examId, $esid);
                $upd->execute();
            } else {
                $ins = $conn->prepare("INSERT INTO student_marks (student_id, exam_id, exam_subject_id, subject_id, obtained_marks, total_marks, percentage, grade, remarks, entered_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $ins->bind_param("iiiiiddssi", $sid, $examId, $esid, $subjectId, $obtained, $totalMarks, $percentage, $grade, $remarks, getCurrentUserId());
                $ins->execute();
            }
        }
    }
    logActivity(getCurrentUserId(), 'Marks Entered', "Entered marks for Exam: {$exam['exam_name']}");
    redirect(BASE_URL.'modules/exams/marks.php?exam_id='.$examId, 'success', 'Marks saved successfully.');
}

// Get students and existing marks
$students = $conn->query("SELECT s.student_id, s.first_name, s.last_name, s.roll_no FROM students s WHERE s.class_id={$exam['class_id']} AND s.status='active' ORDER BY s.roll_no");

$existingMarks = [];
$marksResult = $conn->query("SELECT * FROM student_marks WHERE exam_id=$examId");
while ($m = $marksResult->fetch_assoc()) {
    $existingMarks[$m['student_id']][$m['exam_subject_id']] = $m;
}

include '../../includes/header.php';
?>
<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-pen me-2 text-primary"></i>Enter Marks</h1>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
            </div>
            <?php showFlashMessage(); ?>
            
            <div class="alert alert-info">
                <strong><?php echo $exam['exam_name']; ?></strong> | Class: <?php echo $exam['class_name']; ?> | Session: <?php echo $exam['session_year']; ?>
            </div>
            
            <form method="POST">
                <?php echo csrfField(); ?>
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Roll #</th>
                                        <th>Student</th>
                                        <?php while($es=$examSubjects->fetch_assoc()): ?>
                                        <th><?php echo $es['subject_name']; ?><br><small class="text-muted">(<?php echo $es['total_marks']; ?>)</small></th>
                                        <?php endwhile; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($st=$students->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $st['roll_no']; ?></td>
                                        <td><?php echo $st['first_name'].' '.$st['last_name']; ?></td>
                                        <?php 
                                        $examSubjects->data_seek(0);
                                        while($es=$examSubjects->fetch_assoc()): 
                                            $existing = $existingMarks[$st['student_id']][$es['exam_subject_id']] ?? null;
                                        ?>
                                        <td>
                                            <input type="number" class="form-control form-control-sm" style="width:70px;"
                                                   name="marks[<?php echo $st['student_id']; ?>][<?php echo $es['exam_subject_id']; ?>][obtained]"
                                                   value="<?php echo $existing['obtained_marks'] ?? ''; ?>" 
                                                   min="0" max="<?php echo $es['total_marks']; ?>" step="0.01">
                                            <input type="hidden" name="marks[<?php echo $st['student_id']; ?>][<?php echo $es['exam_subject_id']; ?>][remarks]" value="">
                                        </td>
                                        <?php endwhile; ?>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save All Marks</button>
                        <a href="calculate-results.php?exam_id=<?php echo $examId; ?>" class="btn btn-success ms-2"><i class="fas fa-calculator me-2"></i>Calculate Results</a>
                    </div>
                </div>
            </form>
        </div>
      
    </div>
</div>
  <?php include '../../includes/footer.php'; ?>