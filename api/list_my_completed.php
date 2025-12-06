<?php
// api/list_my_completed.php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

if ($_SESSION['user']['role'] !== 'cleaner') {
    echo json_encode(['success'=>false,'error'=>'Unauthorized']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM collection_records WHERE cleaner_id = ? ORDER BY collected_at DESC");
    $stmt->execute([$_SESSION['user']['id']]);
    $rows = $stmt->fetchAll();
    echo json_encode(['success'=>true,'data'=>$rows]);
} catch (Exception $e) {
    echo json_encode(['success'=>false,'error'=>'Server error.']);
}
