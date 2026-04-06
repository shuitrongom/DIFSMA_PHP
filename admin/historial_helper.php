<?php
/**
 * @author  Sergio Huitron Gomez
 * @copyright 2025-2026 Sergio Huitron Gomez. Todos los derechos reservados.
 * @project DIF San Mateo Atenco - Sistema de Gestion de Contenido
 *
 * admin/historial_helper.php - Helper para registrar eventos en el historial
 */

/**
 * Detecta el tipo de dispositivo a partir del User-Agent.
 * Retorna: 'celular', 'tablet' o 'pc'
 */
function detectar_dispositivo(): string {
    $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
    if (preg_match('/tablet|ipad|kindle|playbook|silk|(android(?!.*mobile))/i', $ua)) {
        return 'tablet';
    }
    if (preg_match('/mobile|android|iphone|ipod|blackberry|opera mini|windows phone/i', $ua)) {
        return 'celular';
    }
    return 'pc';
}

/**
 * Extrae información legible del User-Agent: SO, modelo (si aplica) y navegador.
 * Ejemplo: "Windows 10 / Chrome 120" o "iPhone iOS 17 / Safari"
 */
function detectar_ua_info(): string {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

    // ── Sistema Operativo / Modelo ────────────────────────────────────────
    $os = 'Desconocido';

    // Android con modelo de dispositivo
    if (preg_match('/Android ([0-9.]+);?\s*([^;)]+)?/i', $ua, $m)) {
        $modelo = isset($m[2]) ? trim($m[2]) : '';
        $os = 'Android ' . $m[1] . ($modelo ? " ($modelo)" : '');
    }
    // iPhone
    elseif (preg_match('/iPhone.*OS ([0-9_]+)/i', $ua, $m)) {
        $os = 'iPhone iOS ' . str_replace('_', '.', $m[1]);
    }
    // iPad
    elseif (preg_match('/iPad.*OS ([0-9_]+)/i', $ua, $m)) {
        $os = 'iPad iOS ' . str_replace('_', '.', $m[1]);
    }
    // Windows
    elseif (preg_match('/Windows NT ([0-9.]+)/i', $ua, $m)) {
        $winMap = ['10.0' => '10/11', '6.3' => '8.1', '6.2' => '8', '6.1' => '7', '6.0' => 'Vista'];
        $ver = $winMap[$m[1]] ?? $m[1];
        $os = 'Windows ' . $ver;
    }
    // macOS
    elseif (preg_match('/Mac OS X ([0-9_]+)/i', $ua, $m)) {
        $os = 'macOS ' . str_replace('_', '.', $m[1]);
    }
    // Linux
    elseif (stripos($ua, 'linux') !== false) {
        $os = 'Linux';
    }

    // ── Navegador ─────────────────────────────────────────────────────────
    $browser = 'Desconocido';

    if (preg_match('/Edg\/([0-9.]+)/i', $ua, $m)) {
        $browser = 'Edge ' . explode('.', $m[1])[0];
    } elseif (preg_match('/OPR\/([0-9.]+)/i', $ua, $m)) {
        $browser = 'Opera ' . explode('.', $m[1])[0];
    } elseif (preg_match('/Chrome\/([0-9.]+)/i', $ua, $m)) {
        $browser = 'Chrome ' . explode('.', $m[1])[0];
    } elseif (preg_match('/Firefox\/([0-9.]+)/i', $ua, $m)) {
        $browser = 'Firefox ' . explode('.', $m[1])[0];
    } elseif (preg_match('/Safari\/([0-9.]+)/i', $ua, $m) && stripos($ua, 'chrome') === false) {
        $browser = 'Safari';
    }

    return $os . ' / ' . $browser;
}

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
        // Normalizar ::1 (IPv6 localhost) a 127.0.0.1
        if ($ip === '::1') $ip = '127.0.0.1';

        $dispositivo = detectar_dispositivo();
        // Guardar SO + navegador extraído del User-Agent
        $hostname = detectar_ua_info();

        $pdo->prepare(
            'INSERT INTO admin_historial (user_id, username, accion, seccion, descripcion, ip, dispositivo, hostname) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute([$userId, $username, $accion, $seccion, $descripcion, $ip, $dispositivo, $hostname]);
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
