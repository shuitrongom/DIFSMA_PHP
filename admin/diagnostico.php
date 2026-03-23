<?php
/**
 * diagnostico.php — Archivo temporal para detectar errores en el hosting
 * ELIMINAR DESPUÉS DE USAR
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h3>Diagnóstico del hosting</h3>";
echo "<p>PHP version: " . phpversion() . "</p>";

// 1. Verificar config.php
echo "<h4>1. Config</h4>";
$configPath = __DIR__ . '/../config.php';
if (file_exists($configPath)) {
    echo "<p style='color:green'>✅ config.php encontrado</p>";
    require_once $configPath;
    echo "<p>DB_HOST: " . DB_HOST . "</p>";
    echo "<p>DB_NAME: " . DB_NAME . "</p>";
    echo "<p>DB_USER: " . DB_USER . "</p>";
    echo "<p>APP_DEBUG: " . (APP_DEBUG ? 'true' : 'false') . "</p>";
} else {
    echo "<p style='color:red'>❌ config.php NO encontrado en: $configPath</p>";
}

// 2. Verificar conexión BD
echo "<h4>2. Conexión a BD</h4>";
try {
    require_once __DIR__ . '/../includes/db.php';
    $pdo = get_db();
    echo "<p style='color:green'>✅ Conexión exitosa</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error de conexión: " . htmlspecialchars($e->getMessage()) . "</p>";
    die();
}

// 3. Verificar tablas
echo "<h4>3. Tablas</h4>";
$tablas = ['admin','slider_principal','slider_comunica','noticias_imagenes','presidencia',
           'direcciones','organigrama','tramites','galeria_albumes','galeria_imagenes',
           'seac_bloques','seac_conceptos','seac_pdfs',
           'cp_bloques','cp_titulos','cp_conceptos',
           'pa_bloques','pa_conceptos','pa_pdfs',
           'pae_titulos','pae_pdfs','mi_pdfs',
           'conac_bloques','conac_conceptos','conac_pdfs',
           'fin_bloques','fin_conceptos','avisos_privacidad','avisos_privacidad_config',
           'transparencia_items','login_attempts','footer_config','institucion_banner',
           'programas','programas_secciones'];
foreach ($tablas as $t) {
    try {
        $pdo->query("SELECT 1 FROM $t LIMIT 1");
        echo "<p style='color:green'>✅ $t</p>";
    } catch (Exception $e) {
        echo "<p style='color:red'>❌ $t — " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// 4. Verificar sesiones
echo "<h4>4. Sesiones</h4>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['test'] = 'ok';
echo "<p style='color:green'>✅ Sesiones funcionan</p>";

// 5. Verificar carpeta uploads
echo "<h4>5. Carpeta uploads</h4>";
$uploadsPath = defined('UPLOADS_PATH') ? UPLOADS_PATH : __DIR__ . '/../uploads';
if (is_dir($uploadsPath)) {
    echo "<p style='color:green'>✅ Carpeta uploads existe</p>";
    echo "<p>Escribible: " . (is_writable($uploadsPath) ? '✅ Sí' : '❌ No') . "</p>";
} else {
    echo "<p style='color:red'>❌ Carpeta uploads NO existe: $uploadsPath</p>";
}

echo "<hr><p><strong>Elimina este archivo después de usarlo.</strong></p>";
