<?php
// This will redirect to the login page
if (session_status() === PHP_SESSION_ACTIVE) {
    session_start();
}

header("Location: controllers/auth/login.php");
exit();