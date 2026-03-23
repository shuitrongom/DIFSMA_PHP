<?php
/**
 * Authentication guard for admin pages.
 * Include at the top of every admin page EXCEPT login.php.
 *
 * Verifies that $_SESSION['admin_logged'] === true (strict comparison).
 * If not authenticated: destroys the session and redirects to login.php.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (($_SESSION['admin_logged'] ?? false) !== true) {
    session_destroy();
    header('Location: login.php');
    exit;
}
