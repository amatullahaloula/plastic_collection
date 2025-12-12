<?php
// api/submit_support.php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../includes/db.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

try {
    // Get form data
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? 'General Inquiry');
    $message = trim($_POST['message'] ?? '');

    // Get user_id if logged in (optional - allow guest submissions)
    $user_id = $_SESSION['user']['id'] ?? null;

    // Validate inputs
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Valid email is required']);
        exit();
    }

    if (empty($message)) {
        echo json_encode(['success' => false, 'error' => 'Message is required']);
        exit();
    }

    // Insert into support_requests table
    $stmt = $pdo->prepare("
        INSERT INTO support_requests (user_id, email, subject, message, status) 
        VALUES (:uid, :email, :subject, :message, 'pending')
    ");

    $stmt->execute([
        ':uid' => $user_id,
        ':email' => $email,
        ':subject' => $subject,
        ':message' => $message
    ]);

    echo json_encode([
        'success' => true, 
        'message' => 'Support request submitted successfully'
    ]);

} catch (PDOException $e) {
    error_log("Support request error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Database error occurred. Please try again later.'
    ]);
} catch (Exception $e) {
    error_log("Support request error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'An error occurred. Please try again.'
    ]);
}
?>