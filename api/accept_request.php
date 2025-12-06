<?php
// api/accept_request.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_login();
header('Content-Type: application/json');

if ($_SESSION['user']['role'] !== 'cleaner') {
    echo json_encode(['success' => false, 'error' => 'Only cleaners can accept requests.']);
    exit;
}

$id = (int)($_POST['request_id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid request id.']);
    exit;
}

try {
    // ensure request is still pending
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("SELECT status FROM collection_requests WHERE id = ? FOR UPDATE");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'Request not found.']);
        exit;
    }
    if ($row['status'] !== 'pending') {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'Request not available.']);
        exit;
    }
    $stmt = $pdo->prepare("UPDATE collection_requests SET status = 'accepted', cleaner_id = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$_SESSION['user']['id'], $id]);
    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => 'Server error.']);
}
