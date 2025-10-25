<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

// Destroy session
session_unset();
session_destroy();

// Clear session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirect to login
redirect(BASE_URL . '/auth/login.php');
