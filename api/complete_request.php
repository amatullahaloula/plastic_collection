<?php
// api/complete_request.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_login();
header('Content-Type: application/json');

if ($_SESSION['user']['role'] !== 'cleaner') {
    echo json_encode(['success' => false, 'error' => 'Only cleaners can complete requests.']);
    exit;
}

$id = (int)($_POST['request_id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid id.']);
    exit;
}

try {
    $pdo->beginTransaction();
    // check request exists and accepted by this cleaner
    $stmt = $pdo->prepare("SELECT student_id, bottles, cleaner_id, status FROM collection_requests WHERE id = ? FOR UPDATE");
    $stmt->execute([$id]);
    $r = $stmt->fetch();
    if (!$r) { $pdo->rollBack(); echo json_encode(['success'=>false,'error'=>'Not found']); exit; }
    if ($r['status'] !== 'accepted' || $r['cleaner_id'] != $_SESSION['user']['id']) {
        $pdo->rollBack();
        echo json_encode(['success'=>false,'error'=>'Cannot complete this request.']);
        exit;
    }
    // insert into collection_records
    $stmt = $pdo->prepare("INSERT INTO collection_records (request_id, student_id, cleaner_id, bottles) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id, $r['student_id'], $_SESSION['user']['id'], $r['bottles']]);
    // update request status
    $stmt = $pdo->prepare("UPDATE collection_requests SET status = 'completed', updated_at = NOW() WHERE id = ?");
    $stmt->execute([$id]);
    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => 'Server error.']);
}
