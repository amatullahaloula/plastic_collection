<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Profile</title>
<link rel="stylesheet" href="../css/global_bg.css">

<style>
.container {
    max-width: 750px;
    background: rgba(255,255,255,0.85);
    margin: 40px auto;
    padding: 30px;
    border-radius: 12px;
}

h2 { color: #1b3d6d; margin-bottom: 20px; }

.item {
    background: #f5f7fc;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 12px;
}
</style>
</head>

<body>

<div class="overlay">

<header>
    <div>👤 My Profile</div>
    <div>
        <?php echo $user['nickname']; ?> |
        <a href="../api/logout.php">Logout</a>
    </div>
</header>

<div class="container">

    <h2>Student Information</h2>

    <div class="item"><strong>First Name:</strong> <?= $user['first_name'] ?></div>
    <div class="item"><strong>Last Name:</strong> <?= $user['last_name'] ?></div>
    <div class="item"><strong>Nickname:</strong> <?= $user['nickname'] ?></div>
    <div class="item"><strong>Email:</strong> <?= $user['email'] ?></div>
    <div class="item"><strong>Phone:</strong> <?= $user['phone'] ?></div>
    <div class="item"><strong>Total Bottles Submitted:</strong> <?= $user['total_bottles'] ?? 0 ?></div>

</div>

</div>

</body>
</html>
