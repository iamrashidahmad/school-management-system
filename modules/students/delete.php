<?php
/**
 * Delete Student
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();

$studentId = intval($_GET['id'] ?? 0);

if ($studentId > 0) {
    // Get student info before deletion
    $stmt = $conn->prepare("SELECT s.*, u.user_id FROM students s JOIN users u ON s.user_id = u.user_id WHERE s.student_id = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    
    if ($student) {
        $conn->begin_transaction();
        
        try {
            // Delete related records
            $conn->query("DELETE FROM attendance WHERE student_id = $studentId");
            $conn->query("DELETE FROM student_marks WHERE student_id = $studentId");
            $conn->query("DELETE FROM student_fees WHERE student_id = $studentId");
            $conn->query("DELETE FROM fee_payments WHERE student_id = $studentId");
            $conn->query("DELETE FROM book_issues WHERE student_id = $studentId");
            $conn->query("DELETE FROM assignment_submissions WHERE student_id = $studentId");
            $conn->query("DELETE FROM student_documents WHERE student_id = $studentId");
            
            // Delete student
            $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
            $stmt->bind_param("i", $studentId);
            $stmt->execute();
            
            // Delete user account
            $userId = $student['user_id'];
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            
            $conn->commit();
            
            logActivity(getCurrentUserId(), 'Student Deleted', "Deleted student: {$student['first_name']} {$student['last_name']}");
            redirect(BASE_URL . 'modules/students/', 'success', 'Student deleted successfully.');
        } catch (Exception $e) {
            $conn->rollback();
            redirect(BASE_URL . 'modules/students/', 'error', 'Error deleting student: ' . $e->getMessage());
        }
    }
}

redirect(BASE_URL . 'modules/students/', 'error', 'Student not found.');
