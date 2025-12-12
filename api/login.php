<?php
// api/login.php
require_once __DIR__ . '/../includes/db.php';
session_start();
header('Content-Type: application/json');

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']) && $_POST['remember'] === '1';

if ($email === '' || $password === '') {
    echo json_encode(['success' => false, 'error' => 'Email and password required.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid credentials.']);
        exit;
    }

    // remove password before storing session
    unset($user['password']);
    $_SESSION['user'] = $user;

    if ($remember) {
        // generate token and store
        $token = bin2hex(random_bytes(32));
        $expires = (new DateTime('+30 days'))->format('Y-m-d H:i:s');
        $stmt = $pdo->prepare("UPDATE users SET remember_token = ?, remember_expires = ? WHERE id = ?");
        $stmt->execute([$token, $expires, $user['id']]);
        // cookie value id:token
        setcookie('rememberme', $user['id'] . ':' . $token, time() + 60*60*24*30, "/");
    }

    // response with redirect target
    $target = '../views/dashboard_student.php';
    if ($user['role'] === 'cleaner') $target = '../views/dashboard_cleaner.php';
    if ($user['role'] === 'admin') $target = '../views/dashboard_admin.php';

    echo json_encode(['success' => true, 'redirect' => $target]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error.']);
}
