<?php
/**
 * AJAX - Keep Session Alive
 */
require_once '../includes/session.php';
$_SESSION['last_activity'] = time();
echo json_encode(['success' => true]);
