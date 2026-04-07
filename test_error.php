<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
$_SESSION['admin_logged']   = true;
$_SESSION['admin_rol']      = 'admin';
$_SESSION['admin_id']       = 1;
$_SESSION['admin_username'] = 'test';
$_SESSION['last_activity']  = time();

// Capturar toda la salida de slider_principal.php
ob_start();
try {
    include __DIR__ . '/admin/slider_principal.php';
    $output = ob_get_clean();
    echo "Longitud de salida: " . strlen($output) . " bytes<br>";
    if (strlen($output) < 100) {
        echo "Salida (muy corta): " . htmlspecialchars($output) . "<br>";
    } else {
        echo "Primeros 500 chars: " . htmlspecialchars(substr($output, 0, 500)) . "<br>";
        echo "<strong style='color:green'>slider_principal.php cargó correctamente</strong>";
    }
} catch (Throwable $e) {
    ob_end_clean();
    echo "<strong style='color:red'>ERROR: " . htmlspecialchars($e->getMessage()) . "</strong><br>";
    echo "Archivo: " . htmlspecialchars($e->getFile()) . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
