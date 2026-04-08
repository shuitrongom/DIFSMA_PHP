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
    header('Location: login');
    exit;
}

// ── Timeout por inactividad (5 minutos) ─────────────────────────────────────
$SESSION_TIMEOUT = 300; // segundos

if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $SESSION_TIMEOUT) {
        // Sesión expirada por inactividad
        session_unset();
        session_destroy();
        header('Location: login?expired=1');
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
        $seccion_label = ucwords(trim($seccion_hist));

        // Obtener el valor más descriptivo disponible en el POST
        $valor = '';
        foreach (['titulo','nombre','username','link_titulo','departamento','concepto','descripcion','fecha_noticia','fecha_album','anio'] as $c) {
            if (!empty($_POST[$c])) { $valor = substr(trim($_POST[$c]), 0, 80); break; }
        }
        $id_ref = '';
        if (!empty($_POST['id']))        $id_ref = ' (ID ' . (int)$_POST['id'] . ')';
        elseif (!empty($_POST['user_id'])) $id_ref = ' (Usuario ID ' . (int)$_POST['user_id'] . ')';

        // Mapeo de acción → frase descriptiva completa
        $action_raw = $_POST['action'] ?? '';
        $frases = [
            'add'             => "Se agregó un nuevo elemento en {$seccion_label}" . ($valor ? ": \"{$valor}\"" : ''),
            'create'          => "Se creó un nuevo registro en {$seccion_label}" . ($valor ? ": \"{$valor}\"" : ''),
            'create_album'    => "Se creó el álbum \"{$valor}\" en Galería",
            'edit'            => "Se modificó el registro{$id_ref} en {$seccion_label}" . ($valor ? ": \"{$valor}\"" : ''),
            'edit_album'      => "Se editó el álbum \"{$valor}\" en Galería",
            'update'          => "Se actualizó la información en {$seccion_label}" . ($valor ? ": \"{$valor}\"" : ''),
            'delete'          => "Se eliminó el registro{$id_ref} en {$seccion_label}" . ($valor ? ": \"{$valor}\"" : ''),
            'delete_album'    => "Se eliminó el álbum{$id_ref} en Galería",
            'upload'          => "Se subió una imagen en {$seccion_label}",
            'upload_pdf'      => "Se subió un PDF en {$seccion_label}" . ($valor ? ": \"{$valor}\"" : ''),
            'delete_pdf'      => "Se eliminó un PDF en {$seccion_label}" . ($id_ref ? $id_ref : ''),
            'delete_image'    => "Se eliminó la imagen{$id_ref} en {$seccion_label}",
            'add_image'       => "Se agregó una imagen al álbum en Galería",
            'delete_boton'    => "Se eliminó un botón{$id_ref} en {$seccion_label}",
            'edit_boton'      => "Se editó el botón \"{$valor}\" en {$seccion_label}",
            'link_create'     => "Se creó el enlace \"{$valor}\" en Footer",
            'link_edit'       => "Se editó el enlace \"{$valor}\" en Footer",
            'link_delete'     => "Se eliminó un enlace{$id_ref} en Footer",
            'toggle'          => "Se cambió el estado del registro{$id_ref} en {$seccion_label}",
            'reorder'         => "Se reordenaron los elementos en {$seccion_label}",
            'reset_password'  => "Se restableció la contraseña del usuario{$id_ref}",
            'update_permisos' => "Se actualizaron los permisos del usuario{$id_ref}",
            'clear_all'       => "Se limpió todo el historial de actividad",
            'delete_log'      => "Se eliminó un registro del historial{$id_ref}",
        ];

        $desc_hist = $frases[$action_raw]
            ?? "Se realizó la acción \"{$action_raw}\" en {$seccion_label}" . ($valor ? ": \"{$valor}\"" : $id_ref);

        registrar_historial($pdo_hist, $accion_hist, $seccion_label, $desc_hist);
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
            header('Location: dashboard');
            exit;
        }
    } catch (PDOException $e) {
        // Si falla la consulta, denegar acceso por seguridad
        header('Location: dashboard');
        exit;
    }
}
