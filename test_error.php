<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

echo "session_id: " . session_id() . "<br>";
echo "session_save_path: " . session_save_path() . "<br>";
echo "admin_logged: " . var_export($_SESSION['admin_logged'] ?? 'NO EXISTE', true) . "<br>";
echo "last_activity: " . var_export($_SESSION['last_activity'] ?? 'NO EXISTE', true) . "<br>";
echo "Tiempo actual: " . time() . "<br>";

if (isset($_SESSION['last_activity'])) {
    $diff = time() - $_SESSION['last_activity'];
    echo "Segundos desde última actividad: {$diff}<br>";
    echo "Timeout configurado: 300 segundos<br>";
    if ($diff > 300) {
        echo "<strong style='color:red'>SESIÓN EXPIRADA — esto causa la página en blanco</strong><br>";
    } else {
        echo "<strong style='color:green'>Sesión activa</strong><br>";
    }
}

// Verificar si el directorio de sesiones es escribible
$savePath = session_save_path() ?: sys_get_temp_dir();
echo "Directorio sesiones escribible: " . (is_writable($savePath) ? 'SÍ' : 'NO') . "<br>";
echo "Directorio: {$savePath}<br>";
