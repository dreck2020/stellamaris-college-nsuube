<?php
// admin/keep-alive.php - Keep session alive
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    // Update last activity timestamp
    $_SESSION['last_activity'] = time();
    echo json_encode(['success' => true, 'message' => 'Session extended']);
} else {
    echo json_encode(['success' => false, 'message' => 'Session expired']);
}
?>