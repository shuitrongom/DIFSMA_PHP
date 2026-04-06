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

// Cargar helper de historial
require_once __DIR__ . '/historial_helper.php';

// Registrar actividad POST automaticamente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    try {
        require_once __DIR__ . '/../includes/db.php';
        $pdo_hist = get_db();
        $accion_hist = $_POST['action'] ?? 'accion';
        $seccion_hist = str_replace(['.php', '_', '-'], ['',' ',' '], $current_admin_file_guard);
        $desc_hist = '';
        if (!empty($_POST['titulo'])) $desc_hist = 'Titulo: ' . substr($_POST['titulo'], 0, 100);
        elseif (!empty($_POST['nombre'])) $desc_hist = 'Nombre: ' . substr($_POST['nombre'], 0, 100);
        elseif (!empty($_POST['anio'])) $desc_hist = 'Anio: ' . $_POST['anio'];
        registrar_historial($pdo_hist, $accion_hist, ucwords($seccion_hist), $desc_hist);
    } catch (Exception $e) {}
}

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
