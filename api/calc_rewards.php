<?php
// api/calc_rewards.php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

// only admin
if ($_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Only admin can calculate rewards.']);
    exit;
}

// params: period_start, period_end (YYYY-MM-DD)
$start = $_POST['period_start'] ?? null;
$end = $_POST['period_end'] ?? null;
if (!$start || !$end) {
    echo json_encode(['success' => false, 'error' => 'Provide period_start and period_end (YYYY-MM-DD).']);
    exit;
}

// rates
$pointsPerBottle = 1;      // 1 point per bottle
$moneyPerBottle = 0.5;     // 0.5 GHS per bottle

try {
    // aggregate bottles per student in period (collection_records.collected_at)
    $stmt = $pdo->prepare("SELECT student_id, SUM(bottles) as total_bottles FROM collection_records WHERE DATE(collected_at) BETWEEN ? AND ? GROUP BY student_id");
    $stmt->execute([$start, $end]);
    $rows = $stmt->fetchAll();

    // insert rewards rows for each student
    $insert = $pdo->prepare("INSERT INTO rewards (student_id, period_start, period_end, bottles, points, amount_money) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($rows as $r) {
        $b = (int)$r['total_bottles'];
        $p = (int)($b * $pointsPerBottle);
        $m = number_format($b * $moneyPerBottle, 2, '.', '');
        $insert->execute([$r['student_id'], $start, $end, $b, $p, $m]);
    }

    echo json_encode(['success' => true, 'count' => count($rows)]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error.']);
}
