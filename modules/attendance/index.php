<?php
/**
 * Student Attendance
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$pageTitle = 'Student Attendance';
$activeMenu = 'attendance';

$classId = intval($_GET['class_id'] ?? 0);
$sectionId = intval($_GET['section_id'] ?? 0);
$date = sanitize($_GET['date'] ?? date('Y-m-d'));

// Save attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $attDate = sanitize($_POST['attendance_date']);
        $statuses = $_POST['status'] ?? [];
        $remarks = $_POST['remarks'] ?? [];
        
        foreach ($statuses as $studentId => $status) {
            $remark = sanitize($remarks[$studentId] ?? '');
            $sid = intval($studentId);
            
            // Check if record exists
            $check = $conn->prepare("SELECT attendance_id FROM attendance WHERE student_id=? AND attendance_date=?");
            $check->bind_param("is", $sid, $attDate);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $upd = $conn->prepare("UPDATE attendance SET status=?, remarks=?, marked_by=? WHERE student_id=? AND attendance_date=?");
                $upd->bind_param("ssiis", $status, $remark, getCurrentUserId(), $sid, $attDate);
                $upd->execute();
            } else {
                $ins = $conn->prepare("INSERT INTO attendance (student_id, class_id, section_id, attendance_date, status, remarks, marked_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $ins->bind_param("iiisssi", $sid, $classId, $sectionId, $attDate, $status, $remark, getCurrentUserId());
                $ins->execute();
            }
        }
        logActivity(getCurrentUserId(), 'Attendance Marked', "Marked attendance for Class ID: $classId on $attDate");
        redirect(BASE_URL.'modules/attendance/', 'success', 'Attendance saved successfully.');
    }
}

$classes = $conn->query("SELECT * FROM classes WHERE status=1 ORDER BY class_name_numeric");
$sections = $conn->query("SELECT * FROM sections WHERE class_id=$classId AND status=1 ORDER BY section_name");

$students = null;
$existingAttendance = [];
if ($classId > 0) {
    $stmt = $conn->prepare("SELECT s.student_id, s.first_name, s.last_name, s.roll_no, s.photo FROM students s WHERE s.class_id=? AND (?=0 OR s.section_id=?) AND s.status='active' ORDER BY s.roll_no, s.first_name");
    $stmt->bind_param("iii", $classId, $sectionId, $sectionId);
    $stmt->execute();
    $students = $stmt->get_result();
    
    // Get existing attendance for the date
    $existStmt = $conn->prepare("SELECT student_id, status, remarks FROM attendance WHERE class_id=? AND attendance_date=?");
    $existStmt->bind_param("is", $classId, $date);
    $existStmt->execute();
    $existResult = $existStmt->get_result();
    while ($row = $existResult->fetch_assoc()) {
        $existingAttendance[$row['student_id']] = $row;
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
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-clipboard-check me-2 text-primary"></i>Student Attendance</h1>
                <a href="report.php" class="btn btn-info"><i class="fas fa-chart-bar me-2"></i>Attendance Report</a>
            </div>
            <?php showFlashMessage(); ?>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Class *</label>
                            <select class="form-select class-select" name="class_id" required onchange="this.form.submit()">
                                <option value="">Select Class</option>
                                <?php $classes->data_seek(0); while($c=$classes->fetch_assoc()): ?>
                                <option value="<?php echo $c['class_id']; ?>" <?php echo $classId==$c['class_id']?'selected':''; ?>><?php echo $c['class_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Section</label>
                            <select class="form-select section-select" name="section_id" onchange="this.form.submit()">
                                <option value="">All Sections</option>
                                <?php while($s=$sections->fetch_assoc()): ?>
                                <option value="<?php echo $s['section_id']; ?>" <?php echo $sectionId==$s['section_id']?'selected':''; ?>><?php echo $s['section_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date *</label>
                            <input type="date" class="form-control" name="date" value="<?php echo $date; ?>" onchange="this.form.submit()">
                        </div>
                    </form>
                </div>
            </div>
            
            <?php if ($classId > 0 && $students): ?>
            <form method="POST">
                <?php echo csrfField(); ?>
                <input type="hidden" name="attendance_date" value="<?php echo $date; ?>">
                
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="fas fa-users me-2"></i>Mark Attendance - <?php echo $students->num_rows; ?> Students
                        </h6>
                        <div class="small text-muted"><?php echo date('l, d F Y', strtotime($date)); ?></div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead><tr><th width="50">#</th><th>Photo</th><th>Roll #</th><th>Name</th><th width="300">Status</th><th>Remarks</th></tr></thead>
                                <tbody>
                                    <?php $i=1; while($st=$students->fetch_assoc()): 
                                        $existing = $existingAttendance[$st['student_id']] ?? null;
                                        $currentStatus = $existing['status'] ?? 'Present';
                                    ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><img src="../../assets/uploads/students/<?php echo $st['photo']; ?>" class="rounded-circle" style="width:35px;height:35px;object-fit:cover;" onerror="this.src='../../assets/images/default-user.png'"></td>
                                        <td><?php echo $st['roll_no']; ?></td>
                                        <td><?php echo $st['first_name'].' '.$st['last_name']; ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php foreach(['Present','Absent','Late','Half Day','On Leave'] as $status): ?>
                                                <input type="radio" class="btn-check" name="status[<?php echo $st['student_id']; ?>]" id="st<?php echo $st['student_id'].$status; ?>" value="<?php echo $status; ?>" <?php echo $currentStatus==$status?'checked':''; ?>>
                                                <label class="btn btn-outline-<?php echo $status=='Present'?'success':($status=='Absent'?'danger':($status=='Late'?'warning':($status=='Half Day'?'info':'primary'))); ?> btn-sm" for="st<?php echo $st['student_id'].$status; ?>"><?php echo substr($status,0,1); ?></label>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                        <td><input type="text" class="form-control form-control-sm" name="remarks[<?php echo $st['student_id']; ?>]" value="<?php echo $existing['remarks'] ?? ''; ?>" placeholder="Remarks"></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Attendance</button>
                    </div>
                </div>
            </form>
            <?php elseif ($classId > 0): ?>
            <div class="alert alert-info">No students found in this class.</div>
            <?php endif; ?>
        </div>
        
    </div>
</div>
<?php include '../../includes/footer.php'; ?>