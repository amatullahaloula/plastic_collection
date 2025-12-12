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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Submit Request - Ashesi Plastic</title>
<link rel="stylesheet" href="../css/style.css">
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

.page-background { 
    background: url('../css/assets/empty_bottle_picture.png') center/cover no-repeat; 
    position: fixed; 
    inset: 0; 
    opacity: 0.15; 
    filter: blur(6px); 
    z-index: -1; 
}

.wrapper { 
    max-width: 700px;
    margin: 30px auto; 
    padding: 30px; 
    background: rgba(255, 255, 255, 0.98); 
    backdrop-filter: blur(12px); 
    border-radius: 18px;
    box-shadow: 0 20px 60px rgba(128, 0, 32, 0.4);
}

.card { 
    background: white; 
    padding: 25px; 
    border-radius: 18px; 
    box-shadow: 0 4px 18px rgba(128, 0, 32, 0.12);
    border-left: 4px solid #800020;
}

.section-title { 
    color: #800020;
    font-size: 28px;
    margin-bottom: 25px;
    border-bottom: 3px solid #800020;
    padding-bottom: 15px;
}

.form-grid { 
    display: grid; 
    gap: 18px; 
}

.msg { 
    margin-top: 15px; 
    font-weight: bold; 
    padding: 12px;
    border-radius: 8px;
    text-align: center;
}

.success { 
    color: #059669; 
    background: #d1fae5;
    border: 1px solid #059669;
}

.error { 
    color: #991b1b; 
    background: #fee2e2;
    border: 1px solid #ef4444;
}

a.btn-back { 
    display: inline-block; 
    padding: 10px 20px; 
    background: #800020; 
    color: white; 
    border-radius: 10px; 
    text-decoration: none; 
    margin-bottom: 20px;
    font-weight: 600;
    transition: background 0.3s;
}

a.btn-back:hover {
    background: #4a0012;
}

label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #333;
}

input, select, textarea { 
    width: 100%;
    padding: 12px; 
    border-radius: 8px; 
    border: 1px solid #ccc; 
    font-size: 16px;
    box-sizing: border-box;
    font-family: "Inter", sans-serif;
}

input[readonly] {
    background: #f3f4f6;
    font-weight: 700;
    color: #800020;
    font-size: 18px;
}

button { 
    width: 100%;
    padding: 14px;
    background: #800020; 
    color: white; 
    border: none;
    border-radius: 10px;
    cursor: pointer; 
    transition: background 0.3s;
    font-size: 16px;
    font-weight: 600;
    margin-top: 10px;
}

button:hover { 
    background: #4a0012; 
}

.reward-display {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    margin: 15px 0;
}

.reward-display h3 {
    margin: 0 0 5px 0;
    font-size: 16px;
}

.reward-display .amount {
    font-size: 32px;
    font-weight: 700;
}
</style>
</head>
<body>
<div class="page-background"></div>
<div class="wrapper">

<a href="./dashboard_student.php" class="btn-back">⬅ Back to Dashboard</a>

<div class="card">
    <h2 class="section-title">📝 Submit Plastic Collection Request</h2>

<form id="requestForm">
    <div class="form-grid">

        <div>
            <label>📍 Campus Location</label>
            <select name="location" required>
                <option value="">-- Select Location --</option>
                <?php foreach($campus_locations as $loc): ?>
                    <option><?= htmlspecialchars($loc); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>🚪 Room Number</label>
            <input name="room_number" required placeholder="e.g., A101, Room 203">
        </div>

        <div>
            <label>🍾 Number of Bottles</label>
            <input name="bottles" id="bottles" type="number" min="1" value="1" required>
        </div>

        <div class="reward-display">
            <h3>💰 Estimated Reward</h3>
            <div class="amount">GH₵ <span id="reward"><?= REWARD_RATE ?></span></div>
        </div>

        <div>
            <label>📝 Additional Note (optional)</label>
            <textarea name="note" rows="3" placeholder="e.g., Leave at the door, Call before arriving..."></textarea>
        </div>

        <button type="submit" id="submitBtn">✓ Submit Request</button>
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
    reward.textContent = (bottles.value * RATE).toFixed(2);
});

document.getElementById('requestForm').addEventListener('submit', async e => {
    e.preventDefault();
    const msg = document.getElementById('reqMsg');
    const btn = document.getElementById('submitBtn');
    
    msg.textContent = 'Sending request...';
    msg.className = 'msg';
    btn.disabled = true;

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
            msg.textContent = '✓ Request submitted successfully!';
            msg.className = 'msg success';
            e.target.reset();
            bottles.value = 1;
            reward.textContent = RATE.toFixed(2);
        } else {
            msg.textContent = data.error || 'Error submitting request.';
            msg.className = 'msg error';
        }
    } catch (err) {
        msg.textContent = '❌ Network or server error.';
        msg.className = 'msg error';
        console.error('Fetch error:', err);
    } finally {
        btn.disabled = false;
    }
});
</script>

</body>
</html>