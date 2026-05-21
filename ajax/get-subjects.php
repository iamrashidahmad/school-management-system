<?php
/**
 * AJAX - Get Subjects by Class
 */
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$classId = intval($_POST['class_id'] ?? 0);

if ($classId > 0) {
    $stmt = $conn->prepare("SELECT subject_id, subject_name, subject_code FROM subjects WHERE class_id = ? AND status = 1 ORDER BY subject_name");
    $stmt->bind_param("i", $classId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
    
    echo json_encode(['success' => true, 'subjects' => $subjects]);
} else {
    echo json_encode(['success' => false, 'subjects' => []]);
}
