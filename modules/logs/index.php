<?php
/**
 * Activity Logs
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
requireRole('admin');

$pageTitle = 'Activity Logs';
$activeMenu = 'settings';

$logs = $conn->query("SELECT al.*, u.username FROM activity_logs al LEFT JOIN users u ON al.user_id=u.user_id ORDER BY al.created_at DESC LIMIT 500");

include '../../includes/header.php';
?>
<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-history me-2 text-primary"></i>Activity Logs</h1>
            </div>
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover datatable mb-0">
                            <thead><tr><th>ID</th><th>User</th><th>Action</th><th>Description</th><th>IP Address</th><th>Date & Time</th></tr></thead>
                            <tbody>
                                <?php while($l=$logs->fetch_assoc()): ?>
                                <tr><td><?php echo $l['log_id']; ?></td><td><?php echo $l['username'] ?? 'System'; ?></td><td><span class="badge bg-primary"><?php echo $l['action']; ?></span></td><td><?php echo $l['description']; ?></td><td class="font-monospace small"><?php echo $l['ip_address']; ?></td><td><?php echo formatDateTime($l['created_at']); ?></td></tr>
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