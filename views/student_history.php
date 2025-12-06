<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Collection History</title>
<link rel="stylesheet" href="../css/global_bg.css">

<style>
.container {
    max-width: 900px;
    margin: 40px auto;
    background: rgba(255,255,255,0.85);
    padding: 25px;
    border-radius: 12px;
}

h2 {
    color: #1b3d6d;
    margin-bottom: 15px;
}

.item {
    background: #f3f6fc;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.status {
    font-weight: bold;
    color: #1b3d6d;
}
</style>

</head>
<body>

<div class="overlay">

<header>
    <div>📦 My Collection History</div>
    <div>
        <?php echo $user['nickname']; ?> |
        <a href="/plastic_collection/api/logout.php">Logout</a>
    </div>
</header>

<div class="container" id="historyList">
    Loading...
</div>

</div>

<script>
async function loadHistory() {
    const res = await fetch('/plastic_collection/api/student_history.php');
    const j = await res.json();
    const box = document.getElementById("historyList");

    if (!j.success) {
        box.textContent = "Error loading history";
        return;
    }

    box.innerHTML = "";

    if (!j.data.length) {
        box.innerHTML = "<div>No collection history yet.</div>";
        return;
    }

    j.data.forEach(r => {
        box.innerHTML += `
            <div class="item">
                <div><strong>Bottles:</strong> ${r.bottles}</div>
                <div><strong>Location:</strong> ${r.location}</div>
                <div><strong>Status:</strong> <span class="status">${r.status}</span></div>
                <div><strong>Requested at:</strong> ${r.created_at}</div>
                <div><strong>Collected at:</strong> ${r.collected_at ?? "—"}</div>
                <div><strong>Cleaner:</strong> ${r.cleaner_nick ?? "—"}</div>
            </div>
        `;
    });
}

loadHistory();
</script>

</body>
</html>
