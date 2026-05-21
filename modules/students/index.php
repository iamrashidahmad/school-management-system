<?php
/**
 * Students Module - List All Students
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();

$pageTitle = 'All Students';
$activeMenu = 'students';

// Get filter parameters
$classFilter = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$sectionFilter = isset($_GET['section_id']) ? intval($_GET['section_id']) : 0;
$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$where = "1=1";
$params = [];
$types = "";

if ($classFilter > 0) {
    $where .= " AND s.class_id = ?";
    $params[] = $classFilter;
    $types .= "i";
}
if ($sectionFilter > 0) {
    $where .= " AND s.section_id = ?";
    $params[] = $sectionFilter;
    $types .= "i";
}
if (!empty($statusFilter)) {
    $where .= " AND s.status = ?";
    $params[] = $statusFilter;
    $types .= "s";
}
if (!empty($search)) {
    $where .= " AND (s.first_name LIKE ? OR s.last_name LIKE ? OR s.admission_no LIKE ? OR s.roll_no LIKE ? OR s.email LIKE ?)";
    $searchParam = "%$search%";
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
    $types .= "sssss";
}

// Get students
$sql = "SELECT s.*, c.class_name, sec.section_name, p.father_name, u.is_active 
        FROM students s 
        LEFT JOIN classes c ON s.class_id = c.class_id 
        LEFT JOIN sections sec ON s.section_id = sec.section_id 
        LEFT JOIN parents p ON s.parent_id = p.parent_id 
        LEFT JOIN users u ON s.user_id = u.user_id 
        WHERE $where 
        ORDER BY s.created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$students = $stmt->get_result();

// Get classes for filter
$classes = $conn->query("SELECT * FROM classes WHERE status = 1 ORDER BY class_name_numeric");

include '../../includes/header.php';
?>

<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        
        <div class="container-fluid py-4">
            <!-- Page Header -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-user-graduate me-2 text-primary"></i>All Students
                </h1>
                <a href="add.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Student
                </a>
            </div>
            
            <?php showFlashMessage(); ?>
            
            <!-- Filters Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Search</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Name, Admission #, Roll #, Email">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Class</label>
                                <select class="form-select select2" name="class_id" onchange="this.form.submit()">
                                    <option value="">All Classes</option>
                                    <?php while ($class = $classes->fetch_assoc()): ?>
                                    <option value="<?php echo $class['class_id']; ?>" <?php echo $classFilter == $class['class_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($class['class_name']); ?>
                                    </option>
                                    <?php 
                                        endwhile;
                                        $classes->data_seek(0);
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Section</label>
                                <select class="form-select" name="section_id" onchange="this.form.submit()">
                                    <option value="">All Sections</option>
                                    <?php
                                    if ($classFilter > 0) {
                                        $secStmt = $conn->prepare("SELECT * FROM sections WHERE class_id = ? AND status = 1 ORDER BY section_name");
                                        $secStmt->bind_param("i", $classFilter);
                                        $secStmt->execute();
                                        $sections = $secStmt->get_result();
                                        while ($section = $sections->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $section['section_id']; ?>" <?php echo $sectionFilter == $section['section_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($section['section_name']); ?>
                                    </option>
                                    <?php endwhile; } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Status</label>
                                <select class="form-select" name="status" onchange="this.form.submit()">
                                    <option value="">All Status</option>
                                    <option value="active" <?php echo $statusFilter == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $statusFilter == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="graduated" <?php echo $statusFilter == 'graduated' ? 'selected' : ''; ?>>Graduated</option>
                                    <option value="transferred" <?php echo $statusFilter == 'transferred' ? 'selected' : ''; ?>>Transferred</option>
                                    <option value="suspended" <?php echo $statusFilter == 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2"><i class="fas fa-filter me-1"></i> Filter</button>
                                <a href="index.php" class="btn btn-secondary"><i class="fas fa-undo me-1"></i> Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Students Table -->
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-list me-2"></i>Student List
                    </h6>
                    <div>
                        <a href="import.php" class="btn btn-sm btn-outline-success me-1">
                            <i class="fas fa-file-import me-1"></i>Import
                        </a>
                        <a href="export.php?<?php echo http_build_query($_GET); ?>" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-file-export me-1"></i>Export
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover datatable mb-0" id="studentsTable">
                            <thead>
                                <tr>
                                    <th width="50">Photo</th>
                                    <th>Admission #</th>
                                    <th>Roll #</th>
                                    <th>Name</th>
                                    <th>Class</th>
                                    <th>Section</th>
                                    <th>Gender</th>
                                    <th>Parent/Guardian</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($student = $students->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <img src="../../assets/uploads/students/<?php echo $student['photo']; ?>" 
                                             alt="" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;"
                                             onerror="this.src='../../assets/images/default-user.png'">
                                    </td>
                                    <td class="font-monospace fw-bold"><?php echo htmlspecialchars($student['admission_no']); ?></td>
                                    <td><?php echo htmlspecialchars($student['roll_no']); ?></td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($student['email']); ?></div>
                                    </td>
                                    <td><?php echo htmlspecialchars($student['class_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['section_name']); ?></td>
                                    <td>
                                        <i class="fas fa-<?php echo $student['gender'] == 'Male' ? 'mars text-primary' : ($student['gender'] == 'Female' ? 'venus text-danger' : 'genderless text-secondary'); ?>"></i>
                                        <?php echo $student['gender']; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($student['father_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                    <td><?php echo getStatusBadge($student['status']); ?></td>
                                    <td>
                                        <a href="view.php?id=<?php echo $student['student_id']; ?>" class="btn btn-sm btn-info btn-action" data-bs-toggle="tooltip" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit.php?id=<?php echo $student['student_id']; ?>" class="btn btn-sm btn-primary btn-action" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete.php?id=<?php echo $student['student_id']; ?>" class="btn btn-sm btn-danger btn-action btn-delete" data-bs-toggle="tooltip" title="Delete" 
                                           data-title="Delete Student" data-text="Are you sure you want to delete <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>? This will also delete all associated records.">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if ($students->num_rows == 0): ?>
                                <tr>
                                    <td colspan="11" class="text-center py-5 text-muted">
                                        <i class="fas fa-user-graduate fa-2x mb-2"></i>
                                        <p>No students found. <a href="add.php">Add a student</a></p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
<script>
$(document).ready(function() {
    initDataTable('#studentsTable', {
        pageLength: 25,
        columnDefs: [
            { orderable: false, targets: [0, 10] }
        ]
    });
});
</script>
