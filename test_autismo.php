<?php
// Script temporal para probar la conexión y datos de autismo
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/db.php';

echo "<h2>Test de Autismo Config</h2>";

try {
    $pdo = get_db();
    echo "<p>✓ Conexión a BD exitosa</p>";
    
    // Verificar si la tabla existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'autismo_config'");
    $table_exists = $stmt->fetch();
    
    if ($table_exists) {
        echo "<p>✓ Tabla autismo_config existe</p>";
        
        // Obtener datos
        $stmt = $pdo->query('SELECT * FROM autismo_config LIMIT 1');
        $config = $stmt->fetch();
        
        if ($config) {
            echo "<p>✓ Datos encontrados:</p>";
            echo "<pre>";
            print_r($config);
            echo "</pre>";
            
            echo "<p><strong>en_mantenimiento:</strong> " . ($config['en_mantenimiento'] ?? 'NULL') . "</p>";
            
            if (!empty($config['en_mantenimiento'])) {
                echo "<p style='color:red;'>⚠ MODO MANTENIMIENTO ACTIVO - Debería redirigir</p>";
            } else {
                echo "<p style='color:green;'>✓ Modo mantenimiento INACTIVO - Página normal</p>";
            }
        } else {
            echo "<p style='color:orange;'>⚠ No hay datos en autismo_config</p>";
        }
    } else {
        echo "<p style='color:red;'>✗ Tabla autismo_config NO existe</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='autismo.php'>Ir a autismo.php</a></p>";
echo "<p><a href='mantenimiento.php'>Ir a mantenimiento.php</a></p>";
?>
