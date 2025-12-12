<?php
// api/save_payment.php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

try {
    $user_id = $_SESSION['user']['id'];
    $method = trim($_POST['method'] ?? '');

    // Validate method
    if (!in_array($method, ['momo', 'bank'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid payment method']);
        exit();
    }

    // Initialize variables
    $momo_number = null;
    $network = null;
    $bank_name = null;
    $account_number = null;

    // Validate based on method
    if ($method === 'momo') {
        $momo_number = trim($_POST['momo_number'] ?? '');
        $network = trim($_POST['network'] ?? '');

        if (empty($momo_number) || empty($network)) {
            echo json_encode(['success' => false, 'error' => 'MoMo number and network are required']);
            exit();
        }

        // Basic validation for phone number (10 digits)
        if (!preg_match('/^0\d{9}$/', $momo_number)) {
            echo json_encode(['success' => false, 'error' => 'Invalid MoMo number format (should be 10 digits starting with 0)']);
            exit();
        }

    } elseif ($method === 'bank') {
        $bank_name = trim($_POST['bank_name'] ?? '');
        $account_number = trim($_POST['account_number'] ?? '');

        if (empty($bank_name) || empty($account_number)) {
            echo json_encode(['success' => false, 'error' => 'Bank name and account number are required']);
            exit();
        }
    }

    // Check if payment info already exists for this user
    $stmt = $pdo->prepare("SELECT id FROM payment_info WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Update existing payment info
        $stmt = $pdo->prepare("
            UPDATE payment_info 
            SET method = :method,
                momo_number = :momo_number,
                network = :network,
                bank_name = :bank_name,
                account_number = :account_number,
                updated_at = NOW()
            WHERE user_id = :user_id
        ");

        $stmt->execute([
            ':method' => $method,
            ':momo_number' => $momo_number,
            ':network' => $network,
            ':bank_name' => $bank_name,
            ':account_number' => $account_number,
            ':user_id' => $user_id
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Payment information updated successfully'
        ]);

    } else {
        // Insert new payment info
        $stmt = $pdo->prepare("
            INSERT INTO payment_info 
                (user_id, method, momo_number, network, bank_name, account_number, created_at) 
            VALUES 
                (:user_id, :method, :momo_number, :network, :bank_name, :account_number, NOW())
        ");

        $stmt->execute([
            ':user_id' => $user_id,
            ':method' => $method,
            ':momo_number' => $momo_number,
            ':network' => $network,
            ':bank_name' => $bank_name,
            ':account_number' => $account_number
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Payment information saved successfully'
        ]);
    }

} catch (PDOException $e) {
    error_log("Payment info save error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred. Please try again later.'
    ]);
} catch (Exception $e) {
    error_log("Payment info save error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred. Please try again.'
    ]);
}
?>