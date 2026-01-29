<?php
// inc/security.php
// Session and security helpers. Include this at top of public pages.

if (session_status() === PHP_SESSION_NONE) {
    // Better cookie settings for security
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'] ?? '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Basic security headers (adjust Content-Security-Policy per your needs)
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header("Referrer-Policy: no-referrer-when-downgrade");
// Keep CSP permissive enough for Bootstrap inline styles/scripts — tune later
header("Content-Security-Policy: default-src 'self' https: 'unsafe-inline' 'unsafe-eval'");

// CSRF helpers
function csrf_token(): string {
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}
function csrf_verify($token): bool {
    if (empty($_SESSION['_csrf']) || empty($token)) return false;
    return hash_equals($_SESSION['_csrf'], $token);
}

// simple escape helper
function e($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
