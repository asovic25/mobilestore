<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy session
session_unset();
session_destroy();

// Redirect to login
header("Location: admin_login.php");
exit;
