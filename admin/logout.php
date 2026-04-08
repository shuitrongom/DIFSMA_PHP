<?php
/**
 * admin/logout.php — Cierre de sesión del panel de administración
 *
 * Requirements: 1.5
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpiar todas las variables de sesión
$_SESSION = [];

// Eliminar la cookie de sesión para mayor seguridad
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destruir la sesión en el servidor
session_destroy();

// Redirigir al login
header('Location: login');
exit;
