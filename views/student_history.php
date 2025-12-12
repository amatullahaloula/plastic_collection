<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_once __DIR__ . '/../includes/db.php';

$user = $_SESSION['user'];
$studentId = $user['id'];

// Fetch collection requests directly from database
try {
    $stmt = $pdo->prepare("
        SELECT 
            r.id,
            r.bottles,
            r.location,
            r.note,
            r.status_cleaner,
            r.status_admin,
            r.created_at,
            r.updated_at,
            c.first_name AS cleaner_name
        FROM collection_requests r
        LEFT JOIN users c ON c.id = r.cleaner_id
        WHERE r.student_id = :student_id
        ORDER BY r.created_at DESC
    ");
    
    $stmt->execute([':student_id' => $studentId]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Student history error: " . $e->getMessage());
    $requests = [];
}

// Calculate stats
$totalRequests = count($requests);
$totalBottles = array_sum(array_column($requests, 'bottles'));
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Collection History</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #800020 0%, #4a0012 100%);
    min-height: 100vh;
    padding: 20px;
}

header {
    max-width: 1200px;
    margin: 0 auto 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(255, 255, 255, 0.98);
    padding: 20px 30px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

header > div:first-child {
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 10px;
}

header a {
    color: #800020;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
}

header a:hover {
    color: #4a0012;
}

/* 🔥 NEW BACK BUTTON (SOLID COLOR, NOT WHITISH) */
.back-btn {
    padding: 10px 18px;
    background-color: #800020;
    color: white !important;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);

    /* Ensure fully solid */
    opacity: 1 !important;
    filter: none !important;
    backdrop-filter: none !important;
}

.back-btn:hover {
    background-color: #4a0012;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    background: rgba(255, 255, 255, 0.98);
    padding: 35px;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
}

.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e5e7eb;
}

.header-section h2 {
    color: #1e293b;
    font-size: 28px;
    font-weight: 700;
}

.stats-badge {
    background: linear-gradient(135deg, #800020 0%, #4a0012 100%);
    color: white;
    padding: 10px 20px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(128, 0, 32, 0.4);
}

.table-wrapper {
    overflow-x: auto;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: white;
}

thead {
    background: linear-gradient(135deg, #800020 0%, #4a0012 100%);
    color: white;
}

thead th {
    padding: 18px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    border: none;
}

tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.3s ease;
}

tbody tr:hover {
    background: #fef2f2;
    transform: translateX(4px);
}

tbody td {
    padding: 18px 16px;
    color: #334155;
    font-size: 14px;
}

.bottles-badge {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    font-weight: 700;
    padding: 8px 16px;
    border-radius: 10px;
}

.status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
}

.status-badge.pending {
    background: #fbbf24;
}

.status-badge.accepted {
    background: #34d399;
}

.status-badge.completed {
    background: #22c55e;
}

.status-badge.rejected {
    background: #ef4444;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    color: #64748b;
}
</style>

</head>
<body>

<header>
    <div>
        <span class="icon">🗂️</span>
        My Collection History
    </div>

    <!-- 🔥 BACK TO DASHBOARD BUTTON -->
    <a href="/plastic_collection/views/dashboard_student.php" class="back-btn">← Back to Dashboard</a>
</header>

<div class="container">
    <div class="header-section">
        <h2>📋 Collection Records</h2>
        <div class="stats-badge">
            <?php echo $totalRequests; ?> Requests • <?php echo $totalBottles; ?> Bottles
        </div>
    </div>
    
    <?php if (empty($requests)): ?>
        <div class="empty-state">
            <div style="font-size: 64px; opacity: 0.5;">📦</div>
            <h3>No Collection History Yet</h3>
            <p>Your requests will appear here once you create them</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bottles</th>
                        <th>Location</th>
                        <th>Note</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th>Collected</th>
                        <th>Cleaner</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $r): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($r['id']); ?></td>
                            <td><span class="bottles-badge"><?php echo htmlspecialchars($r['bottles']); ?></span></td>
                            <td><?php echo htmlspecialchars($r['location']); ?></td>
                            <td><?php echo $r['note'] ? htmlspecialchars($r['note']) : '—'; ?></td>
                            <td><span class="status-badge <?php echo strtolower($r['status_cleaner']); ?>"><?php echo htmlspecialchars($r['status_cleaner']); ?></span></td>
                            <td><?php echo htmlspecialchars($r['created_at']); ?></td>
                            <td><?php echo $r['updated_at'] ? htmlspecialchars($r['updated_at']) : '—'; ?></td>
                            <td><?php echo $r['cleaner_name'] ? htmlspecialchars($r['cleaner_name']) : '—'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
