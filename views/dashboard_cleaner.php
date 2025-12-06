<?php
// views/dashboard_cleaner.php
require_once __DIR__ . '/../includes/auth.php';
require_role('cleaner');
require_once __DIR__ . '/../includes/db.php';

$user = $_SESSION['user'] ?? ['nickname' => 'Cleaner'];
function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'collect' && isset($_POST['id'])) {
    $reqId = (int)$_POST['id'];
    try {
        // Mettre à jour uniquement si la request est toujours pending (éviter doublons)
        $stmt = $pdo->prepare("UPDATE collection_requests 
                               SET status = 'completed', collected_by = ?, collected_at = NOW()
                               WHERE id = ? AND status = 'pending'");
        $stmt->execute([$user['id'], $reqId]);
        if ($stmt->rowCount() > 0) {
            $message = "Request #$reqId marked as collected.";
        } else {
            $message = "Request #$reqId could not be marked (maybe already collected).";
        }
    } catch (Exception $e) {
        error_log("cleaner collect error: " . $e->getMessage());
        $message = "Server error when trying to mark collected.";
    }
    // Redirect pour éviter resubmission (GET après POST)
    header("Location: /plastic_collection/views/dashboard_cleaner.php?msg=" . urlencode($message));
    exit;
}

// Chargement des requests pending
try {
    $stmt = $pdo->prepare("SELECT r.*, s.nickname AS student_name
                           FROM collection_requests r
                           LEFT JOIN users s ON s.id = r.student_id
                           WHERE r.status = 'pending'
                           ORDER BY r.created_at ASC");
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $requests = [];
    error_log("cleaner load pending requests error: " . $e->getMessage());
}

if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Cleaner Dashboard</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap">
<style>
    body { font-family:"Inter", Arial, sans-serif; margin:0; background:#f7fbff; color:#0f172a; padding:20px; }
    .wrap { max-width:900px; margin:0 auto; }
    header { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; }
    .card { background:#fff; padding:16px; border-radius:12px; box-shadow: 0 6px 12px rgba(0,0,0,0.06); margin-bottom:12px; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:10px; border-bottom:1px solid #eee; text-align:left; }
    button { padding:8px 12px; border-radius:8px; border:none; cursor:pointer; background:#0f172a; color:#fff; }
    .btn-collect { background:#059669; }
    .small { font-size:13px; color:#374151; }
    .msg { margin-bottom:12px; color:#065f46; }
    .error { color: #b91c1c; }
</style>
</head>
<body>
<div class="wrap">
    <header>
        <h1>Cleaner Dashboard</h1>
        <div>
            <?= h($user['nickname']) ?> &nbsp;|&nbsp;
            <a href="/plastic_collection/api/logout.php">Logout</a>
        </div>
    </header>

    <?php if ($message): ?>
        <div class="card msg"><?= h($message) ?></div>
    <?php endif; ?>

    <section class="card">
        <h3>Pending requests</h3>
        <p class="small">Below are all pending collection requests. Click "Mark as Collected" when you pick them up.</p>

        <?php if (empty($requests)): ?>
            <div class="small">No pending requests at the moment.</div>
        <?php else: ?>
            <div style="overflow:auto; max-height:520px;">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Bottles</th>
                            <th>Location</th>
                            <th>Note</th>
                            <th>Requested At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $r): ?>
                            <tr>
                                <td><?= h($r['id']) ?></td>
                                <td><?= h($r['student_name'] ?? ('#' . $r['student_id'])) ?></td>
                                <td><?= h($r['bottles']) ?></td>
                                <td><?= h($r['location']) ?></td>
                                <td class="small"><?= h($r['note'] ?: '-') ?></td>
                                <td class="small"><?= h($r['created_at']) ?></td>
                                <td>
                                    <form method="post" style="display:inline" onsubmit="return confirm('Mark request #<?= h($r['id']) ?> as collected?');">
                                        <input type="hidden" name="action" value="collect">
                                        <input type="hidden" name="id" value="<?= h($r['id']) ?>">
                                        <button type="submit" class="btn-collect">Mark as Collected</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>
</body>
</html>
