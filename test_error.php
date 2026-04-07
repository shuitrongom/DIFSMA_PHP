<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
$_SESSION['admin_logged']   = true;
$_SESSION['admin_rol']      = 'admin';
$_SESSION['admin_id']       = 1;
$_SESSION['admin_username'] = 'test';
$_SESSION['last_activity']  = time();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
$pdo = get_db();

// Verificar tablas que usan las secciones
$tablas = [
    'slider_principal',
    'admin_historial',
    'admin',
    'admin_permisos',
];

foreach ($tablas as $tabla) {
    try {
        $pdo->query("SELECT 1 FROM `{$tabla}` LIMIT 1");
        echo "Tabla <strong>{$tabla}</strong>: OK<br>";
    } catch (PDOException $e) {
        echo "Tabla <strong style='color:red'>{$tabla}</strong>: FALTA — " . htmlspecialchars($e->getMessage()) . "<br>";
    }
}

// Verificar columnas nuevas en admin_historial
try {
    $cols = $pdo->query("SHOW COLUMNS FROM admin_historial")->fetchAll(PDO::FETCH_COLUMN);
    echo "<br>Columnas de admin_historial: " . implode(', ', $cols) . "<br>";
    
    if (!in_array('dispositivo', $cols)) {
        echo "<strong style='color:red'>FALTA columna 'dispositivo' en admin_historial</strong><br>";
        echo "Ejecuta: ALTER TABLE admin_historial ADD COLUMN dispositivo VARCHAR(20) DEFAULT NULL AFTER ip, ADD COLUMN hostname VARCHAR(255) DEFAULT NULL AFTER dispositivo;<br>";
    } else {
        echo "Columna 'dispositivo': OK<br>";
    }
} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

echo "Test completo.";
