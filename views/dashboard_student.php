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

    <link rel="stylesheet" href="../css/style.css">
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
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
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
            font-size: 22px;
            font-weight: 700;
            color: white;
        }

        header.top a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s;
        }

        header.top a:hover {
            opacity: 0.8;
        }

        main.container {
            background: rgba(255,255,255,0.98);
            padding: 35px;
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
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 18px;
            padding: 30px 22px;
            box-shadow: 0 4px 14px rgba(128, 0, 32, 0.12);
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            color: #800020;
            font-size: 18px;
            font-weight: 600;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .card-canva::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #800020 0%, #4a0012 100%);
            opacity: 0;
            transition: all 0.4s ease;
            z-index: 0;
        }

        .card-canva:hover::before {
            left: 0;
            opacity: 1;
        }

        .card-canva:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 24px rgba(128, 0, 32, 0.25);
            border-color: #800020;
            color: white;
        }

        .card-canva span {
            font-size: 42px;
            display: block;
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
        }

        .card-canva:hover span {
            transform: scale(1.1);
        }

        .card-canva > div {
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body>

<div class="page-background"></div>
<div class="transparent-wrapper">

<header class="top">
    <div>Ashesi Plastic Student page</div>
    <div>
        <a href="./help_center.php">Help Center</a> |
        <?= htmlspecialchars($displayName); ?> |
        <a href="../api/logout.php">Logout</a>
    </div>
</header>

<main class="container">

    <div class="grid-3">

        <!-- Submit Request -->
        <a class="card-canva" href="./student_request.php">
            <span>📝</span>
            <div>Submit Request</div>
        </a>

        <!-- Payment Info -->
        <a class="card-canva" href="./payment_info.php">
            <span>💳</span>
            <div>Payment Info</div>
        </a>

        <!-- History (NEW Separate Page) -->
        <a class="card-canva" href="./student_history.php">
            <span>📋</span>
            <div>My Collection History</div>
        </a>

        <!-- Help Center -->
        <a class="card-canva" href="./help_center.php">
            <span>❓</span>
            <div>Help Center</div>
        </a>

        <!-- Recycling Rules (NEW PAGE) -->
        <a class="card-canva" href="./recycling_rules.php">
            <span>♻️</span>
            <div>Recycling Rules</div>
        </a>

        <!-- My Profile (NEW PAGE) -->
        <a class="card-canva" href="./student_profile.php">
            <span>👤</span>
            <div>My Profile</div>
        </a>
    </div>

</main>
</div>

</body>
</html>