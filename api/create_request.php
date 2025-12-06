<?php
session_start();
header('Content-Type: application/json');


error_log("Request received at " . date('Y-m-d H:i:s'));
error_log("POST data: " . print_r($_POST, true));


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false, 'error'=>'Invalid request method']);
    exit;
}

require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_once __DIR__ . '/../includes/db.php';

$user = $_SESSION['user'];
$user_id = $user['id'];

$location = trim($_POST['location'] ?? '');
$room_number = trim($_POST['room_number'] ?? '');
$bottles = (int) ($_POST['bottles'] ?? 0);
$note = trim($_POST['note'] ?? '');

if (!$location || !$room_number || $bottles < 1) {
    echo json_encode(['success'=>false, 'error'=>'All required fields must be filled']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO collection_requests (student_id, location, bottles, note, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->execute([$user_id, $location, $bottles, $note]);

    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    error_log("DB Insert Error: ".$e->getMessage());
    echo json_encode(['success'=>false, 'error'=>'Database error']);
}
