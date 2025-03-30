<?php
// This will redirect to the login page

// Start session
session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Task.php';

// require_once __DIR__ . '/../controllers/auth/login.php';
// require_once __DIR__ . '/../controllers/tasks/dashboard.php';
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