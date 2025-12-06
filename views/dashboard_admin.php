<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/db.php';

$user = $_SESSION['user'] ?? ['nickname' => 'Admin'];

// Pour sécuriser l'affichage, on échappe toujours les sorties.
function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Charger les requests pour l'admin (tout l'historique)
try {
    $sql = "SELECT r.*, 
                   s.nickname AS student_name,
                   c.nickname AS cleaner_name
            FROM collection_requests r
            LEFT JOIN users s ON s.id = r.student_id
            LEFT JOIN users c ON c.id = r.collected_by
            ORDER BY r.created_at DESC
            LIMIT 1000"; // limite pour éviter lecture infinie, change si besoin
    $stmt = $pdo->query($sql);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $requests = [];
    error_log("admin dashboard load requests error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap">
<style>
    body { margin:0; padding:0; font-family:"Inter", Arial, sans-serif; background:#f6f6f6; }
    .page-background { background: url("../assets/bg_bottles.jpg") center/cover no-repeat; position: fixed; inset:0; opacity:0.35; filter: blur(6px); z-index:-1; }
    .transparent-wrapper { background: rgba(255,255,255,0.45); backdrop-filter: blur(12px); }
    .overlay { min-height:100vh; padding:30px; max-width:1200px; margin:auto; }
    header { display:flex; justify-content:space-between; padding:15px 25px; background:rgba(255,255,255,0.85); border-radius:12px; margin-bottom:20px; font-size:18px; font-weight:bold; color:#1b3d6d; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .grid { display:grid; grid-template-columns: repeat(2, 1fr); gap:25px; margin-bottom:25px; }
    .card { background:white; border-radius:14px; padding:20px; box-shadow:0 6px 15px rgba(0,0,0,0.12); }
    h3 { color:#1b3d6d; margin-bottom:12px; font-size:20px; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:8px 10px; text-align:left; border-bottom:1px solid #eee; font-size:14px; }
    .status-pending { color: #d97706; font-weight:600; }
    .status-completed { color: #16a34a; font-weight:600; }
    .small { font-size:12px; color:#666; }
    .filters { display:flex; gap:8px; margin-bottom:12px; }
    input, select { padding:8px; border-radius:6px; border:1px solid #ccc; }
</style>
</head>
<body>
<div class="page-background"></div>
<div class="transparent-wrapper">
    <div class="overlay">
        <header>
            <div>Ashesi Plastic — Admin</div>
            <div>
                <?= h($user['nickname']) ?> &nbsp;|&nbsp;
                <a href="/plastic_collection/api/logout.php" style="color:#1b3d6d; text-decoration:none;">Logout</a>
            </div>
        </header>

        <div class="grid">
            <section class="card">
                <h3>Statistics</h3>
                <div id="stats">Loading...</div>
            </section>

            <section class="card">
                <h3>Top Students</h3>
                <div id="topStudents">Loading...</div>
            </section>

            <section class="card" style="grid-column: 1 / -1;">
                <h3>Requests list</h3>

                <div class="filters">
                    <form id="filterForm" method="get" style="display:flex; gap:8px; align-items:center;">
                        <select name="status">
                            <option value="">All statuses</option>
                            <option value="pending" <?= (($_GET['status'] ?? '') === 'pending') ? 'selected' : '' ?>>Pending</option>
                            <option value="completed" <?= (($_GET['status'] ?? '') === 'completed') ? 'selected' : '' ?>>Completed</option>
                        </select>
                        <input type="text" name="student" placeholder="Student name" value="<?= h($_GET['student'] ?? '') ?>">
                        <button type="submit">Filter</button>
                        <a href="/plastic_collection/views/dashboard_admin.php" style="margin-left:8px; text-decoration:none;">Reset</a>
                    </form>
                </div>

                <?php
                // Application simple des filtres côté serveur (GET)
                $filtered = $requests;
                $statusFilter = $_GET['status'] ?? '';
                $studentFilter = trim($_GET['student'] ?? '');
                if ($statusFilter !== '') {
                    $filtered = array_filter($filtered, function($r) use ($statusFilter) {
                        return ($r['status'] ?? '') === $statusFilter;
                    });
                }
                if ($studentFilter !== '') {
                    $filtered = array_filter($filtered, function($r) use ($studentFilter) {
                        return stripos($r['student_name'] ?? '', $studentFilter) !== false;
                    });
                }
                ?>

                <div style="overflow:auto; max-height:420px; margin-top:12px;">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Bottles</th>
                                <th>Location</th>
                                <th>Note</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Collected by</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($filtered)): ?>
                            <tr><td colspan="8" class="small">No requests found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($filtered as $r): ?>
                                <tr>
                                    <td><?= h($r['id']) ?></td>
                                    <td><?= h($r['student_name'] ?? ('#' . $r['student_id'])) ?></td>
                                    <td><?= h($r['bottles']) ?></td>
                                    <td><?= h($r['location']) ?></td>
                                    <td class="small"><?= h($r['note'] ?: '-') ?></td>
                                    <td class="small"><?= h($r['created_at']) ?></td>
                                    <td>
                                        <?php if (($r['status'] ?? '') === 'pending'): ?>
                                            <span class="status-pending">Pending</span>
                                        <?php else: ?>
                                            <span class="status-completed">Completed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= h($r['cleaner_name'] ?? ($r['collected_by'] ? ('#' . $r['collected_by']) : '-')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </section>
        </div>

    </div>
</div>

<script>
async function loadStats() {
    try {
        const res = await fetch('/plastic_collection/api/admin_stats.php');
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const j = await res.json();
        if (!j.success) {
            document.getElementById('stats').innerHTML = '<div class="small">' + (j.error || 'Error loading stats') + '</div>';
            return;
        }
        document.getElementById('stats').innerHTML = `
            <div class="small">Total bottles: ${j.total_bottles}</div>
            <div class="small">Students: ${j.students_count}</div>
            <div class="small">Cleaners: ${j.cleaners_count}</div>
        `;
        const top = document.getElementById('topStudents');
        top.innerHTML = j.top_students && j.top_students.length
            ? j.top_students.map(s => `<div class="small">${s.nickname} — ${s.total} bottles</div>`).join('')
            : '<div class="small">No students data yet.</div>';
    } catch (err) {
        console.error('Failed to load stats', err);
        document.getElementById('stats').innerHTML = '<div class="small">Failed to load stats.</div>';
        document.getElementById('topStudents').innerHTML = '<div class="small">Failed to load top students.</div>';
    }
}
document.addEventListener('DOMContentLoaded', loadStats);
</script>
</body>
</html>
