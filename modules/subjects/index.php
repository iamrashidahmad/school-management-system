<?php
/**
 * Subjects Module
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$pageTitle = 'Subjects';
$activeMenu = 'subjects';

$classFilter = intval($_GET['class_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $name = sanitize($_POST['subject_name'] ?? '');
        $code = sanitize($_POST['subject_code'] ?? '');
        $classId = intval($_POST['class_id'] ?? 0);
        $teacherId = intval($_POST['teacher_id'] ?? 0);
        $fullMarks = intval($_POST['full_marks'] ?? 100);
        $passMarks = intval($_POST['pass_marks'] ?? 40);
        
        if ($name && $code && $classId) {
            $stmt = $conn->prepare("INSERT INTO subjects (subject_name, subject_code, class_id, teacher_id, full_marks, pass_marks) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiiii", $name, $code, $classId, $teacherId, $fullMarks, $passMarks);
            if ($stmt->execute()) {
                logActivity(getCurrentUserId(), 'Subject Added', "Added subject: $name");
                redirect(BASE_URL.'modules/subjects/', 'success', 'Subject added.');
            }
        }
    }
}

$where = $classFilter ? "WHERE s.class_id = $classFilter" : "";
$subjects = $conn->query("SELECT s.*, c.class_name, t.first_name, t.last_name FROM subjects s LEFT JOIN classes c ON s.class_id=c.class_id LEFT JOIN teachers t ON s.teacher_id=t.teacher_id $where ORDER BY s.subject_name");
$classes = $conn->query("SELECT * FROM classes WHERE status=1 ORDER BY class_name_numeric");
$teachers = $conn->query("SELECT teacher_id, first_name, last_name FROM teachers WHERE status='active' ORDER BY first_name");

include '../../includes/header.php';
?>
<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-book me-2 text-primary"></i>Subjects</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-2"></i>Add Subject</button>
            </div>
            <?php showFlashMessage(); ?>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-2">
                        <div class="col-md-3">
                            <select class="form-select" name="class_id" onchange="this.form.submit()">
                                <option value="">All Classes</option>
                                <?php $classes->data_seek(0); while($c=$classes->fetch_assoc()): ?>
                                <option value="<?php echo $c['class_id']; ?>" <?php echo $classFilter==$c['class_id']?'selected':''; ?>><?php echo $c['class_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0"><thead><tr><th>Code</th><th>Name</th><th>Class</th><th>Teacher</th><th>Full Marks</th><th>Pass Marks</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php while($s=$subjects->fetch_assoc()): ?>
                            <tr><td class="font-monospace"><?php echo $s['subject_code']; ?></td><td><?php echo $s['subject_name']; ?></td><td><?php echo $s['class_name']; ?></td><td><?php echo $s['first_name'] ? $s['first_name'].' '.$s['last_name'] : '<span class="text-muted">Unassigned</span>'; ?></td><td><?php echo $s['full_marks']; ?></td><td><?php echo $s['pass_marks']; ?></td>
                            <td><a href="edit.php?id=<?php echo $s['subject_id']; ?>" class="btn btn-sm btn-primary btn-action"><i class="fas fa-edit"></i></a><a href="delete.php?id=<?php echo $s['subject_id']; ?>" class="btn btn-sm btn-danger btn-action btn-delete"><i class="fas fa-trash"></i></a></td></tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
</div>
<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Add Subject</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST"><?php echo csrfField(); ?>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Subject Name *</label><input type="text" class="form-control" name="subject_name" required></div>
            <div class="mb-3"><label class="form-label">Subject Code *</label><input type="text" class="form-control" name="subject_code" required></div>
            <div class="mb-3"><label class="form-label">Class *</label><select class="form-select" name="class_id" required><option value="">Select</option><?php $classes->data_seek(0); while($c=$classes->fetch_assoc()): ?><option value="<?php echo $c['class_id']; ?>"><?php echo $c['class_name']; ?></option><?php endwhile; ?></select></div>
            <div class="mb-3"><label class="form-label">Teacher</label><select class="form-select" name="teacher_id"><option value="">Select</option><?php $teachers->data_seek(0); while($t=$teachers->fetch_assoc()): ?><option value="<?php echo $t['teacher_id']; ?>"><?php echo $t['first_name'].' '.$t['last_name']; ?></option><?php endwhile; ?></select></div>
            <div class="row g-2"><div class="col"><label class="form-label">Full Marks</label><input type="number" class="form-control" name="full_marks" value="100"></div><div class="col"><label class="form-label">Pass Marks</label><input type="number" class="form-control" name="pass_marks" value="40"></div></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
</div></div></div>
<?php include '../../includes/footer.php'; ?>