<?php
/**
 * PHPUnit bootstrap file.
 * Sets up the test environment before any test runs.
 */

// Start a session for tests that need $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoload via Composer if available
$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}
