<?php
/**
 * @author  Sergio Huitron Gomez
 * @copyright 2025-2026 Sergio Huitron Gomez. Todos los derechos reservados.
 * @project DIF San Mateo Atenco - Sistema de Gestion de Contenido
 *
 * admin/historial_helper.php - Helper para registrar eventos en el historial
 */

/**
 * Registra un evento en el historial de actividad.
 *
 * @param PDO    $pdo         Conexion a la base de datos
 * @param string $accion      Tipo de accion: 'crear', 'editar', 'eliminar', 'subir', 'login', etc.
 * @param string $seccion     Nombre de la seccion: 'Noticias', 'Galeria', 'Slider Principal', etc.
 * @param string $descripcion Descripcion detallada del evento
 */
function registrar_historial(PDO $pdo, string $accion, string $seccion, string $descripcion = ''): void {
    try {
        $userId   = $_SESSION['admin_id'] ?? null;
        $username = $_SESSION['admin_username'] ?? ($_SESSION['admin_logged'] ? 'admin' : 'desconocido');
        $ip       = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        $pdo->prepare(
            'INSERT INTO admin_historial (user_id, username, accion, seccion, descripcion, ip) VALUES (?, ?, ?, ?, ?, ?)'
        )->execute([$userId, $username, $accion, $seccion, $descripcion, $ip]);
    } catch (PDOException $e) {
        // Silencioso — no interrumpir el flujo si falla el historial
        if (defined('APP_DEBUG') && APP_DEBUG) {
            error_log('historial_helper: ' . $e->getMessage());
        }
    }
}

/**
 * Etiquetas de color por tipo de accion
 */
function historial_badge(string $accion): string {
    $map = [
        'crear'    => 'bg-success',
        'editar'   => 'bg-warning text-dark',
        'eliminar' => 'bg-danger',
        'subir'    => 'bg-primary',
        'login'    => 'bg-info text-dark',
        'logout'   => 'bg-secondary',
        'reorden'  => 'bg-light text-dark border',
    ];
    $class = $map[strtolower($accion)] ?? 'bg-secondary';
    return '<span class="badge ' . $class . '">' . htmlspecialchars(ucfirst($accion)) . '</span>';
}
