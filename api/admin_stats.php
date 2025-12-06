<?php
// api/admin_stats.php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

if ($_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $totalBottlesStmt = $pdo->query("SELECT IFNULL(SUM(bottles),0) AS total_bottles FROM collection_records");
    $totalBottles = $totalBottlesStmt->fetchColumn();

    $studentsStmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'");
    $cleanersStmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'cleaner'");

    $topStudentsStmt = $pdo->query("SELECT u.nickname, u.first_name, u.last_name, SUM(r.bottles) AS total FROM collection_records r JOIN users u ON r.student_id = u.id GROUP BY r.student_id ORDER BY total DESC LIMIT 5");
    $topStudents = $topStudentsStmt->fetchAll();

    echo json_encode([
        'success'=>true,
        'total_bottles' => (int)$totalBottles,
        'students_count' => (int)$studentsStmt->fetchColumn(),
        'cleaners_count' => (int)$cleanersStmt->fetchColumn(),
        'top_students' => $topStudents
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error.']);
}
