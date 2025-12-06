<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_once __DIR__ . '/../includes/db.php';

$user = $_SESSION['user'];
$displayName = $user['nickname'] ?: ($user['first_name'] ?? 'Student');
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Student Dashboard - Ashesi Plastic</title>

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
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .card-canva {
            background: white;
            border-radius: 18px;
            padding: 22px;
            box-shadow: 0 3px 14px rgba(0,0,0,0.07);
            transition: 0.2s;
            text-align: center;
            text-decoration: none;
            color: black;
            font-size: 18px;
            font-weight: 600;
        }

        .card-canva:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.15);
        }

        .card-canva span {
            font-size: 36px;
            display: block;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

<div class="page-background"></div>
<div class="transparent-wrapper">

<header class="top">
    <div>Ashesi Plastic Student page</div>
    <div>
        <a href="/plastic_collection/views/help_center.php" style="color: #f6f6f6; text-decoration: none;">Help Center</a> |
        <?= htmlspecialchars($displayName); ?> |
        <a href="/plastic_collection/api/logout.php" style="color: #f6f6f6; text-decoration: none;">Logout</a>
    </div>
</header>

<main class="container">

    <div class="grid-3">

        <!-- Submit Request -->
        <a class="card-canva" href="/plastic_collection/views/student_request.php">
            <span></span>
            Submit Request
        </a>

        <!-- Payment Info -->
        <a class="card-canva" href="/plastic_collection/views/payment_info.php">
            <span></span>
            Payment Info
        </a>

        <!-- History (NEW Separate Page) -->
        <a class="card-canva" href="/plastic_collection/views/student_history.php">
            <span></span>
            My Collection History
        </a>

        <!-- Help Center -->
        <a class="card-canva" href="/plastic_collection/views/help_center.php">
            <span>❓</span>
            Help Center
        </a>

        <!-- Recycling Rules (NEW PAGE) -->
        <a class="card-canva" href="/plastic_collection/views/recycling_rules.php">
            <span></span>
            Recycling Rules
        </a>

        <!-- My Profile (NEW PAGE) -->
        <a class="card-canva" href="/plastic_collection/views/student_profile.php">
            <span></span>
            My Profile
        </a>
    </div>

</main>
</div>

</body>
</html>
