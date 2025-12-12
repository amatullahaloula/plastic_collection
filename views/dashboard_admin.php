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
               c.first_name AS cleaner_name
        FROM collection_requests r
        LEFT JOIN users s ON s.id = r.student_id
        LEFT JOIN users c ON c.id = r.cleaner_id
        ORDER BY r.created_at DESC
    ");
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $requests = [];
    error_log("Admin dashboard load requests error: " . $e->getMessage());
}

// Analytics: Top Performing Cleaners
try {
    $stmt = $pdo->query("
        SELECT 
            u.first_name,
            u.last_name,
            COUNT(r.id) as total_requests,
            SUM(r.bottles) as total_bottles
        FROM collection_requests r
        INNER JOIN users u ON u.id = r.cleaner_id
        WHERE r.status_cleaner = 'completed'
        GROUP BY u.id, u.first_name, u.last_name
        ORDER BY total_bottles DESC
        LIMIT 5
    ");
    $topCleaners = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $topCleaners = [];
    error_log("Top cleaners query error: " . $e->getMessage());
}

// Analytics: Revenue Generated (assuming GH₵1.00 per bottle)
$pricePerBottle = 1.00;
try {
    $stmt = $pdo->query("
        SELECT 
            SUM(bottles) as total_bottles,
            COUNT(id) as total_requests
        FROM collection_requests
        WHERE status_cleaner = 'completed'
    ");
    $revenueData = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalBottles = $revenueData['total_bottles'] ?? 0;
    $totalRevenue = $totalBottles * $pricePerBottle;
    $totalRequests = $revenueData['total_requests'] ?? 0;
} catch (Exception $e) {
    $totalBottles = 0;
    $totalRevenue = 0;
    $totalRequests = 0;
    error_log("Revenue query error: " . $e->getMessage());
}

// Analytics: Status Distribution
try {
    $stmt = $pdo->query("
        SELECT 
            status_cleaner,
            COUNT(*) as count
        FROM collection_requests
        GROUP BY status_cleaner
    ");
    $statusDist = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $statusDist = [];
}

// Analytics: Monthly Collection Trend (last 6 months)
try {
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            SUM(bottles) as bottles,
            COUNT(*) as requests
        FROM collection_requests
        WHERE status_cleaner = 'completed'
        AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
    ");
    $monthlyTrend = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $monthlyTrend = [];
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

// Support Requests
try {
    $stmt = $pdo->query("
        SELECT sr.*, u.first_name, u.last_name, u.nickname
        FROM support_requests sr
        LEFT JOIN users u ON u.id = sr.user_id
        ORDER BY sr.created_at DESC
        LIMIT 50
    ");
    $supportRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $supportRequests = [];
    error_log("Support requests error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Ashesi Plastic</title>
<link rel="stylesheet" href="/plastic_collection/css/style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
body { margin:0; padding:0; font-family:"Inter", Arial, sans-serif; background:linear-gradient(135deg, #800020 0%, #4a0012 100%); min-height:100vh; }
.wrap { max-width:1400px; margin:auto; padding:20px; }
header { 
    display:flex; 
    justify-content:space-between; 
    align-items:center; 
    margin-bottom:20px; 
    background:rgba(255,255,255,0.98);
    padding:20px 30px;
    border-radius:12px;
    box-shadow:0 10px 30px rgba(128,0,32,0.3);
}
.header-title { 
    font-size:28px; 
    font-weight:700; 
    color:#800020;
}
.header-title span {
    font-weight:300;
    color:#666;
}
.header-right {
    display:flex;
    gap:15px;
    align-items:center;
}
.analytics-btn {
    background:linear-gradient(135deg, #800020 0%, #4a0012 100%);
    color:white;
    padding:10px 20px;
    border-radius:8px;
    text-decoration:none;
    font-weight:600;
    transition:transform 0.2s, box-shadow 0.2s;
    display:inline-flex;
    align-items:center;
    gap:8px;
}
.analytics-btn:hover {
    transform:translateY(-2px);
    box-shadow:0 6px 20px rgba(128,0,32,0.4);
}
.card { background:white; border-radius:14px; padding:20px; box-shadow:0 6px 15px rgba(128,0,32,0.15); margin-bottom:20px; border-left:4px solid #800020; }
h3 { color:#800020; margin-bottom:12px; font-size:20px; }
table { width:100%; border-collapse:collapse; }
th, td { padding:8px 10px; border-bottom:1px solid #eee; text-align:left; font-size:14px; }
th { background:#f8f9fa; font-weight:600; color:#800020; }
.status-pending { color:#d97706; font-weight:600; }
.status-accepted { color:#059669; font-weight:600; }
.status-completed { color:#16a34a; font-weight:600; }
.status-rejected { color:#ef4444; font-weight:600; }
.small { font-size:12px; color:#666; }
.filters { display:flex; gap:8px; margin-bottom:12px; flex-wrap:wrap; }
input, select { padding:8px; border-radius:6px; border:1px solid #ccc; font-size:14px; }
button { padding:8px 12px; border-radius:6px; border:none; background:#800020; color:white; cursor:pointer; font-weight:600; }
button:hover { background:#4a0012; }
.reset-btn { text-decoration:none; padding:8px 12px; background:#e5e7eb; border-radius:6px; color:#374151; display:inline-block; font-weight:600; }
.reset-btn:hover { background:#d1d5db; }

.stats-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:15px; margin-bottom:20px; }
.stat-card { 
    background:linear-gradient(135deg, #800020 0%, #4a0012 100%); 
    color:white; 
    padding:20px; 
    border-radius:12px;
    transition:transform 0.2s;
    box-shadow:0 6px 15px rgba(128,0,32,0.3);
}
.stat-card:hover {
    transform:translateY(-3px);
}
.stat-card.green { background:linear-gradient(135deg, #10b981 0%, #059669 100%); }
.stat-card.orange { background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.stat-value { font-size:32px; font-weight:700; margin:10px 0; }
.stat-label { font-size:14px; opacity:0.9; }

.analytics-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; }
@media (max-width: 968px) { .analytics-grid { grid-template-columns:1fr; } }

.chart-container { position:relative; height:300px; }
.top-list { list-style:none; padding:0; margin:0; }
.top-list li { padding:12px; border-bottom:1px solid #f0f0f0; display:flex; justify-content:space-between; align-items:center; }
.top-list li:last-child { border-bottom:none; }
.top-list li:hover { background:#f8f9fa; }
.rank { background:#800020; color:white; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:600; margin-right:12px; }
.rank.gold { background:#f59e0b; }
.rank.silver { background:#6b7280; }
.rank.bronze { background:#cd7f32; }

.logout-link {
    color:#800020;
    text-decoration:none;
    font-weight:600;
}
.logout-link:hover {
    text-decoration:underline;
}
</style>
</head>
<body>
<div class="wrap">
    <header>
        <div class="header-title">
            🌍 Ashesi Plastic <span>— Admin Dashboard</span>
        </div>
        <div class="header-right">
            <span style="color:#666;">
                <?= h($user['nickname']) ?>
            </span>
            <a href="../api/logout.php" class="logout-link">Logout</a>
        </div>
    </header>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card green">
            <div class="stat-label">💰 Total Revenue</div>
            <div class="stat-value">GH₵<?= number_format($totalRevenue, 2) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">♻️ Bottles Collected</div>
            <div class="stat-value"><?= number_format($totalBottles) ?></div>
        </div>
        <div class="stat-card orange">
            <div class="stat-label">✅ Completed Requests</div>
            <div class="stat-value"><?= number_format($totalRequests) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">📊 Avg. Bottles/Request</div>
            <div class="stat-value"><?= $totalRequests > 0 ? number_format($totalBottles / $totalRequests, 1) : '0' ?></div>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="analytics-grid">
        <!-- Top Performing Cleaners -->
        <div class="card">
            <h3>🏆 Top Performing Cleaners</h3>
            <ul class="top-list">
                <?php if (empty($topCleaners)): ?>
                    <li style="justify-content:center; color:#999;">No data available</li>
                <?php else: ?>
                    <?php foreach ($topCleaners as $idx => $cleaner): ?>
                        <li>
                            <div style="display:flex; align-items:center;">
                                <span class="rank <?= $idx === 0 ? 'gold' : ($idx === 1 ? 'silver' : ($idx === 2 ? 'bronze' : '')) ?>">
                                    <?= $idx + 1 ?>
                                </span>
                                <div>
                                    <strong><?= h($cleaner['first_name'] . ' ' . $cleaner['last_name']) ?></strong>
                                    <div class="small"><?= h($cleaner['total_requests']) ?> requests</div>
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <strong style="color:#059669; font-size:18px;"><?= number_format($cleaner['total_bottles']) ?></strong>
                                <div class="small">bottles</div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Status Distribution Chart -->
        <div class="card">
            <h3>📊 Request Status Distribution</h3>
            <div class="chart-container">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Monthly Trend Chart -->
    <div class="card">
        <h3>📈 Monthly Collection Trend (Last 6 Months)</h3>
        <div class="chart-container" style="height:250px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Filter Section -->
    <section class="card">
        <h3>🔍 Filter Requests</h3>
        <form method="get" class="filters">
            <select name="status">
                <option value="">All statuses</option>
                <option value="pending" <?= ($statusFilter==='pending')?'selected':'' ?>>Pending</option>
                <option value="completed" <?= ($statusFilter==='completed')?'selected':'' ?>>Completed</option>
            </select>
            <input type="text" name="student" placeholder="Student name" value="<?= h($studentFilter) ?>">
            <button type="submit">Filter</button>
            <a href="/plastic_collection/views/dashboard_admin.php" class="reset-btn">Reset</a>
        </form>
    </section>

    <!-- Requests Table -->
    <section class="card">
        <h3>📋 All Requests (<?= count($filtered) ?>)</h3>
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
                        <th>Cleaner</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($filtered)): ?>
                        <tr><td colspan="8" class="small" style="text-align:center; padding:20px;">No requests found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($filtered as $r): ?>
                            <tr>
                                <td><?= h($r['id']) ?></td>
                                <td><?= h($r['student_name'] ?? ('#'.$r['student_id'])) ?></td>
                                <td><strong><?= h($r['bottles']) ?></strong></td>
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
                                <td>
                                    <?php 
                                        $sc = $r['status_cleaner'] ?? 'pending';
                                        echo ($sc === 'completed') ? h($r['cleaner_name'] ?? '-') : '-';
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Support Requests Section -->
    <section class="card">
        <h3>💬 Support Requests (<?= count($supportRequests) ?>)</h3>
        <div style="overflow:auto; max-height:600px;">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($supportRequests)): ?>
                        <tr><td colspan="7" class="small" style="text-align:center; padding:20px;">No support requests found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($supportRequests as $sr): ?>
                            <tr>
                                <td><?= h($sr['id']) ?></td>
                                <td><?= h($sr['nickname'] ?? $sr['first_name'] . ' ' . $sr['last_name'] ?? 'Guest') ?></td>
                                <td class="small"><?= h($sr['email']) ?></td>
                                <td><?= h($sr['subject'] ?: 'General Inquiry') ?></td>
                                <td class="small" style="max-width:300px;"><?= h(substr($sr['message'], 0, 100)) ?><?= strlen($sr['message']) > 100 ? '...' : '' ?></td>
                                <td>
                                    <span class="<?= $sr['status'] === 'resolved' ? 'status-completed' : 'status-pending' ?>">
                                        <?= h($sr['status']) ?>
                                    </span>
                                </td>
                                <td class="small"><?= h($sr['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
const statusData = <?= json_encode($statusDist) ?>;
const statusLabels = statusData.map(s => s.status_cleaner || 'unknown');
const statusCounts = statusData.map(s => parseInt(s.count));

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusCounts,
            backgroundColor: ['#f59e0b', '#ef4444', '#16a34a', '#800020'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

const trendData = <?= json_encode($monthlyTrend) ?>;
const trendLabels = trendData.map(t => t.month);
const trendBottles = trendData.map(t => parseInt(t.bottles));
const trendRequests = trendData.map(t => parseInt(t.requests));

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trendLabels,
        datasets: [{
            label: 'Bottles Collected',
            data: trendBottles,
            borderColor: '#800020',
            backgroundColor: 'rgba(128, 0, 32, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Requests',
            data: trendRequests,
            borderColor: '#059669',
            backgroundColor: 'rgba(5, 150, 105, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
</body>
</html>