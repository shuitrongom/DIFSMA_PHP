<?php
/**
 * Authentication guard for admin pages.
 * Include at the top of every admin page EXCEPT login.php.
 *
 * ob_start() previene errores "headers already sent" causados por BOM
 * o espacios antes de <?php en archivos que incluyen este guard.
 */

ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (($_SESSION['admin_logged'] ?? false) !== true) {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
    header('Location: login.php');
    exit;
}
