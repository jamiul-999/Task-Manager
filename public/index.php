<?php
// This will redirect to the login page

// Start session
session_start();

// Log in check
require_once __DIR__ . '/../models/User.php';

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