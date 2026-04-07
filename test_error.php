<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
echo "PHP version: " . PHP_VERSION . "<br>";

// Verificar sintaxis de TODOS los archivos admin
$adminFiles = glob(__DIR__ . '/admin/*.php');
sort($adminFiles);
$errors = [];
foreach ($adminFiles as $f) {
    $out = shell_exec('php -l ' . escapeshellarg($f) . ' 2>&1');
    $name = basename($f);
    if (strpos($out, 'No syntax errors') === false) {
        echo "<strong style='color:red'>ERROR: $name</strong>: " . htmlspecialchars($out) . "<br>";
        $errors[] = $name;
    }
}
if (empty($errors)) {
    echo "<strong style='color:green'>Todos los archivos admin OK</strong><br>";
}

// Verificar archivos raíz
$rootFiles = glob(__DIR__ . '/*.php');
foreach ($rootFiles as $f) {
    $out = shell_exec('php -l ' . escapeshellarg($f) . ' 2>&1');
    $name = basename($f);
    if (strpos($out, 'No syntax errors') === false && $name !== 'test_error.php') {
        echo "<strong style='color:red'>ROOT ERROR: $name</strong>: " . htmlspecialchars($out) . "<br>";
    }
}

// Verificar includes
$incFiles = glob(__DIR__ . '/includes/*.php');
foreach ($incFiles as $f) {
    $out = shell_exec('php -l ' . escapeshellarg($f) . ' 2>&1');
    $name = basename($f);
    if (strpos($out, 'No syntax errors') === false) {
        echo "<strong style='color:red'>INCLUDES ERROR: $name</strong>: " . htmlspecialchars($out) . "<br>";
    }
}
echo "Verificación completa.";
