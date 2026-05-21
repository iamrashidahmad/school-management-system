<?php
/**
 * AJAX - Get Sections by Class
 */
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$classId = intval($_POST['class_id'] ?? 0);

if ($classId > 0) {
    $stmt = $conn->prepare("SELECT section_id, section_name FROM sections WHERE class_id = ? AND status = 1 ORDER BY section_name");
    $stmt->bind_param("i", $classId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sections = [];
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }
    
    echo json_encode(['success' => true, 'sections' => $sections]);
} else {
    echo json_encode(['success' => false, 'sections' => []]);
}
