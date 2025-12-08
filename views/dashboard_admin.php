<?php
// views/dashboard_admin.php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/db.php';

$user = $_SESSION['user'] ?? ['nickname' => 'Admin'];

function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Load all requests
try {
    $stmt = $pdo->query("
        SELECT r.*, 
               s.nickname AS student_name,
               c.nickname AS cleaner_name
        FROM collection_requests r
        LEFT JOIN users s ON s.id = r.student_id
        LEFT JOIN users c ON c.id = r.collected_by
        ORDER BY r.created_at DESC
    ");
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $requests = [];
    error_log("Admin dashboard load requests error: " . $e->getMessage());
}

// Apply simple GET filters
$statusFilter = $_GET['status'] ?? '';
$studentFilter = trim($_GET['student'] ?? '');

$filtered = array_filter($requests, function($r) use ($statusFilter, $studentFilter) {
    $match = true;
    if ($statusFilter !== '') {
        $match = ($r['status_admin'] ?? '') === $statusFilter;
    }
    if ($match && $studentFilter !== '') {
        $match = stripos($r['student_name'] ?? '', $studentFilter) !== false;
    }
    return $match;
});
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="/plastic_collection/css/style.css">
<style>
body { margin:0; padding:0; font-family:"Inter", Arial, sans-serif; background:#f6f6f6; }
.wrap { max-width:1200px; margin:auto; padding:20px; }
header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.card { background:white; border-radius:14px; padding:20px; box-shadow:0 6px 15px rgba(0,0,0,0.12); margin-bottom:20px; }
h3 { color:#1b3d6d; margin-bottom:12px; font-size:20px; }
table { width:100%; border-collapse:collapse; }
th, td { padding:8px 10px; border-bottom:1px solid #eee; text-align:left; font-size:14px; }
.status-pending { color:#d97706; font-weight:600; }
.status-accepted { color:#059669; font-weight:600; }
.status-completed { color:#16a34a; font-weight:600; }
.status-rejected { color:#ef4444; font-weight:600; }
.small { font-size:12px; color:#666; }
.filters { display:flex; gap:8px; margin-bottom:12px; }
input, select { padding:6px; border-radius:6px; border:1px solid #ccc; }
</style>
</head>
<body>
<div class="wrap">
    <header>
        <div>Ashesi Plastic — Admin</div>
        <div>
            <?= h($user['nickname']) ?> &nbsp;|&nbsp;
            <a href="/plastic_collection/api/logout.php" style="text-decoration:none;">Logout</a>
        </div>
    </header>

    <section class="card">
        <h3>Filter Requests</h3>
        <form method="get" class="filters">
            <select name="status">
                <option value="">All statuses</option>
                <option value="pending" <?= ($statusFilter==='pending')?'selected':'' ?>>Pending</option>
                <option value="completed" <?= ($statusFilter==='completed')?'selected':'' ?>>Completed</option>
            </select>
            <input type="text" name="student" placeholder="Student name" value="<?= h($studentFilter) ?>">
            <button type="submit">Filter</button>
            <a href="/plastic_collection/views/dashboard_admin.php" style="text-decoration:none; padding:6px 12px; background:#ddd; border-radius:6px;">Reset</a>
        </form>
    </section>

    <section class="card">
        <h3>Requests list</h3>
        <div style="overflow:auto; max-height:600px;">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Bottles</th>
                        <th>Location</th>
                        <th>Note</th>
                        <th>Requested At</th>
                        <th>Cleaner Status</th>
                        <th>Admin Status</th>
                        <th>Cleaner</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($filtered)): ?>
                        <tr><td colspan="9" class="small">No requests found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($filtered as $r): ?>
                            <tr>
                                <td><?= h($r['id']) ?></td>
                                <td><?= h($r['student_name'] ?? ('#'.$r['student_id'])) ?></td>
                                <td><?= h($r['bottles']) ?></td>
                                <td><?= h($r['location']) ?></td>
                                <td class="small"><?= h($r['note'] ?: '-') ?></td>
                                <td class="small"><?= h($r['created_at']) ?></td>
                                <td class="small">
                                    <?php 
                                        $sc = $r['status_cleaner'] ?? 'pending';
                                        $class = 'status-pending';
                                        if ($sc==='accepted') $class='status-accepted';
                                        elseif ($sc==='completed') $class='status-completed';
                                        elseif ($sc==='rejected') $class='status-rejected';
                                    ?>
                                    <span class="<?= $class ?>"><?= h($sc) ?></span>
                                </td>
                                <td class="small"><?= h($r['status_admin'] ?? 'pending') ?></td>
                                <td><?= h($r['cleaner_name'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
</body>
</html>
