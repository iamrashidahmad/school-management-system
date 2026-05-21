<?php
/**
 * Report Cards - View & Print
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$pageTitle = 'Report Cards';
$activeMenu = 'exams';

$studentId = intval($_GET['student_id'] ?? 0);
$sessionYear = sanitize($_GET['session_year'] ?? getCurrentSession());

$school = getSchoolInfo();

$students = $conn->query("SELECT s.student_id, s.first_name, s.last_name, s.admission_no, s.roll_no, s.date_of_birth, s.photo, c.class_name, sec.section_name FROM students s JOIN classes c ON s.class_id=c.class_id LEFT JOIN sections sec ON s.section_id=sec.section_id WHERE s.status='active' ORDER BY s.first_name");

$reportData = null;
if ($studentId) {
    $stmt = $conn->prepare("SELECT s.*, c.class_name, sec.section_name, p.father_name FROM students s JOIN classes c ON s.class_id=c.class_id LEFT JOIN sections sec ON s.section_id=sec.section_id LEFT JOIN parents p ON s.parent_id=p.parent_id WHERE s.student_id=?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $studentInfo = $stmt->get_result()->fetch_assoc();
    
    $results = $conn->query("SELECT r.*, e.exam_name, et.exam_name as type_name, et.percentage_weight, sm.obtained_marks, sm.total_marks as sub_total, sm.percentage as sub_pct, sub.subject_name FROM results r JOIN exams e ON r.exam_id=e.exam_id JOIN exam_types et ON e.exam_type_id=et.exam_type_id JOIN student_marks sm ON r.exam_id=sm.exam_id AND r.student_id=sm.student_id JOIN exam_subjects es ON sm.exam_subject_id=es.exam_subject_id JOIN subjects sub ON sm.subject_id=sub.subject_id WHERE r.student_id=$studentId AND e.session_year='$sessionYear' AND e.publish_result=1 ORDER BY e.start_date, sub.subject_name");
}

include '../../includes/header.php';
?>
<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-alt me-2 text-primary"></i>Report Cards</h1>
                <?php if ($studentId): ?><button class="btn btn-primary no-print" onclick="printSection('reportCard')"><i class="fas fa-print me-2"></i>Print</button><?php endif; ?>
            </div>
            
            <div class="card shadow-sm mb-4 no-print">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Student</label>
                            <select class="form-select select2" name="student_id" required onchange="this.form.submit()">
                                <option value="">Select Student</option>
                                <?php while($st=$students->fetch_assoc()): ?>
                                <option value="<?php echo $st['student_id']; ?>" <?php echo $studentId==$st['student_id']?'selected':''; ?>><?php echo $st['first_name'].' '.$st['last_name'].' ('.$st['roll_no'].')'; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Session</label>
                            <input type="text" class="form-control" name="session_year" value="<?php echo $sessionYear; ?>">
                        </div>
                        <div class="col-md-2"><button type="submit" class="btn btn-primary">View</button></div>
                    </form>
                </div>
            </div>
            
            <?php if ($studentId && isset($studentInfo) && $studentInfo): ?>
            <div id="reportCard">
                <div class="report-card">
                    <!-- Header -->
                    <div class="report-card-header">
                        <h2><?php echo $school['school_name']; ?></h2>
                        <p><?php echo $school['school_address']; ?></p>
                        <p>Phone: <?php echo $school['school_phone']; ?> | Email: <?php echo $school['school_email']; ?></p>
                        <h4 class="mt-3 fw-bold border-bottom border-top py-2">ANNUAL REPORT CARD</h4>
                        <p class="mb-0">Session: <?php echo $sessionYear; ?></p>
                    </div>
                    
                    <!-- Student Info -->
                    <div class="report-card-student-info">
                        <div class="info-item"><div class="label">Student Name</div><div class="value"><?php echo $studentInfo['first_name'].' '.$studentInfo['last_name']; ?></div></div>
                        <div class="info-item"><div class="label">Roll Number</div><div class="value"><?php echo $studentInfo['roll_no']; ?></div></div>
                        <div class="info-item"><div class="label">Admission No</div><div class="value"><?php echo $studentInfo['admission_no']; ?></div></div>
                        <div class="info-item"><div class="label">Class</div><div class="value"><?php echo $studentInfo['class_name']; ?> - <?php echo $studentInfo['section_name']; ?></div></div>
                        <div class="info-item"><div class="label">Father Name</div><div class="value"><?php echo $studentInfo['father_name'] ?? 'N/A'; ?></div></div>
                        <div class="info-item"><div class="label">Date of Birth</div><div class="value"><?php echo formatDate($studentInfo['date_of_birth']); ?></div></div>
                    </div>
                    
                    <!-- Results Table -->
                    <table class="report-table">
                        <thead>
                            <tr><th>S.No</th><th>Subject</th><th>Exam</th><th>Obtained</th><th>Total</th><th>%</th><th>Grade</th></tr>
                        </thead>
                        <tbody>
                            <?php $i=1; $grandObtained=0; $grandTotal=0; while($r=$results->fetch_assoc()): $grandObtained+=$r['obtained_marks']; $grandTotal+=$r['sub_total']; ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td class="text-start"><?php echo $r['subject_name']; ?></td>
                                <td><?php echo $r['type_name']; ?></td>
                                <td><?php echo $r['obtained_marks']; ?></td>
                                <td><?php echo $r['sub_total']; ?></td>
                                <td><?php echo $r['sub_pct']; ?>%</td>
                                <td><?php echo calculateGrade($r['sub_pct'])['grade_name']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold" style="background:#f0f0f0;">
                                <td colspan="3" class="text-end">Grand Total:</td>
                                <td><?php echo $grandObtained; ?></td>
                                <td><?php echo $grandTotal; ?></td>
                                <td colspan="2"><?php echo $grandTotal > 0 ? round(($grandObtained/$grandTotal)*100,2) : 0; ?>%</td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <!-- Summary -->
                    <?php
                    $summary = $conn->query("SELECT * FROM results WHERE student_id=$studentId ORDER BY percentage DESC");
                    $resultSummary = $summary->fetch_assoc();
                    ?>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4 text-center"><div class="p-3 bg-light rounded"><div class="fw-bold text-primary h4"><?php echo $resultSummary['grade'] ?? 'N/A'; ?></div><div class="small">Grade</div></div></div>
                        <div class="col-md-4 text-center"><div class="p-3 bg-light rounded"><div class="fw-bold text-success h4"><?php echo $resultSummary['percentage'] ?? 0; ?>%</div><div class="small">Percentage</div></div></div>
                        <div class="col-md-4 text-center"><div class="p-3 bg-light rounded"><div class="fw-bold text-info h4"><?php echo $resultSummary['class_position'] ?? 'N/A'; ?></div><div class="small">Position</div></div></div>
                    </div>
                    
                    <!-- Remarks -->
                    <div class="mb-4"><strong>Remarks:</strong> <?php echo $resultSummary['remarks'] ?? ($resultSummary['status'] ?? 'Pass'); ?></div>
                    
                    <!-- Signatures -->
                    <div class="signatures">
                        <div class="signature-line"><div class="line">Class Teacher</div></div>
                        <div class="signature-line"><div class="line">Controller of Examinations</div></div>
                        <div class="signature-line"><div class="line">Principal</div></div>
                    </div>
                    <div class="text-center mt-3 small text-muted">Date of Issue: <?php echo date('d F Y'); ?></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
    </div>
</div>
<?php include '../../includes/footer.php'; ?>