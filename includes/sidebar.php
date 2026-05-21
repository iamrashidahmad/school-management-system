<?php
/**
 * Sidebar Navigation Component
 */
$role = getCurrentUserRole();
$userRole = ucfirst($role);
$school = getSchoolInfo();
$logo = $school['school_logo'] ?? 'default_logo.png';

// Menu structure based on roles
$menus = [
    'admin' => [
        ['title' => 'Dashboard', 'icon' => 'fa-tachometer-alt', 'url' => 'dashboard.php', 'key' => 'dashboard'],
        ['title' => 'Students', 'icon' => 'fa-user-graduate', 'url' => 'modules/students/', 'key' => 'students',
         'submenu' => [
             ['title' => 'All Students', 'url' => 'modules/students/'],
             ['title' => 'Add Student', 'url' => 'modules/students/add.php'],
             ['title' => 'Student ID Cards', 'url' => 'modules/students/id-cards.php'],
             ['title' => 'Import/Export', 'url' => 'modules/students/import.php'],
         ]],
        ['title' => 'Teachers', 'icon' => 'fa-chalkboard-teacher', 'url' => 'modules/teachers/', 'key' => 'teachers',
         'submenu' => [
             ['title' => 'All Teachers', 'url' => 'modules/teachers/'],
             ['title' => 'Add Teacher', 'url' => 'modules/teachers/add.php'],
             ['title' => 'Subject Assignment', 'url' => 'modules/teachers/assign-subjects.php'],
         ]],
        ['title' => 'Parents', 'icon' => 'fa-users', 'url' => 'modules/parents/', 'key' => 'parents'],
        ['title' => 'Classes', 'icon' => 'fa-school', 'url' => 'modules/classes/', 'key' => 'classes',
         'submenu' => [
             ['title' => 'All Classes', 'url' => 'modules/classes/'],
             ['title' => 'Sections', 'url' => 'modules/classes/sections.php'],
             ['title' => 'Class Teachers', 'url' => 'modules/classes/teachers.php'],
         ]],
        ['title' => 'Subjects', 'icon' => 'fa-book', 'url' => 'modules/subjects/', 'key' => 'subjects'],
        ['title' => 'Attendance', 'icon' => 'fa-clipboard-check', 'url' => 'modules/attendance/', 'key' => 'attendance',
         'submenu' => [
             ['title' => 'Student Attendance', 'url' => 'modules/attendance/'],
             ['title' => 'Teacher Attendance', 'url' => 'modules/attendance/teachers.php'],
             ['title' => 'Attendance Report', 'url' => 'modules/attendance/report.php'],
         ]],
        ['title' => 'Examinations', 'icon' => 'fa-file-alt', 'url' => 'modules/exams/', 'key' => 'exams',
         'submenu' => [
             ['title' => 'Exam Types', 'url' => 'modules/exams/types.php'],
             ['title' => 'Exams', 'url' => 'modules/exams/'],
             ['title' => 'Exam Subjects', 'url' => 'modules/exams/subjects.php'],
             ['title' => 'Enter Marks', 'url' => 'modules/exams/marks.php'],
             ['title' => 'Results', 'url' => 'modules/exams/results.php'],
             ['title' => 'Report Cards', 'url' => 'modules/exams/report-cards.php'],
         ]],
        ['title' => 'Fees', 'icon' => 'fa-dollar-sign', 'url' => 'modules/fees/', 'key' => 'fees',
         'submenu' => [
             ['title' => 'Fee Structure', 'url' => 'modules/fees/structure.php'],
             ['title' => 'Fee Types', 'url' => 'modules/fees/types.php'],
             ['title' => 'Collect Fee', 'url' => 'modules/fees/collect.php'],
             ['title' => 'Fee Payments', 'url' => 'modules/fees/payments.php'],
             ['title' => 'Pending Fees', 'url' => 'modules/fees/pending.php'],
             ['title' => 'Fee Reports', 'url' => 'modules/fees/reports.php'],
         ]],
        ['title' => 'Library', 'icon' => 'fa-book-reader', 'url' => 'modules/library/', 'key' => 'library',
         'submenu' => [
             ['title' => 'All Books', 'url' => 'modules/library/'],
             ['title' => 'Add Book', 'url' => 'modules/library/add.php'],
             ['title' => 'Issue Book', 'url' => 'modules/library/issue.php'],
             ['title' => 'Return Book', 'url' => 'modules/library/return.php'],
             ['title' => 'Library Reports', 'url' => 'modules/library/reports.php'],
         ]],
        ['title' => 'Transport', 'icon' => 'fa-bus', 'url' => 'modules/transport/', 'key' => 'transport',
         'submenu' => [
             ['title' => 'Routes', 'url' => 'modulestransport/routes.php'],
             ['title' => 'Vehicles', 'url' => 'modules/transport/vehicles.php'],
             ['title' => 'Allocations', 'url' => 'modules/transport/allocations.php'],
         ]],
        ['title' => 'Notices', 'icon' => 'fa-bullhorn', 'url' => 'modules/notices/', 'key' => 'notices'],
        ['title' => 'Assignments', 'icon' => 'fa-tasks', 'url' => 'modules/assignments/', 'key' => 'assignments'],
        ['title' => 'Quizzes', 'icon' => 'fa-question-circle', 'url' => 'modules/quizzes/', 'key' => 'quizzes'],
        ['title' => 'Reports', 'icon' => 'fa-chart-bar', 'url' => 'modules/reports/', 'key' => 'reports',
         'submenu' => [
             ['title' => 'Student Reports', 'url' => 'modules/reports/students.php'],
             ['title' => 'Attendance Reports', 'url' => 'modules/reports/attendance.php'],
             ['title' => 'Exam Reports', 'url' => 'modules/reports/exams.php'],
             ['title' => 'Fee Reports', 'url' => 'modules/reports/fees.php'],
             ['title' => 'Financial Reports', 'url' => 'modules/reports/financial.php'],
         ]],
        ['title' => 'Settings', 'icon' => 'fa-cog', 'url' => 'modules/settings/', 'key' => 'settings',
         'submenu' => [
             ['title' => 'School Info', 'url' => 'modules/settings/school.php'],
             ['title' => 'Academic Session', 'url' => 'modules/settings/session.php'],
             ['title' => 'Users', 'url' => 'modules/settings/users.php'],
             ['title' => 'Backup', 'url' => 'modules/settings/backup.php'],
             ['title' => 'Logs', 'url' => 'modules/logs/'],
         ]],
    ],
    'teacher' => [
        ['title' => 'Dashboard', 'icon' => 'fa-tachometer-alt', 'url' => 'dashboard.php', 'key' => 'dashboard'],
        ['title' => 'My Students', 'icon' => 'fa-user-graduate', 'url' => 'modules/students/', 'key' => 'students'],
        ['title' => 'Attendance', 'icon' => 'fa-clipboard-check', 'url' => 'modules/attendance/', 'key' => 'attendance'],
        ['title' => 'Examinations', 'icon' => 'fa-file-alt', 'url' => 'modules/exams/', 'key' => 'exams',
         'submenu' => [
             ['title' => 'Enter Marks', 'url' => 'modules/exams/marks.php'],
             ['title' => 'Results', 'url' => 'modules/exams/results.php'],
         ]],
        ['title' => 'Assignments', 'icon' => 'fa-tasks', 'url' => 'modules/assignments/', 'key' => 'assignments'],
        ['title' => 'Quizzes', 'icon' => 'fa-question-circle', 'url' => 'modules/quizzes/', 'key' => 'quizzes'],
        ['title' => 'Notices', 'icon' => 'fa-bullhorn', 'url' => 'modules/notices/', 'key' => 'notices'],
        ['title' => 'Library', 'icon' => 'fa-book-reader', 'url' => 'modules/library/', 'key' => 'library'],
        ['title' => 'My Profile', 'icon' => 'fa-user', 'url' => 'modules/settings/profile.php', 'key' => 'profile'],
    ],
    'student' => [
        ['title' => 'Dashboard', 'icon' => 'fa-tachometer-alt', 'url' => 'dashboard.php', 'key' => 'dashboard'],
        ['title' => 'My Profile', 'icon' => 'fa-user', 'url' => 'modules/students/profile.php', 'key' => 'profile'],
        ['title' => 'Attendance', 'icon' => 'fa-clipboard-check', 'url' => 'modules/attendance/view.php', 'key' => 'attendance'],
        ['title' => 'Exams & Results', 'icon' => 'fa-file-alt', 'url' => 'modules/exams/my-results.php', 'key' => 'exams'],
        ['title' => 'Assignments', 'icon' => 'fa-tasks', 'url' => 'modules/assignments/my.php', 'key' => 'assignments'],
        ['title' => 'Quizzes', 'icon' => 'fa-question-circle', 'url' => 'modules/quizzes/my.php', 'key' => 'quizzes'],
        ['title' => 'Fee Details', 'icon' => 'fa-dollar-sign', 'url' => 'modules/fees/my.php', 'key' => 'fees'],
        ['title' => 'Library', 'icon' => 'fa-book-reader', 'url' => 'modules/library/my.php', 'key' => 'library'],
        ['title' => 'Notices', 'icon' => 'fa-bullhorn', 'url' => 'modules/notices/', 'key' => 'notices'],
        ['title' => 'Transport', 'icon' => 'fa-bus', 'url' => 'modules/transport/my.php', 'key' => 'transport'],
    ],
    'parent' => [
        ['title' => 'Dashboard', 'icon' => 'fa-tachometer-alt', 'url' => 'dashboard.php', 'key' => 'dashboard'],
        ['title' => 'My Children', 'icon' => 'fa-user-graduate', 'url' => 'modules/students/children.php', 'key' => 'students'],
        ['title' => 'Attendance', 'icon' => 'fa-clipboard-check', 'url' => 'modules/attendance/children.php', 'key' => 'attendance'],
        ['title' => 'Exam Results', 'icon' => 'fa-file-alt', 'url' => 'modules/exams/children-results.php', 'key' => 'exams'],
        ['title' => 'Fee Details', 'icon' => 'fa-dollar-sign', 'url' => 'modules/fees/children.php', 'key' => 'fees'],
        ['title' => 'Notices', 'icon' => 'fa-bullhorn', 'url' => 'modules/notices/', 'key' => 'notices'],
        ['title' => 'Library', 'icon' => 'fa-book-reader', 'url' => 'modules/library/children.php', 'key' => 'library'],
        ['title' => 'Transport', 'icon' => 'fa-bus', 'url' => 'modules/transport/children.php', 'key' => 'transport'],
        ['title' => 'My Profile', 'icon' => 'fa-user', 'url' => 'modules/settings/profile.php', 'key' => 'profile'],
    ],
    'accountant' => [
        ['title' => 'Dashboard', 'icon' => 'fa-tachometer-alt', 'url' => 'dashboard.php', 'key' => 'dashboard'],
        ['title' => 'Fee Structure', 'icon' => 'fa-list-alt', 'url' => 'modules/fees/structure.php', 'key' => 'fees'],
        ['title' => 'Collect Fee', 'icon' => 'fa-dollar-sign', 'url' => 'modules/fees/collect.php', 'key' => 'fees'],
        ['title' => 'Fee Payments', 'icon' => 'fa-money-bill-wave', 'url' => 'modules/fees/payments.php', 'key' => 'fees'],
        ['title' => 'Pending Fees', 'icon' => 'fa-exclamation-circle', 'url' => 'modules/fees/pending.php', 'key' => 'fees'],
        ['title' => 'Fee Reports', 'icon' => 'fa-chart-bar', 'url' => 'modules/fees/reports.php', 'key' => 'fees'],
        ['title' => 'Financial Reports', 'icon' => 'fa-chart-line', 'url' => 'modules/reports/financial.php', 'key' => 'reports'],
        ['title' => 'Students', 'icon' => 'fa-user-graduate', 'url' => 'modules/students/', 'key' => 'students'],
        ['title' => 'Notices', 'icon' => 'fa-bullhorn', 'url' => 'modules/notices/', 'key' => 'notices'],
        ['title' => 'My Profile', 'icon' => 'fa-user', 'url' => 'modules/settings/profile.php', 'key' => 'profile'],
    ],
];

$currentMenu = $menus[$role] ?? $menus['admin'];
?>
<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>dashboard.php">
        <div class="sidebar-brand-icon">
            <i class="fas fa-school"></i>
        </div>
        <div class="sidebar-brand-text mx-2">
            <div class="small">School ERP</div>
        </div>
    </a>
    
    <!-- Divider -->
    <hr class="sidebar-divider my-0">
    
    <!-- User Info (Mobile) -->
    <div class="d-lg-none text-center text-white py-3">
        <img src="<?php echo BASE_URL; ?>assets/images/default-user.png" class="rounded-circle mb-2" style="width:50px;height:50px;object-fit:cover;">
        <div class="small fw-bold"><?php echo htmlspecialchars($userName); ?></div>
        <span class="badge bg-light text-dark"><?php echo $userRole; ?></span>
    </div>
    
    <hr class="sidebar-divider my-0 d-lg-none">
    
    <!-- Menu Items -->
    <?php foreach ($currentMenu as $menu): ?>
    <?php 
    $hasSubmenu = isset($menu['submenu']) && !empty($menu['submenu']);
    $isActive = $activeMenu === $menu['key'];
    $menuId = 'menu_' . $menu['key'];
    ?>
    
    <li class="nav-item <?php echo $isActive ? 'active' : ''; ?>">
        <a class="nav-link <?php echo $hasSubmenu ? 'collapsed' : ''; ?>" 
           href="<?php echo $hasSubmenu ? '#' : BASE_URL . $menu['url']; ?>"
           <?php echo $hasSubmenu ? 'data-bs-toggle="collapse" data-bs-target="#' . $menuId . '"' : ''; ?>>
            <i class="fas <?php echo $menu['icon']; ?>"></i>
            <span><?php echo $menu['title']; ?></span>
            <?php if ($hasSubmenu): ?>
            <i class="fas fa-chevron-right ms-auto small submenu-icon"></i>
            <?php endif; ?>
        </a>
        
        <?php if ($hasSubmenu): ?>
        <div id="<?php echo $menuId; ?>" class="collapse <?php echo $isActive ? 'show' : ''; ?>" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <?php foreach ($menu['submenu'] as $sub): ?>
                <a class="collapse-item" href="<?php echo BASE_URL . $sub['url']; ?>">
                    <i class="fas fa-circle small me-2" style="font-size:6px;vertical-align:middle;"></i>
                    <?php echo $sub['title']; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </li>
    
    <!-- Divider for certain items -->
    <?php if (in_array($menu['key'], ['dashboard', 'fees', 'notices'])): ?>
    <hr class="sidebar-divider">
    <?php endif; ?>
    
    <?php endforeach; ?>
    
    <!-- Sidebar Toggle Button (Bottom) -->
    <div class="text-center d-none d-md-inline mt-auto">
        <button class="rounded-circle border-0" id="sidebarToggleBottom">
            <i class="fas fa-angle-left"></i>
        </button>
    </div>
</ul>
