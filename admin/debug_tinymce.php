<?php
// Archivo temporal de diagnóstico — ELIMINAR DESPUÉS DE USAR
require_once __DIR__ . '/auth_guard.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Diagnóstico TinyMCE</h2>";
echo "<p>PHP: " . phpversion() . "</p>";
echo "<p>Archivo local tinymce: " . (file_exists(__DIR__ . '/../lib/tinymce/tinymce.min.js') ? '<b style="color:green">EXISTE</b>' : '<b style="color:red">NO EXISTE</b>') . "</p>";

// Test incluir admin/presidencia sin output
echo "<p>Probando carga de presidencia...</p>";
ob_start();
try {
    // Solo verificar que los requires funcionan
    require_once __DIR__ . '/csrf.php';
    require_once __DIR__ . '/upload_handler.php';
    require_once __DIR__ . '/../includes/db.php';
    $pdo = get_db();
    $stmt = $pdo->query('SELECT id FROM presidencia LIMIT 1');
    echo "<p style='color:green'>DB OK - presidencia tabla accesible</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>ERROR: " . htmlspecialchars($e->getMessage()) . "</p>";
}
ob_end_clean();

echo "<p><a href='presidencia'>Ir a presidencia</a></p>";
echo "<p><b>Elimina este archivo después de revisar.</b></p>";
?>
