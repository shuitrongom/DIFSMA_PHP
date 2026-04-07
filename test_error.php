<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Mostrar fecha de modificación de archivos clave
$files = [
    'admin/slider_principal.php',
    'admin/noticias.php', 
    'admin/galeria.php',
    'css/admin.css',
    'js/admin-tooltips.js',
];

foreach ($files as $f) {
    $path = __DIR__ . '/' . $f;
    if (file_exists($path)) {
        $mtime = date('Y-m-d H:i:s', filemtime($path));
        $size  = filesize($path);
        // Verificar BOM
        $fh    = fopen($path, 'rb');
        $first3 = fread($fh, 3);
        fclose($fh);
        $hasBom = ($first3 === "\xEF\xBB\xBF");
        echo "$f — Modificado: $mtime — Tamaño: {$size}b — BOM: " . ($hasBom ? '<strong style="color:red">SÍ</strong>' : 'No') . "<br>";
    } else {
        echo "<strong style='color:red'>NO EXISTE: $f</strong><br>";
    }
}

// Verificar si admin-tooltips.js existe
echo "<br>js/admin-tooltips.js: " . (file_exists(__DIR__ . '/js/admin-tooltips.js') ? 'EXISTS' : '<strong style="color:red">MISSING</strong>') . "<br>";
