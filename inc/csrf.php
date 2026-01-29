<?php
// inc/csrf.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate or return CSRF token
 */
function csrf_token()
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

/**
 * Validate CSRF token
 */
function verify_csrf_token($token)
{
    return isset($_SESSION['_csrf_token'])
        && hash_equals($_SESSION['_csrf_token'], $token);
}

/**
 * Regenerate token (optional)
 */
function regenerate_csrf_token()
{
    unset($_SESSION['_csrf_token']);
}
