<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Simular sesión de admin para probar sin login
session_start();
$_SESSION['admin_logged']   = true;
$_SESSION['admin_rol']      = 'admin';
$_SESSION['admin_id']       = 1;
$_SESSION['admin_username'] = 'test';
$_SESSION['last_activity']  = time();

echo "Sesión OK<br>";

// Probar cargar cada dependencia del admin en orden
require_once __DIR__ . '/config.php';
echo "config OK<br>";

require_once __DIR__ . '/includes/db.php';
echo "db OK<br>";

$pdo = get_db();
echo "PDO OK<br>";

require_once __DIR__ . '/admin/historial_helper.php';
echo "historial_helper OK<br>";

require_once __DIR__ . '/admin/sidebar_sections.php';
echo "sidebar_sections OK<br>";

// Probar vendor
require_once __DIR__ . '/vendor/autoload.php';
echo "autoload OK<br>";

// Probar dompdf
$d = new \Dompdf\Dompdf();
echo "dompdf OK<br>";

// Probar PhpSpreadsheet
$s = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
echo "PhpSpreadsheet OK<br>";

echo "<strong style='color:green'>Todo OK</strong>";
