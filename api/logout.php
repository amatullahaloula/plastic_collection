<?php
// api/logout.php
require_once __DIR__ . '/../includes/auth.php';
logout_user();
header('Location: /plastic_collection/views/login.php');
exit;
