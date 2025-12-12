<?php
// includes/db.php
$envFile = __DIR__ . '../.env';

if (!file_exists($envFile)) {
    die(".env file not found!");
}

// Parse .env
$env = parse_ini_file($envFile);

// Set database variables
$servername = $env['host'];
$username = $env['user'];
$password = $env['pass'];
$database = $env['db'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}
