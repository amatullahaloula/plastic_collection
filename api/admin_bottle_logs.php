<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin — Bottle Logs</title>

<link rel="stylesheet" href="../css/global_bg.css">

<style>
.container {
    max-width: 900px;
    margin: 40px auto;
    background: rgba(255,255,255,0.85);
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

h2 {
    color: #1b3d6d;
    margin-bottom: 20px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    background: #1b3d6d;
    color: white;
    padding: 10px;
}

.table td {
    background: #f8f9fc;
    padding: 10px;
}

.table tr:nth-child(even) td {
    background: #eef1f7;
}
</style>

</head>
<body>

<div class="overlay">

<header>
    <div>📦 Ashesi Plastic — Collection Logs</div>
    <div>
        <?php echo htmlspecialchars($user['nickname']); ?> |
        <a href="../logout.php">Logout</a>
    </div>
</header>

<div class="container">
    <h2>All Bottle Collections</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Bottles</th>
                <th>Location</th>
                <th>Date</th>
                <th>Cleaner</th>
            </tr>
        </thead>
        <tbody id="logsBody">
            <tr><td colspan="5">Loading…</td></tr>
        </tbody>
    </table>
</div>

</div>

<script>
async function loadLogs() {
    const res = await fetch('./api/admin_bottle_logs.php');
    const j = await res.json();

    const tbody = document.getElementById("logsBody");
    tbody.innerHTML = "";

    if (!j.success) {
        tbody.innerHTML = "<tr><td colspan='5'>Error loading data</td></tr>";
        return;
    }

    j.data.forEach(row => {
        tbody.innerHTML += `
            <tr>
                <td>${row.student_nick} (${row.student_first} ${row.student_last})</td>
                <td>${row.bottles}</td>
                <td>${row.location}</td>
                <td>${row.collected_at}</td>
                <td>${row.cleaner_nick ?? "—"}</td>
            </tr>
        `;
    });
}

loadLogs();
</script>

</body>
</html>
