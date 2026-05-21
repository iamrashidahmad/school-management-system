<?php
/**
 * Main Dashboard - Role-based Dashboard
 */
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$role = getCurrentUserRole();
$userId = getCurrentUserId();
$pageTitle = 'Dashboard';
$activeMenu = 'dashboard';

// Get dashboard statistics
$stats = getDashboardStats();

// Get user-specific data
$userDetails = getRoleDetails($userId, $role);

include 'includes/header.php';
?>

<div id="wrapper">
    
    <?php include 'includes/sidebar.php'; ?>
    
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include 'includes/navbar.php'; ?>
        
        <div class="container-fluid py-4">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard
                </h1>
                <div class="d-none d-sm-inline-block">
                    <span class="text-muted me-2"><?php echo date('l, d F Y'); ?></span>
                </div>
            </div>
            
            <?php showFlashMessage(); ?>
            
            <?php if ($role === 'admin'): ?>
            <!-- ==================== ADMIN DASHBOARD ==================== -->
            
            <!-- Statistics Cards Row 1 -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label text-primary">Total Students</div>
                                    <div class="stat-value"><?php echo formatNumber($stats['total_students']); ?></div>
                                    <a href="<?php echo BASE_URL; ?>modules/students/" class="small text-primary">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                                <div class="stat-icon text-primary">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label text-success">Total Teachers</div>
                                    <div class="stat-value"><?php echo formatNumber($stats['total_teachers']); ?></div>
                                    <a href="<?php echo BASE_URL; ?>modules/teachers/" class="small text-success">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                                <div class="stat-icon text-success">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-info h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label text-info">Total Classes</div>
                                    <div class="stat-value"><?php echo formatNumber($stats['total_classes']); ?></div>
                                    <a href="<?php echo BASE_URL; ?>modules/classes/" class="small text-info">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                                <div class="stat-icon text-info">
                                    <i class="fas fa-school"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label text-warning">Total Subjects</div>
                                    <div class="stat-value"><?php echo formatNumber($stats['total_subjects']); ?></div>
                                    <a href="<?php echo BASE_URL; ?>modules/subjects/" class="small text-warning">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                                <div class="stat-icon text-warning">
                                    <i class="fas fa-book"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistics Cards Row 2 -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-danger h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label text-danger">Total Parents</div>
                                    <div class="stat-value"><?php echo formatNumber($stats['total_parents']); ?></div>
                                    <span class="small text-muted">Registered guardians</span>
                                </div>
                                <div class="stat-icon text-danger">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label text-success">Monthly Collection</div>
                                    <div class="stat-value"><?php echo formatCurrency($stats['monthly_collection']); ?></div>
                                    <a href="<?php echo BASE_URL; ?>modules/fees/payments.php" class="small text-success">View Payments <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                                <div class="stat-icon text-success">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-danger h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label text-danger">Pending Fees</div>
                                    <div class="stat-value"><?php echo formatCurrency($stats['pending_fees']); ?></div>
                                    <a href="<?php echo BASE_URL; ?>modules/fees/pending.php" class="small text-danger">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                                <div class="stat-icon text-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-secondary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label text-secondary">Today's Attendance</div>
                                    <div class="stat-value"><?php echo $stats['today_attendance']; ?>%</div>
                                    <a href="<?php echo BASE_URL; ?>modules/attendance/" class="small text-secondary">Mark Attendance <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                                <div class="stat-icon text-secondary">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Row -->
            <div class="row g-3 mb-4">
                <!-- Monthly Revenue Chart -->
                <div class="col-xl-8">
                    <div class="card shadow-sm h-100">
                        <div class="card-header d-flex justify-content-between align-items-center py-3">
                            <h6 class="m-0 fw-bold text-primary">
                                <i class="fas fa-chart-line me-2"></i>Monthly Revenue Overview
                            </h6>
                            <div class="dropdown no-arrow">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <?php echo date('Y'); ?>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#"><?php echo date('Y'); ?></a>
                                    <a class="dropdown-item" href="#"><?php echo date('Y') - 1; ?></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart" height="280"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Gender Distribution -->
                <div class="col-xl-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 fw-bold text-primary">
                                <i class="fas fa-chart-pie me-2"></i>Student Gender Distribution
                            </h6>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <canvas id="genderChart" style="max-height:250px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bottom Row: Recent Activity, Upcoming Exams, Notices -->
            <div class="row g-3">
                <!-- Recent Activity -->
                <div class="col-xl-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 fw-bold text-primary">
                                <i class="fas fa-history me-2"></i>Recent Activities
                            </h6>
                            <a href="<?php echo BASE_URL; ?>modules/logs/" class="small text-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="timeline p-3" style="max-height:350px;overflow-y:auto;">
                                <?php foreach ($stats['recent_activities'] as $activity): ?>
                                <div class="timeline-item">
                                    <div class="time small text-muted"><?php echo formatDateTime($activity['created_at']); ?></div>
                                    <div class="small fw-bold"><?php echo htmlspecialchars($activity['username'] ?? 'System'); ?></div>
                                    <div class="small"><?php echo htmlspecialchars($activity['action']); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars(truncateText($activity['description'], 50)); ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Upcoming Exams -->
                <div class="col-xl-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 fw-bold text-primary">
                                <i class="fas fa-calendar-alt me-2"></i>Upcoming Exams
                            </h6>
                            <a href="<?php echo BASE_URL; ?>modules/exams/" class="small text-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush" style="max-height:350px;overflow-y:auto;">
                                <?php if (!empty($stats['upcoming_exams'])): ?>
                                    <?php foreach ($stats['upcoming_exams'] as $exam): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1 small fw-bold"><?php echo htmlspecialchars($exam['exam_name']); ?></h6>
                                            <small class="text-muted"><?php echo formatDate($exam['start_date']); ?></small>
                                        </div>
                                        <p class="mb-1 small text-muted">
                                            <i class="fas fa-school me-1"></i><?php echo htmlspecialchars($exam['class_name']); ?>
                                            <span class="mx-2">|</span>
                                            <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($exam['type_name']); ?>
                                        </p>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-5 text-muted">
                                        <i class="fas fa-calendar-check fa-2x mb-2"></i>
                                        <p class="small">No upcoming exams</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Notices -->
                <div class="col-xl-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 fw-bold text-primary">
                                <i class="fas fa-bullhorn me-2"></i>Recent Notices
                            </h6>
                            <a href="<?php echo BASE_URL; ?>modules/notices/" class="small text-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush" style="max-height:350px;overflow-y:auto;">
                                <?php foreach ($stats['recent_notices'] as $notice): ?>
                                <div class="list-group-item <?php echo $notice['is_pinned'] ? 'border-start border-3 border-primary' : ''; ?>">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <h6 class="mb-1 small fw-bold">
                                            <?php if ($notice['is_pinned']): ?><i class="fas fa-thumbtack text-primary me-1"></i><?php endif; ?>
                                            <?php echo htmlspecialchars(truncateText($notice['title'], 40)); ?>
                                        </h6>
                                        <span class="badge bg-<?php echo $notice['notice_type'] == 'Urgent' ? 'danger' : ($notice['notice_type'] == 'Exam' ? 'warning' : 'info'); ?> ms-2" style="font-size:0.65rem;">
                                            <?php echo $notice['notice_type']; ?>
                                        </span>
                                    </div>
                                    <p class="mb-1 small text-muted"><?php echo htmlspecialchars(truncateText($notice['content'], 60)); ?></p>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i><?php echo formatDate($notice['created_at']); ?>
                                        <span class="mx-1">|</span>
                                        <i class="fas fa-user me-1"></i><?php echo $notice['target_role']; ?>
                                    </small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Action Buttons -->
            <div class="row g-3 mt-2">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header py-3">
                            <h6 class="m-0 fw-bold text-primary">
                                <i class="fas fa-bolt me-2"></i>Quick Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-6 col-md-4 col-lg-2">
                                    <a href="<?php echo BASE_URL; ?>modules/students/add.php" class="btn btn-outline-primary w-100 py-3">
                                        <i class="fas fa-user-plus d-block mb-1"></i>
                                        <span class="small">Add Student</span>
                                    </a>
                                </div>
                                <div class="col-6 col-md-4 col-lg-2">
                                    <a href="<?php echo BASE_URL; ?>modules/teachers/add.php" class="btn btn-outline-success w-100 py-3">
                                        <i class="fas fa-chalkboard-teacher d-block mb-1"></i>
                                        <span class="small">Add Teacher</span>
                                    </a>
                                </div>
                                <div class="col-6 col-md-4 col-lg-2">
                                    <a href="<?php echo BASE_URL; ?>modules/attendance/" class="btn btn-outline-info w-100 py-3">
                                        <i class="fas fa-clipboard-check d-block mb-1"></i>
                                        <span class="small">Attendance</span>
                                    </a>
                                </div>
                                <div class="col-6 col-md-4 col-lg-2">
                                    <a href="<?php echo BASE_URL; ?>modules/fees/collect.php" class="btn btn-outline-warning w-100 py-3">
                                        <i class="fas fa-dollar-sign d-block mb-1"></i>
                                        <span class="small">Collect Fee</span>
                                    </a>
                                </div>
                                <div class="col-6 col-md-4 col-lg-2">
                                    <a href="<?php echo BASE_URL; ?>modules/exams/" class="btn btn-outline-danger w-100 py-3">
                                        <i class="fas fa-file-alt d-block mb-1"></i>
                                        <span class="small">Exams</span>
                                    </a>
                                </div>
                                <div class="col-6 col-md-4 col-lg-2">
                                    <a href="<?php echo BASE_URL; ?>modules/notices/add.php" class="btn btn-outline-secondary w-100 py-3">
                                        <i class="fas fa-bullhorn d-block mb-1"></i>
                                        <span class="small">Post Notice</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php elseif ($role === 'teacher'): ?>
            <!-- ==================== TEACHER DASHBOARD ==================== -->
            
            <div class="row g-3 mb-4">
                <div class="col-xl-4 col-md-6">
                    <div class="card stat-card stat-card-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label text-primary">My Students</div>
                                    <div class="stat-value"><?php echo formatNumber($stats['total_students']); ?></div>
                                </div>
                                <div class="stat-icon text-primary"><i class="fas fa-user-graduate"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card stat-card stat-card-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label text-success">Attendance Today</div>
                                    <div class="stat-value"><?php echo $stats['today_attendance']; ?>%</div>
                                </div>
                                <div class="stat-icon text-success"><i class="fas fa-clipboard-check"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card stat-card stat-card-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label text-warning">Upcoming Exams</div>
                                    <div class="stat-value"><?php echo count($stats['upcoming_exams']); ?></div>
                                </div>
                                <div class="stat-icon text-warning"><i class="fas fa-file-alt"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header"><h6 class="m-0 fw-bold text-primary"><i class="fas fa-tasks me-2"></i>My Tasks</h6></div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <a href="<?php echo BASE_URL; ?>modules/attendance/" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div><i class="fas fa-clipboard-check text-primary me-2"></i>Mark Attendance</div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>modules/exams/marks.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div><i class="fas fa-pen text-success me-2"></i>Enter Marks</div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>modules/assignments/" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div><i class="fas fa-tasks text-warning me-2"></i>Manage Assignments</div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>modules/quizzes/" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div><i class="fas fa-question-circle text-info me-2"></i>Manage Quizzes</div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header"><h6 class="m-0 fw-bold text-primary"><i class="fas fa-bullhorn me-2"></i>Notices</h6></div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush" style="max-height:300px;overflow-y:auto;">
                                <?php foreach ($stats['recent_notices'] as $notice): ?>
                                <div class="list-group-item small">
                                    <div class="fw-bold"><?php echo htmlspecialchars(truncateText($notice['title'], 35)); ?></div>
                                    <div class="text-muted"><?php echo formatDate($notice['created_at']); ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php elseif ($role === 'student'): ?>
            <!-- ==================== STUDENT DASHBOARD ==================== -->
            
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-primary h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon text-primary mb-2"><i class="fas fa-clipboard-check fa-2x"></i></div>
                            <div class="stat-label text-primary">Attendance</div>
                            <div class="stat-value"><?php echo $stats['today_attendance']; ?>%</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-success h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon text-success mb-2"><i class="fas fa-file-alt fa-2x"></i></div>
                            <div class="stat-label text-success">Exams</div>
                            <div class="stat-value"><?php echo count($stats['upcoming_exams']); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-warning h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon text-warning mb-2"><i class="fas fa-dollar-sign fa-2x"></i></div>
                            <div class="stat-label text-warning">Pending Fees</div>
                            <div class="stat-value"><?php echo formatCurrency($stats['pending_fees']); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-info h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon text-info mb-2"><i class="fas fa-book fa-2x"></i></div>
                            <div class="stat-label text-info">Library Books</div>
                            <div class="stat-value">0</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header"><h6 class="m-0 fw-bold text-primary"><i class="fas fa-file-alt me-2"></i>My Results</h6></div>
                        <div class="card-body">
                            <a href="<?php echo BASE_URL; ?>modules/exams/my-results.php" class="btn btn-primary">
                                <i class="fas fa-eye me-2"></i>View Results
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header"><h6 class="m-0 fw-bold text-primary"><i class="fas fa-bullhorn me-2"></i>Notices</h6></div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush" style="max-height:300px;overflow-y:auto;">
                                <?php foreach ($stats['recent_notices'] as $notice): ?>
                                <div class="list-group-item small">
                                    <div class="fw-bold"><?php echo htmlspecialchars(truncateText($notice['title'], 35)); ?></div>
                                    <div class="text-muted"><?php echo formatDate($notice['created_at']); ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php elseif ($role === 'parent'): ?>
            <!-- ==================== PARENT DASHBOARD ==================== -->
            
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card stat-card stat-card-primary h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon text-primary mb-2"><i class="fas fa-user-graduate fa-2x"></i></div>
                            <div class="stat-label text-primary">My Children</div>
                            <div class="stat-value">1</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card stat-card-warning h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon text-warning mb-2"><i class="fas fa-dollar-sign fa-2x"></i></div>
                            <div class="stat-label text-warning">Pending Fees</div>
                            <div class="stat-value"><?php echo formatCurrency($stats['pending_fees']); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card stat-card-success h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon text-success mb-2"><i class="fas fa-chart-line fa-2x"></i></div>
                            <div class="stat-label text-success">Results</div>
                            <div class="stat-value">View</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header"><h6 class="m-0 fw-bold text-primary"><i class="fas fa-child me-2"></i>My Children</h6></div>
                        <div class="card-body">
                            <a href="<?php echo BASE_URL; ?>modules/students/children.php" class="btn btn-primary">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php elseif ($role === 'accountant'): ?>
            <!-- ==================== ACCOUNTANT DASHBOARD ==================== -->
            
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label text-success">Monthly Collection</div>
                                    <div class="stat-value"><?php echo formatCurrency($stats['monthly_collection']); ?></div>
                                </div>
                                <div class="stat-icon text-success"><i class="fas fa-dollar-sign"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-danger h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label text-danger">Pending Fees</div>
                                    <div class="stat-value"><?php echo formatCurrency($stats['pending_fees']); ?></div>
                                </div>
                                <div class="stat-icon text-danger"><i class="fas fa-exclamation-triangle"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label text-primary">Total Students</div>
                                    <div class="stat-value"><?php echo formatNumber($stats['total_students']); ?></div>
                                </div>
                                <div class="stat-icon text-primary"><i class="fas fa-user-graduate"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card stat-card-info h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label text-info">Today's Transactions</div>
                                    <div class="stat-value">-</div>
                                </div>
                                <div class="stat-icon text-info"><i class="fas fa-money-bill-wave"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header"><h6 class="m-0 fw-bold text-primary"><i class="fas fa-chart-line me-2"></i>Revenue Overview</h6></div>
                        <div class="card-body"><canvas id="revenueChart" height="250"></canvas></div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header"><h6 class="m-0 fw-bold text-primary"><i class="fas fa-bolt me-2"></i>Quick Actions</h6></div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="<?php echo BASE_URL; ?>modules/fees/collect.php" class="btn btn-success"><i class="fas fa-dollar-sign me-2"></i>Collect Fee</a>
                                <a href="<?php echo BASE_URL; ?>modules/fees/pending.php" class="btn btn-danger"><i class="fas fa-exclamation-circle me-2"></i>Pending Fees</a>
                                <a href="<?php echo BASE_URL; ?>modules/fees/reports.php" class="btn btn-info"><i class="fas fa-chart-bar me-2"></i>Fee Reports</a>
                                <a href="<?php echo BASE_URL; ?>modules/fees/structure.php" class="btn btn-primary"><i class="fas fa-list-alt me-2"></i>Fee Structure</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
        
       
    </div>
</div>
 <?php include 'includes/footer.php'; ?>
<!-- Chart.js Scripts -->
<script>
// Revenue Chart
var revenueCtx = document.getElementById('revenueChart');
if (revenueCtx) {
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var revenueData = <?php 
        $monthlyData = array_fill(0, 12, 0);
        foreach ($stats['monthly_revenue'] as $month => $value) {
            $monthlyData[$month - 1] = $value;
        }
        echo json_encode($monthlyData);
    ?>;
    
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Revenue ($)',
                data: revenueData,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78,115,223,0.05)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#4e73df',
                pointBorderColor: '#fff',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#eaecf4', drawBorder: false },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
}

// Gender Chart
var genderCtx = document.getElementById('genderChart');
if (genderCtx) {
    var genderData = <?php 
        $genders = ['Male' => 0, 'Female' => 0, 'Other' => 0];
        foreach ($stats['gender_distribution'] as $gender => $count) {
            $genders[$gender] = $count;
        }
        echo json_encode(array_values($genders));
    ?>;
    
    new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: ['Male', 'Female', 'Other'],
            datasets: [{
                data: genderData,
                backgroundColor: ['#4e73df', '#e83e8c', '#858796'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, usePointStyle: true, pointStyle: 'circle' }
                }
            }
        }
    });
}
</script>
