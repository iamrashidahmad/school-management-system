<?php
/**
 * Toggle Publish Results
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$examId = intval($_GET['id'] ?? 0);

if ($examId) {
    $exam = $conn->query("SELECT * FROM exams WHERE exam_id=$examId")->fetch_assoc();
    $newStatus = $exam['publish_result'] ? 0 : 1;
    $conn->query("UPDATE exams SET publish_result=$newStatus WHERE exam_id=$examId");
    logActivity(getCurrentUserId(), 'Exam Publish Toggled', "Exam ID: $examId, Status: " . ($newStatus ? 'Published' : 'Unpublished'));
    redirect(BASE_URL.'modules/exams/', 'success', 'Exam status updated.');
}
redirect(BASE_URL.'modules/exams/', 'error', 'Invalid request.');
