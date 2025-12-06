<?php
// api/list_my_requests.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_login();
header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("SELECT r.*, u.nickname AS student_nick FROM collection_requests r JOIN users u ON r.student_id = u.id WHERE r.student_id = ? ORDER BY r.created_at DESC");
    $stmt->execute([$_SESSION['user']['id']]);
    $rows = $stmt->fetchAll();
    echo json_encode(['success'=>true,'data'=>$rows]);
} catch (Exception $e) {
    echo json_encode(['success'=>false,'error'=>'Server error.']);
}
