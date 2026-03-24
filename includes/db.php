<?php
/**
 * includes/db.php — Conexión PDO singleton
 *
 * Uso:
 *   $pdo = get_db();
 *   $stmt = $pdo->prepare('SELECT * FROM admin WHERE username = ?');
 *   $stmt->execute([$username]);
 */

// Output buffering (reemplaza php_value en .htaccess para compatibilidad con hosting)
if (!ob_get_level()) {
    ob_start();
}

// Cargar configuración: busca config.php en el directorio padre (webroot)
// En producción, ajustar esta ruta para apuntar fuera del webroot.
$_config_path = __DIR__ . '/../config.php';
if (file_exists($_config_path)) {
    require_once $_config_path;
} else {
    // Fallback: leer solo de variables de entorno si config.php no existe
    if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'dif_cms');
    if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'dif_user');
    if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') ?: '');
    if (!defined('APP_DEBUG')) define('APP_DEBUG', false);
}

/**
 * Retorna la instancia PDO singleton configurada con:
 *  - charset utf8mb4
 *  - ERRMODE_EXCEPTION
 *  - FETCH_ASSOC por defecto
 *
 * @return PDO
 * @throws PDOException si la conexión falla
 */
function get_db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_NAME
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }

    return $pdo;
}
