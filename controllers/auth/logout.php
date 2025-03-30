<?php
session_start();
require_once __DIR__ . '/../../models/User.php';

User::logout();
header('Location: login.php');
exit();
?>