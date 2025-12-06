<?php
// api/save_payment.php
session_start();
header("Content-Type: application/json");

// User must be logged in
if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "error" => "Not authenticated"]);
    exit;
}

require_once "../config/db.php"; // <-- your database connection

$user_id = $_SESSION['user']['id'];

// Sanitize main field
$method = trim($_POST['method'] ?? "");

if ($method !== "momo" && $method !== "bank") {
    echo json_encode(["success" => false, "error" => "Invalid payment method"]);
    exit;
}

// Build final data
$momo_number = null;
$network = null;
$bank_name = null;
$account_number = null;

if ($method === "momo") {
    $momo_number = trim($_POST['momo_number'] ?? "");
    $network     = trim($_POST['network'] ?? "");

    if ($momo_number === "" || $network === "") {
        echo json_encode(["success" => false, "error" => "Missing MoMo info"]);
        exit;
    }
}

if ($method === "bank") {
    $bank_name      = trim($_POST['bank_name'] ?? "");
    $account_number = trim($_POST['account_number'] ?? "");

    if ($bank_name === "" || $account_number === "") {
        echo json_encode(["success" => false, "error" => "Missing bank info"]);
        exit;
    }
}

// Check if user already has payment info
$q = $db->prepare("SELECT id FROM payment_info WHERE user_id = ?");
$q->execute([$user_id]);

if ($q->rowCount() > 0) {
    // Update existing
    $update = $db->prepare("
        UPDATE payment_info
        SET method=?, momo_number=?, network=?, bank_name=?, account_number=?
        WHERE user_id=?
    ");
    $ok = $update->execute([
        $method, $momo_number, $network, $bank_name, $account_number, $user_id
    ]);
} else {
    // Insert new
    $insert = $db->prepare("
        INSERT INTO payment_info (user_id, method, momo_number, network, bank_name, account_number)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $ok = $insert->execute([
        $user_id, $method, $momo_number, $network, $bank_name, $account_number
    ]);
}

if ($ok) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Database error"]);
}
