<?php
/**
 * Top Navbar Component
 */
$role = getCurrentUserRole();
$userName = getCurrentUserName();
$notifications = getUnreadNotificationCount(getCurrentUserId());
$school = getSchoolInfo();
$schoolName = $school['school_name'] ?? SITE_NAME;
$profileImg = $_SESSION['profile_image'] ?? 'default.png';
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white topbar shadow-sm fixed-top">
    <div class="container-fluid">
        <!-- Sidebar Toggle -->
        <button class="btn btn-link rounded-circle me-3" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- School Name / Brand -->
        <a class="navbar-brand d-none d-md-block" href="<?php echo BASE_URL; ?>dashboard.php">
            <i class="fas fa-school me-2 text-primary"></i>
            <span class="fw-bold"><?php echo htmlspecialchars($schoolName); ?></span>
        </a>
        
        <!-- Mobile Brand -->
        <a class="navbar-brand d-md-none" href="<?php echo BASE_URL; ?>dashboard.php">
            <i class="fas fa-school text-primary"></i>
        </a>
        
        <!-- Right Navbar -->
        <ul class="navbar-nav ms-auto align-items-center">
            <!-- Academic Session Badge -->
            <li class="nav-item d-none d-lg-block me-3">
                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                    <i class="fas fa-calendar-alt me-1"></i> Session: <?php echo getCurrentSession(); ?>
                </span>
            </li>
            
            <!-- Notifications -->
            <li class="nav-item dropdown me-3">
                <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell fa-lg"></i>
                    <?php if ($notifications > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        <?php echo $notifications; ?>
                    </span>
                    <?php endif; ?>
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in notifications-dropdown" aria-labelledby="notificationDropdown">
                    <h6 class="dropdown-header bg-primary text-white">
                        <i class="fas fa-bell me-2"></i>Notifications
                    </h6>
                    <?php
                    $recentNotifs = getRecentNotifications(getCurrentUserId(), 5);
                    if ($recentNotifs->num_rows > 0):
                        while ($notif = $recentNotifs->fetch_assoc()):
                    ?>
                    <a class="dropdown-item d-flex align-items-center py-2 <?php echo $notif['is_read'] ? '' : 'bg-light fw-bold'; ?>" href="#">
                        <div class="me-3">
                            <div class="icon-circle bg-<?php echo $notif['type'] == 'success' ? 'success' : ($notif['type'] == 'warning' ? 'warning' : 'info'); ?>-subtle text-<?php echo $notif['type']; ?> rounded-circle" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-<?php echo $notif['type'] == 'success' ? 'check' : ($notif['type'] == 'warning' ? 'exclamation' : 'info'); ?>" style="font-size:0.8rem;"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-muted"><?php echo formatDateTime($notif['created_at']); ?></div>
                            <span class="small"><?php echo htmlspecialchars(truncateText($notif['title'], 40)); ?></span>
                        </div>
                    </a>
                    <?php endwhile; else: ?>
                    <div class="dropdown-item text-center py-3 text-muted">
                        <i class="fas fa-bell-slash me-2"></i>No notifications
                    </div>
                    <?php endif; ?>
                    <a class="dropdown-item text-center small text-primary py-2" href="<?php echo BASE_URL; ?>modules/notices/">
                        View All Notifications
                    </a>
                </div>
            </li>
            
            <!-- Messages (Quick Links) -->
            <li class="nav-item dropdown me-3 d-none d-md-block">
                <a class="nav-link" href="#" id="quickLinksDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-th-large fa-lg"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in" style="width: 280px;" aria-labelledby="quickLinksDropdown">
                    <h6 class="dropdown-header bg-primary text-white">
                        <i class="fas fa-th-large me-2"></i>Quick Links
                    </h6>
                    <div class="row g-0 text-center">
                        <div class="col-4 py-3 quick-link-item">
                            <a href="<?php echo BASE_URL; ?>modules/students/add.php" class="text-decoration-none text-dark">
                                <i class="fas fa-user-plus text-primary mb-1 d-block"></i>
                                <small>Add Student</small>
                            </a>
                        </div>
                        <div class="col-4 py-3 quick-link-item">
                            <a href="<?php echo BASE_URL; ?>modules/attendance/" class="text-decoration-none text-dark">
                                <i class="fas fa-clipboard-check text-success mb-1 d-block"></i>
                                <small>Attendance</small>
                            </a>
                        </div>
                        <div class="col-4 py-3 quick-link-item">
                            <a href="<?php echo BASE_URL; ?>modules/exams/" class="text-decoration-none text-dark">
                                <i class="fas fa-file-alt text-warning mb-1 d-block"></i>
                                <small>Exams</small>
                            </a>
                        </div>
                        <div class="col-4 py-3 quick-link-item">
                            <a href="<?php echo BASE_URL; ?>modules/fees/collect.php" class="text-decoration-none text-dark">
                                <i class="fas fa-dollar-sign text-info mb-1 d-block"></i>
                                <small>Collect Fee</small>
                            </a>
                        </div>
                        <div class="col-4 py-3 quick-link-item">
                            <a href="<?php echo BASE_URL; ?>modules/reports/" class="text-decoration-none text-dark">
                                <i class="fas fa-chart-bar text-danger mb-1 d-block"></i>
                                <small>Reports</small>
                            </a>
                        </div>
                        <div class="col-4 py-3 quick-link-item">
                            <a href="<?php echo BASE_URL; ?>modules/settings/" class="text-decoration-none text-dark">
                                <i class="fas fa-cog text-secondary mb-1 d-block"></i>
                                <small>Settings</small>
                            </a>
                        </div>
                    </div>
                </div>
            </li>
            
            <!-- User Profile -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img class="rounded-circle me-2" src="<?php echo BASE_URL; ?>assets/uploads/<?php echo ($role == 'teacher' ? 'teachers' : ($role == 'student' ? 'students' : 'logos')); ?>/<?php echo $profileImg; ?>" 
                         alt="Profile" style="width:35px;height:35px;object-fit:cover;" 
                         onerror="this.src='<?php echo BASE_URL; ?>assets/images/default-user.png'">
                    <span class="d-none d-lg-inline small fw-bold text-gray-600"><?php echo htmlspecialchars($userName); ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>modules/settings/profile.php">
                        <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i> My Profile
                    </a>
                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>modules/settings/change-password.php">
                        <i class="fas fa-lock fa-sm fa-fw me-2 text-gray-400"></i> Change Password
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-danger"></i> Logout
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-sign-out-alt me-2"></i>Ready to Leave?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Select "Logout" below if you are ready to end your current session.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <a class="btn btn-danger" href="<?php echo BASE_URL; ?>logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>
    </div>
</div>
