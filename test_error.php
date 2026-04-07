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

// Simular lo que hace slider_principal.php paso a paso
echo "1. Iniciando...<br>";

require_once __DIR__ . '/admin/upload_handler.php';
echo "2. upload_handler OK<br>";

// Consultar slides
$stmt = $pdo->query('SELECT * FROM slider_principal ORDER BY orden ASC');
$slides = $stmt->fetchAll();
echo "3. Slides encontrados: " . count($slides) . "<br>";

require_once __DIR__ . '/admin/sidebar_sections.php';
echo "4. sidebar_sections OK<br>";

echo "<strong style='color:green'>slider_principal cargará OK</strong><br>";
echo "El problema puede ser el upload_handler o una función que no existe.<br>";

// Verificar función handle_upload
if (function_exists('handle_upload')) {
    echo "5. handle_upload() existe: OK<br>";
} else {
    echo "5. <strong style='color:red'>handle_upload() NO EXISTE</strong><br>";
}

echo "Test completo.";
