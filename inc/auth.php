<?php
// inc/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

function current_user(PDO $pdo = null) {
    if (!is_logged_in()) return null;
    if (isset($_SESSION['user'])) return $_SESSION['user']; // cached
    // optionally fetch from DB if $pdo provided
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: /public/user_login.php');
        exit;
    }
}

function require_role(string $role) {
    if (!is_logged_in() || ($_SESSION['user']['role'] ?? '') !== $role) {
        header('HTTP/1.1 403 Forbidden');
        echo 'Access denied';
        exit;
    }
}
