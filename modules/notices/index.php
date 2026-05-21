<?php
/**
 * Notices Module
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$pageTitle = 'Notices';
$activeMenu = 'notices';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $title = sanitize($_POST['title'] ?? '');
        $content = sanitize($_POST['content'] ?? '');
        $type = sanitize($_POST['notice_type'] ?? 'General');
        $target = sanitize($_POST['target_role'] ?? 'All');
        $start = sanitize($_POST['start_date'] ?? '');
        $end = sanitize($_POST['end_date'] ?? '');
        $pinned = isset($_POST['is_pinned']) ? 1 : 0;
        
        if ($title && $content) {
            $stmt = $conn->prepare("INSERT INTO notices (title, content, notice_type, target_role, posted_by, is_pinned, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssiiis", $title, $content, $type, $target, getCurrentUserId(), $pinned, $start, $end);
            $stmt->execute();
            logActivity(getCurrentUserId(), 'Notice Posted', "Posted: $title");
            redirect(BASE_URL.'modules/notices/', 'success', 'Notice posted successfully.');
        }
    }
}

$notices = $conn->query("SELECT n.*, u.username FROM notices n JOIN users u ON n.posted_by=u.user_id ORDER BY n.is_pinned DESC, n.created_at DESC");

include '../../includes/header.php';
?>
<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-bullhorn me-2 text-primary"></i>Notices</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-2"></i>Post Notice</button>
            </div>
            <?php showFlashMessage(); ?>
            
            <div class="row g-3">
                <?php while($n=$notices->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm h-100 <?php echo $n['is_pinned'] ? 'border-start border-4 border-primary' : ''; ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-<?php echo $n['notice_type']=='Urgent'?'danger':($n['notice_type']=='Exam'?'warning':'info'); ?>"><?php echo $n['notice_type']; ?></span>
                                <?php if($n['is_pinned']): ?><i class="fas fa-thumbtack text-primary"></i><?php endif; ?>
                            </div>
                            <h6 class="card-title"><?php echo $n['title']; ?></h6>
                            <p class="card-text small text-muted"><?php echo truncateText($n['content'], 100); ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted"><i class="fas fa-user me-1"></i><?php echo $n['username']; ?></small>
                                <small class="text-muted"><?php echo formatDate($n['created_at']); ?></small>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-secondary"><?php echo $n['target_role']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Post New Notice</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST"><?php echo csrfField(); ?>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Title *</label><input type="text" class="form-control" name="title" required></div>
            <div class="mb-3"><label class="form-label">Content *</label><textarea class="form-control" name="content" rows="4" required></textarea></div>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Type</label><select class="form-select" name="notice_type"><option value="General">General</option><option value="Exam">Exam</option><option value="Fee">Fee</option><option value="Event">Event</option><option value="Holiday">Holiday</option><option value="Urgent">Urgent</option></select></div>
                <div class="col-md-4"><label class="form-label">Target</label><select class="form-select" name="target_role"><option value="All">All</option><option value="Admin">Admin</option><option value="Teacher">Teacher</option><option value="Student">Student</option><option value="Parent">Parent</option></select></div>
                <div class="col-md-4"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="is_pinned" value="1" id="pin"><label class="form-check-label" for="pin">Pin Notice</label></div></div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-6"><label class="form-label">Start Date</label><input type="date" class="form-control" name="start_date"></div>
                <div class="col-md-6"><label class="form-label">End Date</label><input type="date" class="form-control" name="end_date"></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Post</button></div>
    </form>
</div></div></div>
<?php include '../../includes/footer.php'; ?>