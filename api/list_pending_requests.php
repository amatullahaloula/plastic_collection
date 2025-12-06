<?php
// api/list_pending_requests.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

// Only cleaners and admins may list pending for collection view
$role = $_SESSION['user']['role'] ?? null;

if (!isset($_SESSION['user'])) {
    attempt_remember_login();
}
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['cleaner','admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT r.*, u.first_name AS student_first, u.last_name AS student_last, u.nickname AS student_nick FROM collection_requests r JOIN users u ON r.student_id = u.id WHERE r.status = 'pending' ORDER BY r.created_at DESC");
    $stmt->execute();
    $rows = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error.']);
}
