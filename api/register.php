<?php
// api/register.php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$first = trim($_POST['first_name'] ?? '');
$last = trim($_POST['last_name'] ?? '');
$nickname = trim($_POST['nickname'] ?? '');
$email = trim($_POST['email'] ?? '');
$role = trim($_POST['role'] ?? 'student');
$degree = trim($_POST['degree'] ?? null);
$password = $_POST['password'] ?? '';
$phone = trim($_POST['phone'] ?? '');

// validation
if ($first === '' || $last === '' || $email === '' || $password === '' || $role === '') {
    echo json_encode(['success' => false, 'error' => 'Please fill required fields.']);
    exit;
}

// ashesi email check
if (!preg_match("/@ashesi\.edu\.gh$/i", $email)) {
    echo json_encode(['success' => false, 'error' => 'Use a valid Ashesi email (example@ashesi.edu.gh).']);
    exit;
}

// if role is student, degree is required
if ($role === 'student' && (empty($degree))) {
    echo json_encode(['success' => false, 'error' => 'Select your class / year.']);
    exit;
}

// normalize role
if (!in_array($role, ['student','cleaner','admin'])) $role = 'student';

try {
    // check existing email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Email already registered.']);
        exit;
    }

    $passHash = password_hash($password, PASSWORD_DEFAULT);

    // DB columns: first_name, last_name, nickname, email, degree, role, password, phone
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, nickname, email, degree, role, password, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$first, $last, $nickname ?: null, $email, ($role === 'student' ? $degree : null), $role, $passHash, $phone ?: null]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // don't leak internal error details in production
    echo json_encode(['success' => false, 'error' => 'Server error.']);
}
