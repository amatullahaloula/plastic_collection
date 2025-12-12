<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_once __DIR__ . '/../includes/db.php';

$user = $_SESSION['user'];

$campus_locations = [
    'Ephraim Amu (Hakuna)',
    'Oteng Korankye',
    'Walter Sisulu',
    'Wangari Maathai',
    'Kofi Tawiah',
    '2H',
    '2D',
    'Library',
    'Lab',
    'SELC',
    'Courtyard',
    'Other'
];

const REWARD_RATE = 1.00;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Submit Request - Ashesi Plastic</title>
<link rel="stylesheet" href="../css/style.css">
<style>
body { margin:0; padding:0; font-family:sans-serif; background:#f6f6f6; }
.page-background { background:url('../css/assets/empty_bottle_picture.png') center/cover no-repeat; position:fixed; inset:0; opacity:.35; filter:blur(6px); z-index:-1; }
.wrapper { margin:30px; padding:25px; background:rgba(255,255,255,0.7); backdrop-filter:blur(12px); border-radius:18px; }
.card { background:white; padding:20px; border-radius:18px; box-shadow:0 4px 18px rgba(0,0,0,0.08); }
.section-title { color:#5a0016; }
.form-grid { display:grid; gap:12px; }
.msg { margin-top:10px; font-weight:bold; }
.success { color:green; }
.error { color:red; }
a.btn-back { display:inline-block; padding:10px 15px; background:#5a0016; color:white; border-radius:10px; text-decoration:none; margin-bottom:15px; }
input, select, textarea, button { padding:10px; border-radius:8px; border:1px solid #c8c8c8; font-size:16px; }
button { background:#1b3d6d; color:white; cursor:pointer; transition:0.3s; }
button:hover { background:#224c87; }
</style>
</head>
<body>
<div class="page-background"></div>
<div class="wrapper">

<a href="./dashboard_student.php" class="btn-back">⬅ Back</a>

<div class="card">
    <h2 class="section-title">Submit Plastic Collection Request </h2>

<form id="requestForm">
    <div class="form-grid">

        <div>
            <label>Campus Location</label>
            <select name="location" required>
                <option value="">-- Select --</option>
                <?php foreach($campus_locations as $loc): ?>
                    <option><?= htmlspecialchars($loc); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Room Number</label>
            <input name="room_number" required placeholder="A101">
        </div>

        <div>
            <label>Bottles</label>
            <input name="bottles" id="bottles" type="number" min="1" value="1" required>
        </div>

        <div>
            <label>Estimated Reward (GHS)</label>
            <input id="reward" readonly value="<?= REWARD_RATE ?>">
        </div>

        <div>
            <label>Note (optional)</label>
            <textarea name="note"></textarea>
        </div>

        <button type="submit" id="submitBtn">Submit Request</button>
        <div id="reqMsg" class="msg"></div>

    </div>
</form>

</div>
</div>

<script>
const RATE = <?= REWARD_RATE ?>;
const bottles = document.getElementById('bottles');
const reward = document.getElementById('reward');

bottles.addEventListener('input', () => {
    reward.value = (bottles.value * RATE).toFixed(2);
});

document.getElementById('requestForm').addEventListener('submit', async e => {
    e.preventDefault();
    const msg = document.getElementById('reqMsg');
    msg.textContent = 'Sending...';
    msg.className = 'msg';

    const formData = new FormData(e.target);

    try {
        const res = await fetch('../api/create_request.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        });

        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const data = await res.json();

        if (data.success) {
            msg.textContent = 'Request submitted successfully!';
            msg.className = 'msg success';
            e.target.reset();
            bottles.value = 1;
            reward.value = RATE.toFixed(2);
        } else {
            msg.textContent = data.error || 'Error submitting request.';
            msg.className = 'msg error';
        }
    } catch (err) {
        msg.textContent = 'Network or server error.';
        msg.className = 'msg error';
        console.error('Fetch error:', err);
    }
});
</script>

</body>
</html>
