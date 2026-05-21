<?php
/**
 * Classes Module
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$pageTitle = 'Classes';
$activeMenu = 'classes';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $name = sanitize($_POST['class_name'] ?? '');
        $numeric = intval($_POST['class_name_numeric'] ?? 0);
        $capacity = intval($_POST['capacity'] ?? 40);
        $desc = sanitize($_POST['description'] ?? '');
        if ($name) {
            $stmt = $conn->prepare("INSERT INTO classes (class_name, class_name_numeric, capacity, description) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siis", $name, $numeric, $capacity, $desc);
            $stmt->execute();
            logActivity(getCurrentUserId(), 'Class Added', "Added class: $name");
            redirect(BASE_URL.'modules/classes/', 'success', 'Class added successfully.');
        }
    }
}

$classes = $conn->query("SELECT c.*, (SELECT COUNT(*) FROM students WHERE class_id=c.class_id AND status='active') as student_count, (SELECT COUNT(*) FROM sections WHERE class_id=c.class_id) as section_count FROM classes c ORDER BY c.class_name_numeric");

include '../../includes/header.php';
?>
<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-school me-2 text-primary"></i>Classes</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-2"></i>Add Class</button>
            </div>
            <?php showFlashMessage(); ?>
            <div class="row g-3">
                <?php while($c=$classes->fetch_assoc()): ?>
                <div class="col-xl-3 col-md-4 col-sm-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0"><?php echo $c['class_name']; ?></h5>
                                <?php echo getStatusBadge($c['status'] ? 'active' : 'inactive'); ?>
                            </div>
                            <div class="small text-muted mb-3"><?php echo $c['description']; ?></div>
                            <div class="row text-center g-2 mb-3">
                                <div class="col-6"><div class="bg-light rounded p-2"><div class="fw-bold text-primary"><?php echo $c['student_count']; ?></div><div class="small text-muted">Students</div></div></div>
                                <div class="col-6"><div class="bg-light rounded p-2"><div class="fw-bold text-info"><?php echo $c['section_count']; ?></div><div class="small text-muted">Sections</div></div></div>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mb-3">
                                <span>Capacity: <?php echo $c['capacity']; ?></span>
                            </div>
                            <a href="sections.php?class_id=<?php echo $c['class_id']; ?>" class="btn btn-sm btn-outline-primary w-100"><i class="fas fa-eye me-1"></i>View Sections</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
      
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Add New Class</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST">
            <?php echo csrfField(); ?>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Class Name *</label><input type="text" class="form-control" name="class_name" required></div>
                <div class="mb-3"><label class="form-label">Numeric Value</label><input type="number" class="form-control" name="class_name_numeric" min="0"></div>
                <div class="mb-3"><label class="form-label">Capacity</label><input type="number" class="form-control" name="capacity" value="40" min="1"></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="2"></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
        </form>
    </div></div>
</div>
  <?php include '../../includes/footer.php'; ?>