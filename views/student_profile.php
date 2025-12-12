<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_once __DIR__ . '/../includes/db.php';

$user = $_SESSION['user'];

// Get total bottles submitted by this student
try {
    $stmt = $pdo->prepare("
        SELECT SUM(bottles) as total_bottles, COUNT(*) as total_requests
        FROM collection_requests
        WHERE student_id = :student_id
    ");
    $stmt->execute([':student_id' => $user['id']]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalBottles = $stats['total_bottles'] ?? 0;
    $totalRequests = $stats['total_requests'] ?? 0;
} catch (Exception $e) {
    $totalBottles = 0;
    $totalRequests = 0;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile - Ashesi Plastic</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<style>
body {
    margin: 0;
    padding: 0;
    font-family: "Inter", sans-serif;
    background: linear-gradient(135deg, #800020 0%, #4a0012 100%);
    min-height: 100vh;
    padding: 20px;
}

.overlay {
    max-width: 800px;
    margin: 0 auto;
}

header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: rgba(255, 255, 255, 0.98);
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(128, 0, 32, 0.3);
}

header > div:first-child {
    font-size: 24px;
    font-weight: 700;
    color: #800020;
}

header a {
    color: #800020;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s;
}

header a:hover {
    color: #4a0012;
}

.container {
    background: rgba(255, 255, 255, 0.98);
    margin: 0 auto;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 20px 60px rgba(128, 0, 32, 0.4);
}

h2 { 
    color: #800020; 
    margin-bottom: 25px;
    font-size: 28px;
    border-bottom: 3px solid #800020;
    padding-bottom: 15px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 30px;
}

.stat-card {
    background: linear-gradient(135deg, #800020 0%, #4a0012 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(128, 0, 32, 0.3);
}

.stat-card.green {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-value {
    font-size: 36px;
    font-weight: 700;
    margin: 10px 0;
}

.stat-label {
    font-size: 14px;
    opacity: 0.9;
}

.item {
    background: #f8f9fa;
    padding: 18px;
    border-radius: 10px;
    margin-bottom: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-left: 4px solid #800020;
    transition: transform 0.3s, box-shadow 0.3s;
}

.item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(128, 0, 32, 0.15);
}

.item strong {
    color: #800020;
    font-weight: 600;
}

.item span {
    color: #374151;
    font-weight: 500;
}

.back-btn {
    display: inline-block;
    padding: 10px 20px;
    background: #800020;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    margin-bottom: 20px;
    transition: background 0.3s;
}

.back-btn:hover {
    background: #4a0012;
}

.section-divider {
    margin: 30px 0;
    border: 0;
    border-top: 2px solid #e5e7eb;
}
</style>
</head>

<body>

<div class="overlay">

<header>
    <div>👤 My Profile</div>
    <div>
        <strong><?php echo htmlspecialchars($user['nickname']); ?></strong> |
        <a href="../api/logout.php">Logout</a>
    </div>
</header>

<div class="container">
    
    <a href="./dashboard_student.php" class="back-btn">← Back to Dashboard</a>

    <h2>📊 Statistics</h2>

    <div class="stats-grid">
        <div class="stat-card green">
            <div class="stat-label">♻️ Total Bottles</div>
            <div class="stat-value"><?= number_format($totalBottles) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">📋 Total Requests</div>
            <div class="stat-value"><?= number_format($totalRequests) ?></div>
        </div>
        <div class="stat-card green">
            <div class="stat-label">💰 Total Earned</div>
            <div class="stat-value">GH₵<?= number_format($totalBottles * 1.00, 2) ?></div>
        </div>
    </div>

    <hr class="section-divider">

    <h2>📝 Personal Information</h2>

    <div class="item">
        <strong>👤 First Name:</strong> 
        <span><?= htmlspecialchars($user['first_name']) ?></span>
    </div>

    <div class="item">
        <strong>👤 Last Name:</strong> 
        <span><?= htmlspecialchars($user['last_name']) ?></span>
    </div>

    <div class="item">
        <strong>✨ Nickname:</strong> 
        <span><?= htmlspecialchars($user['nickname'] ?: 'Not set') ?></span>
    </div>

    <div class="item">
        <strong>📧 Email:</strong> 
        <span><?= htmlspecialchars($user['email']) ?></span>
    </div>

    <div class="item">
        <strong>📱 Phone:</strong> 
        <span><?= htmlspecialchars($user['phone'] ?: 'Not provided') ?></span>
    </div>

    <div class="item">
        <strong>🎓 Class/Year:</strong> 
        <span><?= htmlspecialchars($user['degree'] ?: 'Not specified') ?></span>
    </div>

    <div class="item">
        <strong>📅 Member Since:</strong> 
        <span><?= htmlspecialchars($user['created_at'] ?? 'N/A') ?></span>
    </div>

</div>

</div>

</body>
</html>