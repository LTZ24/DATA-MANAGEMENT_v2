<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

session_unset();
session_destroy();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

redirect(BASE_URL . '/auth/login.php');
