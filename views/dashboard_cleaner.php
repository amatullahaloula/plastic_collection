<?php
// views/dashboard_cleaner.php
require_once __DIR__ . '/../includes/auth.php';
require_role('cleaner');
require_once __DIR__ . '/../includes/db.php';

$user = $_SESSION['user'] ?? ['id' => 0, 'nickname' => 'Cleaner'];

function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$message = '';

// Handle POST actions (accept / reject / collect)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action']) && !empty($_POST['id'])) {
    $reqId = (int)$_POST['id'];
    $action = $_POST['action'];
    try {
        if ($action === 'accept') {
            $stmt = $pdo->prepare("UPDATE collection_requests
                                   SET status_cleaner='accepted', 
                                       cleaner_id=:uid, 
                                       updated_at=NOW()
                                   WHERE id=:id AND status_cleaner='pending'");
            $stmt->execute([':uid'=>$user['id'], ':id'=>$reqId]);
            
            if ($stmt->rowCount()) {
                $message = "Request #$reqId accepted.";
            } else {
                $message = "Request #$reqId cannot be accepted (may already be processed).";
            }

        } elseif ($action === 'reject') {
            $stmt = $pdo->prepare("UPDATE collection_requests
                                   SET status_cleaner='rejected', 
                                       cleaner_id=:uid, 
                                       updated_at=NOW()
                                   WHERE id=:id AND status_cleaner='pending'");
            $stmt->execute([':uid'=>$user['id'], ':id'=>$reqId]);
            
            if ($stmt->rowCount()) {
                $message = "Request #$reqId rejected.";
            } else {
                $message = "Request #$reqId cannot be rejected (may already be processed).";
            }

        } elseif ($action === 'collect') {
            $stmt = $pdo->prepare("UPDATE collection_requests
                                   SET status_cleaner='completed', 
                                       status_admin='completed', 
                                       cleaner_id=:uid, 
                                       updated_at=NOW()
                                   WHERE id=:id AND status_cleaner IN ('accepted','pending')");
            $stmt->execute([':uid'=>$user['id'], ':id'=>$reqId]);
            
            if ($stmt->rowCount()) {
                $message = "Request #$reqId marked as collected.";
            } else {
                $message = "Request #$reqId cannot be collected (may already be processed).";
            }
        } else {
            $message = "Unknown action.";
        }
    } catch (PDOException $e) {
        error_log("Cleaner action error: " . $e->getMessage());
        $message = "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        error_log("Cleaner action error: " . $e->getMessage());
        $message = "Server error while updating request.";
    }

    header("Location: " . $_SERVER['PHP_SELF'] . '?msg=' . urlencode($message));
    exit;
}

// Load pending requests for Cleaner
try {
    $stmt = $pdo->prepare("SELECT r.*, u.nickname AS student_name
                           FROM collection_requests r
                           LEFT JOIN users u ON u.id = r.student_id
                           WHERE r.status_cleaner IN ('pending','accepted')
                           ORDER BY r.created_at ASC");
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $requests = [];
    error_log("Cleaner load requests error: " . $e->getMessage());
}

$message = $_GET['msg'] ?? '';
?>

<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Cleaner Dashboard</title>
<style>
body { font-family:Inter, Arial; margin:0; background:#f7fbff; color:#0f172a; padding:20px; }
.wrap { max-width:1000px; margin:0 auto; }
.header { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; }
.card { background:#fff; padding:16px; border-radius:12px; box-shadow:0 6px 12px rgba(0,0,0,0.06); margin-bottom:12px; }
table { width:100%; border-collapse:collapse; }
th, td { padding:10px; border-bottom:1px solid #eee; text-align:left; vertical-align: middle; }
button { padding:8px 12px; border-radius:8px; border:none; cursor:pointer; color:#fff; }
.btn-accept { background:#059669; margin-right:6px; }
.btn-reject { background:#ef4444; margin-right:6px; }
.btn-collect { background:#1d4ed8; }
.small { font-size:13px; color:#374151; }
.msg { margin-bottom:12px; padding:12px; border-radius:8px; background:#d1fae5; color:#065f46; }
.msg-error { background:#fee2e2; color:#991b1b; }
.actions-form { display:inline-block; margin:0; padding:0; }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1>Cleaner Dashboard</h1>
        <div>
            <?= h($user['nickname']) ?> | <a href="/plastic_collection/api/logout.php">Logout</a>
        </div>
    </div>

    <?php if($message): ?>
        <div class="card msg <?= strpos($message, 'error') !== false ? 'msg-error' : '' ?>">
            <?= h($message) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h3>Pending requests</h3>
        <p class="small">Accept to claim, Reject to refuse, Collect when done.</p>

        <?php if(empty($requests)): ?>
            <div class="small">No pending requests.</div>
        <?php else: ?>
            <div style="overflow:auto; max-height:640px;">
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
                <?php foreach($requests as $r): ?>
                    <tr>
                        <td><?= h($r['id']) ?></td>
                        <td><?= h($r['student_name'] ?? ('#'.$r['student_id'])) ?></td>
                        <td><?= h($r['bottles']) ?></td>
                        <td><?= h($r['location']) ?></td>
                        <td class="small"><?= h($r['note'] ?: '-') ?></td>
                        <td class="small"><?= h($r['created_at']) ?></td>
                        <td>
                            <?php if($r['status_cleaner']=='pending'): ?>
                                <form class="actions-form" method="post" onsubmit="return confirm('Accept request #<?= h($r['id']) ?> ?');">
                                    <input type="hidden" name="action" value="accept">
                                    <input type="hidden" name="id" value="<?= h($r['id']) ?>">
                                    <button type="submit" class="btn-accept">Accept</button>
                                </form>
                                <form class="actions-form" method="post" onsubmit="return confirm('Reject request #<?= h($r['id']) ?> ?');">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="id" value="<?= h($r['id']) ?>">
                                    <button type="submit" class="btn-reject">Reject</button>
                                </form>
                            <?php endif; ?>
                            <?php if($r['status_cleaner']=='accepted'): ?>
                                <form class="actions-form" method="post" onsubmit="return confirm('Mark request #<?= h($r['id']) ?> as collected?');">
                                    <input type="hidden" name="action" value="collect">
                                    <input type="hidden" name="id" value="<?= h($r['id']) ?>">
                                    <button type="submit" class="btn-collect">Collect</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>

</div>
</body>
</html>