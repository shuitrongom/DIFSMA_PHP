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

// ── Timeout por inactividad (5 minutos) ─────────────────────────────────────
$SESSION_TIMEOUT = 300; // segundos

if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $SESSION_TIMEOUT) {
        // Sesión expirada por inactividad
        session_unset();
        session_destroy();
        header('Location: login.php?expired=1');
        exit;
    }
}
// Actualizar timestamp de última actividad
$_SESSION['last_activity'] = time();

// ── Verificar permisos de sección para usuarios no-admin ────────────────────
$current_admin_file_guard = basename($_SERVER['SCRIPT_FILENAME'] ?? '');
$is_admin_role = ($_SESSION['admin_rol'] ?? 'admin') === 'admin';

// Páginas que todos pueden ver (dashboard, logout, su perfil)
$public_pages = ['dashboard.php', 'logout.php'];

if (!$is_admin_role && !in_array($current_admin_file_guard, $public_pages)) {
    // Verificar si tiene permiso para esta sección
    try {
        require_once __DIR__ . '/../includes/db.php';
        $pdo_guard = get_db();
        $stmt_guard = $pdo_guard->prepare('SELECT id FROM admin_permisos WHERE user_id = ? AND seccion_file = ?');
        $stmt_guard->execute([$_SESSION['admin_id'] ?? 0, $current_admin_file_guard]);
        if (!$stmt_guard->fetch()) {
            $_SESSION['flash_message'] = 'No tienes permiso para acceder a esta seccion.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: dashboard.php');
            exit;
        }
    } catch (PDOException $e) {
        // Si falla la consulta, denegar acceso por seguridad
        header('Location: dashboard.php');
        exit;
    }
}
