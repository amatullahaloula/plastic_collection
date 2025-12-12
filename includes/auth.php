<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

function require_login() {
    if (!isset($_SESSION['user'])) {
        attempt_remember_login();
    }
    if (!isset($_SESSION['user'])) {
        header("Location: ../views/login.php");
        exit;
    }
}

function require_role($role) {
    require_login();
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== $role) {
        header("Location: /plastic_collection/views/login.php");
        exit;
    }
}

function attempt_remember_login() {
    global $pdo;
    if (isset($_COOKIE['rememberme']) && !isset($_SESSION['user'])) {
        $val = $_COOKIE['rememberme']; // stored as id:token
        $parts = explode(':', $val);
        if (count($parts) === 2) {
            [$id, $token] = $parts;
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND remember_token = ? AND remember_expires > NOW()");
            $stmt->execute([(int)$id, $token]);
            $user = $stmt->fetch();
            if ($user) {
                // set session user (strip password)
                unset($user['password']);
                $_SESSION['user'] = $user;
            } else {
                // invalid cookie - delete
                setcookie('rememberme', '', time() - 3600, "/");
            }
        }
    }
}

function logout_user() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    session_unset();
    session_destroy();
    setcookie('rememberme', '', time() - 3600, "/");
}
