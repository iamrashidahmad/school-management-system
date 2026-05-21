<?php
/**
 * Calculate Results
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$examId = intval($_GET['exam_id'] ?? 0);

if ($examId) {
    // Get exam info
    $exam = $conn->query("SELECT * FROM exams WHERE exam_id=$examId")->fetch_assoc();
    $examType = $conn->query("SELECT * FROM exam_types WHERE exam_type_id={$exam['exam_type_id']}")->fetch_assoc();
    
    // Get all students for this class
    $students = $conn->query("SELECT student_id FROM students WHERE class_id={$exam['class_id']} AND status='active'");
    
    while ($st = $students->fetch_assoc()) {
        $studentId = $st['student_id'];
        
        // Calculate totals
        $marksStmt = $conn->prepare("SELECT SUM(obtained_marks) as total_obtained, SUM(total_marks) as total_marks FROM student_marks WHERE exam_id=? AND student_id=?");
        $marksStmt->bind_param("ii", $examId, $studentId);
        $marksStmt->execute();
        $marks = $marksStmt->get_result()->fetch_assoc();
        
        if ($marks['total_marks'] > 0) {
            $obtained = $marks['total_obtained'];
            $total = $marks['total_marks'];
            $percentage = round(($obtained / $total) * 100, 2);
            $grade = calculateGrade($percentage);
            $gradeName = $grade['grade_name'];
            $gradePoint = $grade['grade_point'];
            $status = $percentage >= 40 ? 'Pass' : 'Fail';
            
            // Check existing result
            $check = $conn->prepare("SELECT result_id FROM results WHERE student_id=? AND exam_id=?");
            $check->bind_param("ii", $studentId, $examId);
            $check->execute();
            
            if ($check->get_result()->num_rows > 0) {
                $upd = $conn->prepare("UPDATE results SET total_obtained=?, total_marks=?, percentage=?, grade=?, grade_point=?, status=?, remarks=? WHERE student_id=? AND exam_id=?");
                $upd->bind_param("dddsdssii", $obtained, $total, $percentage, $gradeName, $gradePoint, $status, '', $studentId, $examId);
                $upd->execute();
            } else {
                $ins = $conn->prepare("INSERT INTO results (student_id, exam_id, class_id, total_obtained, total_marks, percentage, grade, grade_point, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $ins->bind_param("iiiddddsd", $studentId, $examId, $exam['class_id'], $obtained, $total, $percentage, $gradeName, $gradePoint, $status);
                $ins->execute();
            }
        }
    }
    
    // Calculate class positions
    $positions = $conn->query("SELECT result_id FROM results WHERE exam_id=$examId ORDER BY percentage DESC");
    $position = 1;
    while ($pos = $positions->fetch_assoc()) {
        $conn->query("UPDATE results SET class_position=$position WHERE result_id={$pos['result_id']}");
        $position++;
    }
    
    logActivity(getCurrentUserId(), 'Results Calculated', "Calculated results for Exam ID: $examId");
    redirect(BASE_URL.'modules/exams/results.php?exam_id='.$examId, 'success', 'Results calculated successfully.');
}
redirect(BASE_URL.'modules/exams/', 'error', 'Invalid exam.');
