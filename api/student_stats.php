<?php
// api/student_stats.php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

if ($_SESSION['user']['role'] !== 'student') {
    echo json_encode(['success'=>false,'error'=>'Unauthorized']);
    exit;
}

$uid = $_SESSION['user']['id'];

try {
    $stmt = $pdo->prepare("SELECT IFNULL(SUM(bottles),0) AS total_bottles FROM collection_records WHERE student_id = ?");
    $stmt->execute([$uid]);
    $total = (int)$stmt->fetchColumn();
    echo json_encode(['success'=>true,'total_bottles'=>$total]);
} catch (Exception $e) {
    echo json_encode(['success'=>false,'error'=>'Server error.']);
}
