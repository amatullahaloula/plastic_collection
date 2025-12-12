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
            background: linear-gradient(135deg, #800020 0%, #4a0012 100%);
            min-height: 100vh;
        }

        .page-background {
            background: url('/plastic_collection/img/bottles_bg.png') center/cover no-repeat;
            position: fixed;
            inset: 0;
            opacity: 0.15;
            filter: blur(6px);
            z-index: -1;
        }

        .transparent-wrapper {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            padding: 20px;
            margin: 20px;
            box-shadow: 0 20px 60px rgba(128, 0, 32, 0.4);
        }

        header.top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: linear-gradient(135deg, #800020 0%, #4a0012 100%);
            border-radius: 12px;
            margin-bottom: 30px;
            color: white;
            box-shadow: 0 4px 12px rgba(128, 0, 32, 0.3);
        }

        header.top > div:first-child {
            font-size: 24px;
            font-weight: 700;
        }

        header.top a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.3s;
        }

        header.top a:hover {
            opacity: 0.8;
        }

        main.container {
            background: rgba(255,255,255,0.98);
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(128, 0, 32, 0.12);
            max-width: 900px;
            margin: auto;
        }

        h2.section-title {
            color: #800020;
            margin-bottom: 20px;
            font-size: 32px;
            border-bottom: 3px solid #800020;
            padding-bottom: 15px;
        }

        .rule {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 14px;
            box-shadow: 0 3px 10px rgba(128, 0, 32, 0.1);
            display: flex;
            gap: 15px;
            align-items: flex-start;
            border-left: 4px solid #800020;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .rule:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(128, 0, 32, 0.2);
        }

        .rule-icon {
            font-size: 32px;
            flex-shrink: 0;
        }

        .rule strong {
            color: #800020;
            font-size: 16px;
        }

        a.back {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #800020;
            color: white;
            text-decoration: none;
            font-weight: 600;
            border-radius: 8px;
            transition: background 0.3s;
        }

        a.back:hover {
            background: #4a0012;
        }

        .intro-text {
            margin-bottom: 25px;
            color: #555;
            font-size: 16px;
            line-height: 1.6;
        }
    </style>
</head>

<body>

<div class="page-background"></div>
<div class="transparent-wrapper">

<header class="top">
    <div>♻️ Recycling Rules</div>
    <div>
        <strong><?= htmlspecialchars($displayName); ?></strong> |
        <a href="./dashboard_student.php">Dashboard</a> |
        <a href="../api/logout.php">Logout</a>
    </div>
</header>

<main class="container">
    <a href="./dashboard_student.php" class="back">← Back to Dashboard</a>

    <h2 class="section-title">📋 Recycling Rules</h2>

    <p class="intro-text">
        Please follow these rules to ensure safe and clean recycling on campus. 
        These guidelines help us maintain an efficient and hygienic collection system for everyone.
    </p>

    <!-- RULE 1 -->
    <div class="rule">
        <div class="rule-icon">🍾</div>
        <div>
            <strong>1. Only plastic bottles are accepted.</strong><br>
            Other plastic materials (bags, cups, wrappers) are not allowed.
        </div>
    </div>

    <!-- RULE 2 -->
    <div class="rule">
        <div class="rule-icon">💧</div>
        <div>
            <strong>2. Bottles must be empty and rinsed.</strong><br>
            No liquid, no food waste inside.
        </div>
    </div>

    <!-- RULE 3 -->
    <div class="rule">
        <div class="rule-icon">👌</div>
        <div>
            <strong>3. Do not crush bottles too much.</strong><br>
            Slightly press them but keep the shape visible for counting.
        </div>
    </div>

    <!-- RULE 4 -->
    <div class="rule">
        <div class="rule-icon">📍</div>
        <div>
            <strong>4. Only Ashesi campus collection points are valid.</strong><br>
            Submit requests only from approved locations.
        </div>
    </div>

    <!-- RULE 5 -->
    <div class="rule">
        <div class="rule-icon">⏰</div>
        <div>
            <strong>5. Collection time must be respected.</strong><br>
            The cleaner team collects bottles during working hours (9am-4pm).
        </div>
    </div>

    <!-- RULE 6 -->
    <div class="rule">
        <div class="rule-icon">🛍️</div>
        <div>
            <strong>6. Keep bottles in a clean bag or container.</strong><br>
            Do not leave bottles on the ground. Use a clean bag or bin.
        </div>
    </div>

    <!-- RULE 7 -->
    <div class="rule">
        <div class="rule-icon">❌</div>
        <div>
            <strong>7. No dangerous or sharp objects.</strong><br>
            Only clean, safe plastic bottles are accepted. No broken glass or hazardous materials.
        </div>
    </div>

</main>
</div>

</body>
</html>