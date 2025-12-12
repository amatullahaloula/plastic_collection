<?php
// views/recycling_rules.php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');

$user = $_SESSION['user'];
$displayName = $user['nickname'] ?: ($user['first_name'] ?? 'Student');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recycling Rules - Ashesi Plastic</title>

    <link rel="stylesheet" href="/plastic_collection/css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Inter", sans-serif;
            background: #f6f6f6;
        }

        .page-background {
            background: url('/plastic_collection/img/bottles_bg.png') center/cover no-repeat;
            position: fixed;
            inset: 0;
            opacity: 0.35;
            filter: blur(6px);
            z-index: -1;
        }

        .transparent-wrapper {
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            padding: 20px;
            margin: 20px;
        }

        main.container {
            background: rgba(255,255,255,0.85);
            padding: 25px;
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            max-width: 800px;
            margin: auto;
        }

        h2.section-title {
            color: #5a0016;
            margin-bottom: 15px;
        }

        .rule {
            background: white;
            padding: 18px;
            margin-bottom: 15px;
            border-radius: 14px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }

        .rule-icon {
            font-size: 30px;
        }

        a.back {
            display: inline-block;
            margin-bottom: 15px;
            color: #5a0016;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>

<body>

<div class="page-background"></div>
<div class="transparent-wrapper">

<header class="top">
    <div>Recycling Rules</div>
    <div>
        <?= htmlspecialchars($displayName); ?> |
        <a href="./dashboard_student.php" style="color:#5a0016; text-decoration:none;">Dashboard</a> |
        <a href="../api/logout.php" style="color:#5a0016; text-decoration:none;">Logout</a>
    </div>
</header>

<main class="container">
    <h2 class="section-title"> Recycling Rules</h2>

    <p style="margin-bottom:20px;color:#444;">
        Please follow these rules to ensure safe and clean recycling on campus:
    </p>

    <!-- RULE 1 -->
    <div class="rule">
        <div class="rule-icon"></div>
        <div><strong>1. Only plastic bottles are accepted.</strong><br>
        Other plastic materials (bags, cups, wrappers) are not allowed.</div>
    </div>

    <!-- RULE 2 -->
    <div class="rule">
        <div class="rule-icon"></div>
        <div><strong>2. Bottles must be empty and rinsed.</strong><br>
        No liquid, no food waste inside.</div>
    </div>

    <!-- RULE 3 -->
    <div class="rule">
        <div class="rule-icon"></div>
        <div><strong>3. Do not crush bottles too much.</strong><br>
        Slightly press them but keep the shape visible for counting.</div>
    </div>

    <!-- RULE 4 -->
    <div class="rule">
        <div class="rule-icon"></div>
        <div><strong>4. Only Ashesi campus collection points are valid.</strong><br>
        Submit requests only from approved locations.</div>
    </div>

    <!-- RULE 5 -->
    <div class="rule">
        <div class="rule-icon"></div>
        <div><strong>5. Collection time must be respected.</strong><br>
        The cleaner team collects bottles during working hours.</div>
    </div>

    <!-- RULE 6 -->
    <div class="rule">
        <div class="rule-icon"></div>
        <div><strong>6. Keep bottles in a clean bag or container.</strong><br>
        Do not leave bottles on the ground.</div>
    </div>

    <!-- RULE 7 -->
    <div class="rule">
        <div class="rule-icon">❌</div>
        <div><strong>7. No dangerous or sharp objects.</strong><br>
        Only clean, safe plastic bottles are accepted.</div>
    </div>

</main>
</div>

</body>
</html>
