<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Method not allowed.");
}

// Logged in?
$user_id = $_SESSION['user']['id'] ?? null;
if (!$user_id) {
    die("You must be logged in to submit a request.");
}

// Sanitize inputs
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation
if ($email === '' || $subject === '' || $message === '') {
    die("Please fill all fields.");
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO support_requests 
            (user_id, email, subject, message, status, created_at) 
        VALUES 
            (:uid, :email, :subject, :message, 'pending', NOW())
    ");

    $stmt->execute([
        ':uid'     => $user_id,
        ':email'   => $email,
        ':subject' => $subject,
        ':message' => $message
    ]);

    header("Location: ../views/help_center.php?sent=1");
    exit;

} catch (Exception $e) {
    error_log("Support insert error: " . $e->getMessage());
    die("Something went wrong.");
}
?>
