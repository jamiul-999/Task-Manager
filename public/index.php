<?php
// This will redirect to the login page

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Start session
session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Task.php';

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/tasks.php';
// Log in check


if (User::isLoggedIn()) {
    // Redirect to dashboard if already logged in
    header("Location: ../controllers/tasks/dashboard.php");
}
else {
    // Redirect to login page if not logged in
    header("Location: ../controllers/auth/login.php");
}

exit();
?>