<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
echo "PHP version: " . PHP_VERSION . "<br>";
require_once 'config.php';
echo "Config OK<br>";
require_once 'includes/db.php';
echo "DB OK<br>";

// Probar cada archivo del admin
$files = [
    'admin/auth_guard.php',
    'admin/historial_helper.php',
    'admin/sidebar_sections.php',
];
foreach ($files as $f) {
    try {
        // Solo verificar sintaxis sin ejecutar
        $out = shell_exec('php -l ' . escapeshellarg(__DIR__ . '/' . $f) . ' 2>&1');
        echo htmlspecialchars($f) . ': ' . htmlspecialchars($out) . '<br>';
    } catch (Exception $e) {
        echo $f . ': ERROR - ' . $e->getMessage() . '<br>';
    }
}

// Probar vendor/autoload
if (file_exists('vendor/autoload.php')) {
    echo "vendor/autoload.php: EXISTS<br>";
} else {
    echo "vendor/autoload.php: MISSING - ejecuta composer install<br>";
}

// Verificar extension zip (necesaria para PhpSpreadsheet)
echo "zip extension: " . (extension_loaded('zip') ? 'OK' : 'MISSING') . '<br>';
echo "gd extension: " . (extension_loaded('gd') ? 'OK' : 'MISSING') . '<br>';
echo "mbstring extension: " . (extension_loaded('mbstring') ? 'OK' : 'MISSING') . '<br>';
