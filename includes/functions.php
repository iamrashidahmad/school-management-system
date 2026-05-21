<?php
/**
 * Common Functions Library
 */

require_once __DIR__ . '/../config/database.php';

// Format date
define('DATE_FORMAT', 'd M Y');
define('DATETIME_FORMAT', 'd M Y h:i A');
define('DB_DATE_FORMAT', 'Y-m-d');

function formatDate($date, $format = DATE_FORMAT) {
    if (empty($date) || $date == '0000-00-00') return 'N/A';
    return date($format, strtotime($date));
}

function formatDateTime($datetime, $format = DATETIME_FORMAT) {
    if (empty($datetime) || $datetime == '0000-00-00 00:00:00') return 'N/A';
    return date($format, strtotime($datetime));
}

// Format currency
function formatCurrency($amount, $symbol = '$') {
    return $symbol . number_format($amount, 2);
}

// Format number
function formatNumber($number, $decimals = 0) {
    return number_format($number, $decimals);
}

// Get current academic session
function getCurrentSession() {
    global $conn;
    $result = $conn->query("SELECT academic_session FROM school_info LIMIT 1");
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc()['academic_session'];
    }
    return date('Y') . '-' . (date('Y') + 1);
}

// Get school info
function getSchoolInfo() {
    global $conn;
    $result = $conn->query("SELECT * FROM school_info LIMIT 1");
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Count total records
function countRecords($table, $where = '', $params = []) {
    global $conn;
    $sql = "SELECT COUNT(*) as total FROM $table";
    if (!empty($where)) {
        $sql .= " WHERE $where";
    }
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total'] ?? 0;
}

// Get single record
function getRecord($table, $where, $params = []) {
    global $conn;
    $sql = "SELECT * FROM $table WHERE $where LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Get all records
function getRecords($table, $where = '', $params = [], $order = '', $limit = '') {
    global $conn;
    $sql = "SELECT * FROM $table";
    if (!empty($where)) {
        $sql .= " WHERE $where";
    }
    if (!empty($order)) {
        $sql .= " ORDER BY $order";
    }
    if (!empty($limit)) {
        $sql .= " LIMIT $limit";
    }
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

// Execute query
function executeQuery($sql, $params = []) {
    global $conn;
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt;
}

// Generate unique code
function generateCode($prefix, $table, $column, $length = 4) {
    global $conn;
    $year = date('Y');
    $sql = "SELECT MAX(CAST(SUBSTRING($column, LOCATE('-', $column, -$length-1)+1) AS UNSIGNED)) as max_num 
            FROM $table WHERE $column LIKE ?";
    $like = "$prefix-$year-%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $next = ($result['max_num'] ?? 0) + 1;
    return sprintf("%s-%s-%04d", $prefix, $year, $next);
}

// Upload file
function uploadFile($file, $directory, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf'], $maxSize = 5242880) {
    $result = ['success' => false, 'message' => '', 'filename' => ''];
    
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        $result['message'] = 'No file uploaded';
        return $result;
    }
    
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmp = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Validate extension
    if (!in_array($fileExt, $allowedTypes)) {
        $result['message'] = 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes);
        return $result;
    }
    
    // Validate size
    if ($fileSize > $maxSize) {
        $result['message'] = 'File too large. Max size: ' . ($maxSize / 1048576) . 'MB';
        return $result;
    }
    
    // Create directory if not exists
    $uploadDir = __DIR__ . '/../assets/uploads/' . $directory . '/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Generate unique filename
    $newName = uniqid() . '_' . time() . '.' . $fileExt;
    $destination = $uploadDir . $newName;
    
    if (move_uploaded_file($fileTmp, $destination)) {
        $result['success'] = true;
        $result['filename'] = $newName;
    } else {
        $result['message'] = 'Failed to upload file';
    }
    
    return $result;
}

// Delete file
function deleteFile($filename, $directory) {
    $path = __DIR__ . '/../assets/uploads/' . $directory . '/' . $filename;
    if (file_exists($path) && $filename != 'default.png') {
        return unlink($path);
    }
    return false;
}

// Generate pagination
function getPagination($totalRecords, $currentPage = 1, $perPage = 25) {
    $totalPages = ceil($totalRecords / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total' => $totalRecords,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset
    ];
}

function renderPagination($pagination, $url) {
    if ($pagination['total_pages'] <= 1) return '';
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    // Previous
    $prevClass = $pagination['current_page'] <= 1 ? 'disabled' : '';
    $html .= '<li class="page-item ' . $prevClass . '">
        <a class="page-link" href="' . $url . '&page=' . ($pagination['current_page'] - 1) . '">Previous</a></li>';
    
    // Page numbers
    $start = max(1, $pagination['current_page'] - 2);
    $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        $active = $i == $pagination['current_page'] ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '">
            <a class="page-link" href="' . $url . '&page=' . $i . '">' . $i . '</a></li>';
    }
    
    // Next
    $nextClass = $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '';
    $html .= '<li class="page-item ' . $nextClass . '">
        <a class="page-link" href="' . $url . '&page=' . ($pagination['current_page'] + 1) . '">Next</a></li>';
    
    $html .= '</ul></nav>';
    return $html;
}

// Calculate grade from percentage
function calculateGrade($percentage) {
    global $conn;
    $stmt = $conn->prepare("SELECT grade_name, grade_point, description FROM grading_system 
                            WHERE ? BETWEEN min_percentage AND max_percentage AND status = 1 LIMIT 1");
    $stmt->bind_param("d", $percentage);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?? ['grade_name' => 'N/A', 'grade_point' => 0, 'description' => 'N/A'];
}

// Get attendance summary
function getAttendanceSummary($student_id, $month = null, $year = null) {
    global $conn;
    
    $month = $month ?? date('m');
    $year = $year ?? date('Y');
    
    $sql = "SELECT 
        COUNT(*) as total_days,
        SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present,
        SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent,
        SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) as late,
        SUM(CASE WHEN status = 'Half Day' THEN 1 ELSE 0 END) as half_day
    FROM attendance 
    WHERE student_id = ? AND MONTH(attendance_date) = ? AND YEAR(attendance_date) = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $student_id, $month, $year);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Calculate fee summary
function getFeeSummary($student_id = null) {
    global $conn;
    
    $sql = "SELECT 
        COUNT(*) as total_fees,
        SUM(amount) as total_amount,
        SUM(paid_amount) as total_paid,
        SUM(balance_amount) as total_balance,
        SUM(CASE WHEN status = 'Paid' THEN 1 ELSE 0 END) as paid_count,
        SUM(CASE WHEN status = 'Unpaid' THEN 1 ELSE 0 END) as unpaid_count,
        SUM(CASE WHEN status = 'Partial' THEN 1 ELSE 0 END) as partial_count,
        SUM(CASE WHEN status = 'Overdue' THEN 1 ELSE 0 END) as overdue_count
    FROM student_fees";
    
    if ($student_id) {
        $sql .= " WHERE student_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $student_id);
    } else {
        $stmt = $conn->prepare($sql);
    }
    
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Send notification
function sendNotification($user_id, $title, $message, $type = 'info') {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $title, $message, $type);
    return $stmt->execute();
}

// Get unread notification count
function getUnreadNotificationCount($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'] ?? 0;
}

// Get recent notifications
function getRecentNotifications($user_id, $limit = 5) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
    $stmt->bind_param("ii", $user_id, $limit);
    $stmt->execute();
    return $stmt->get_result();
}

// Truncate text
function truncateText($text, $length = 50) {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

// Get setting value
function getSetting($key, $default = '') {
    global $conn;
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['setting_value'];
    }
    return $default;
}

// Update setting
function updateSetting($key, $value) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                            ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->bind_param("sss", $key, $value, $value);
    return $stmt->execute();
}

// Generate random password
function generatePassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    return substr(str_shuffle($chars), 0, $length);
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validate phone
function isValidPhone($phone) {
    return preg_match('/^[+]?[0-9\s\-\(\)]{7,20}$/', $phone);
}

// Get months list
function getMonths() {
    return [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    ];
}

// Get years list (current year +/- 5)
function getYears() {
    $current = date('Y');
    $years = [];
    for ($i = $current - 5; $i <= $current + 5; $i++) {
        $years[$i] = $i;
    }
    return $years;
}

// Get status badge HTML
function getStatusBadge($status) {
    $badges = [
        'active' => 'badge bg-success',
        'inactive' => 'badge bg-secondary',
        'Present' => 'badge bg-success',
        'Absent' => 'badge bg-danger',
        'Late' => 'badge bg-warning',
        'Half Day' => 'badge bg-info',
        'On Leave' => 'badge bg-primary',
        'Paid' => 'badge bg-success',
        'Unpaid' => 'badge bg-danger',
        'Partial' => 'badge bg-warning',
        'Overdue' => 'badge bg-danger',
        'Pass' => 'badge bg-success',
        'Fail' => 'badge bg-danger',
        'Pending' => 'badge bg-warning',
        'Published' => 'badge bg-success',
        'Draft' => 'badge bg-secondary',
        'Closed' => 'badge bg-dark',
        'graduated' => 'badge bg-info',
        'transferred' => 'badge bg-primary',
        'suspended' => 'badge bg-danger'
    ];
    $class = $badges[$status] ?? 'badge bg-secondary';
    return '<span class="' . $class . '">' . ucfirst($status) . '</span>';
}

// Export to CSV
function exportCSV($filename, $headers, $data) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, $headers);
    
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
}

// Get dashboard statistics
function getDashboardStats() {
    global $conn;
    
    $stats = [];
    
    // Total Students
    $result = $conn->query("SELECT COUNT(*) as count FROM students WHERE status = 'active'");
    $stats['total_students'] = $result->fetch_assoc()['count'];
    
    // Total Teachers
    $result = $conn->query("SELECT COUNT(*) as count FROM teachers WHERE status = 'active'");
    $stats['total_teachers'] = $result->fetch_assoc()['count'];
    
    // Total Classes
    $result = $conn->query("SELECT COUNT(*) as count FROM classes WHERE status = 1");
    $stats['total_classes'] = $result->fetch_assoc()['count'];
    
    // Total Subjects
    $result = $conn->query("SELECT COUNT(*) as count FROM subjects WHERE status = 1");
    $stats['total_subjects'] = $result->fetch_assoc()['count'];
    
    // Total Parents
    $result = $conn->query("SELECT COUNT(*) as count FROM parents WHERE status = 1");
    $stats['total_parents'] = $result->fetch_assoc()['count'];
    
    // Fee Collection (current month)
    $currentMonth = date('Y-m');
    $result = $conn->query("SELECT COALESCE(SUM(paid_amount), 0) as total FROM student_fees WHERE month_year = '$currentMonth' AND status = 'Paid'");
    $stats['monthly_collection'] = $result->fetch_assoc()['total'];
    
    // Pending Fees
    $result = $conn->query("SELECT COALESCE(SUM(balance_amount), 0) as total FROM student_fees WHERE status IN ('Unpaid', 'Partial', 'Overdue')");
    $stats['pending_fees'] = $result->fetch_assoc()['total'];
    
    // Today's Attendance
    $today = date('Y-m-d');
    $result = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE attendance_date = '$today'");
    $attendanceMarked = $result->fetch_assoc()['total'];
    $result = $conn->query("SELECT COUNT(*) as total FROM students WHERE status = 'active'");
    $totalStudents = $result->fetch_assoc()['total'];
    $stats['today_attendance'] = $totalStudents > 0 ? round(($attendanceMarked / $totalStudents) * 100, 1) : 0;
    
    // Recent Activities
    $result = $conn->query("SELECT al.*, u.username FROM activity_logs al LEFT JOIN users u ON al.user_id = u.user_id ORDER BY al.created_at DESC LIMIT 10");
    $stats['recent_activities'] = $result->fetch_all(MYSQLI_ASSOC);
    
    // Upcoming Exams
    $result = $conn->query("SELECT e.*, et.exam_name as type_name, c.class_name FROM exams e 
                            JOIN exam_types et ON e.exam_type_id = et.exam_type_id 
                            JOIN classes c ON e.class_id = c.class_id 
                            WHERE e.start_date >= CURDATE() AND e.status = 1 
                            ORDER BY e.start_date LIMIT 5");
    $stats['upcoming_exams'] = $result->fetch_all(MYSQLI_ASSOC);
    
    // Recent Notices
    $result = $conn->query("SELECT * FROM notices WHERE status = 1 ORDER BY created_at DESC LIMIT 5");
    $stats['recent_notices'] = $result->fetch_all(MYSQLI_ASSOC);
    
    // Monthly revenue data for chart
    $result = $conn->query("SELECT MONTH(payment_date) as month, SUM(amount) as total 
                            FROM fee_payments WHERE YEAR(payment_date) = YEAR(CURDATE()) 
                            GROUP BY MONTH(payment_date) ORDER BY month");
    $stats['monthly_revenue'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['monthly_revenue'][$row['month']] = $row['total'];
    }
    
    // Student gender distribution
    $result = $conn->query("SELECT gender, COUNT(*) as count FROM students WHERE status = 'active' GROUP BY gender");
    $stats['gender_distribution'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['gender_distribution'][$row['gender']] = $row['count'];
    }
    
    return $stats;
}
?>
