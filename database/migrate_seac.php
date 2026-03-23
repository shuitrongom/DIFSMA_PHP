<?php
/**
 * migrate_seac.php — Parsea SEAC.html y genera SQL de migración
 * 
 * Uso: php database/migrate_seac.php > database/seac_migration.sql
 */

$html = file_get_contents(__DIR__ . '/../trasparencia/SEAC.html');

// Encontrar todos los bloques de año
preg_match_all('/&nbsp;&nbsp;(\d{4})</', $html, $yearMatches);
$years = array_unique($yearMatches[1]);
rsort($years); // 2025, 2024, ... 2018

// Dividir el HTML por bloques de año
$blocks = [];
$yearPositions = [];
foreach ($years as $year) {
    $pos = strpos($html, '&nbsp;&nbsp;' . $year . '</p>');
    if ($pos !== false) {
        $yearPositions[$year] = $pos;
    }
}

// Ordenar por posición
asort($yearPositions);
$sortedYears = array_keys($yearPositions);

// Extraer el HTML de cada bloque
for ($i = 0; $i < count($sortedYears); $i++) {
    $year = $sortedYears[$i];
    $start = $yearPositions[$year];
    $end = ($i + 1 < count($sortedYears)) ? $yearPositions[$sortedYears[$i + 1]] : strlen($html);
    $blocks[$year] = substr($html, $start, $end - $start);
}

echo "-- =============================================================================\n";
echo "-- SEAC Migration — Generado automáticamente desde SEAC.html\n";
echo "-- Ejecutar en phpMyAdmin después de schema.sql\n";
echo "-- =============================================================================\n\n";
echo "SET NAMES utf8mb4;\n\n";

// Para cada año, extraer conceptos y PDFs
$bloqueOrden = 1;
foreach ($sortedYears as $year) {
    $blockHtml = $blocks[$year];
    
    echo "-- ── Bloque $year ──────────────────────────────────────────────────────────\n";
    echo "INSERT INTO `seac_bloques` (`anio`, `orden`) VALUES ($year, $bloqueOrden)\n";
    echo "ON DUPLICATE KEY UPDATE `orden` = VALUES(`orden`);\n";
    echo "SET @bloque_{$year}_id = (SELECT `id` FROM `seac_bloques` WHERE `anio` = $year);\n\n";
    
    // Encontrar todas las filas <tr> con conceptos
    // Patrón: <td>NUMERO.- NOMBRE</td> seguido de <td> con posibles PDFs
    preg_match_all('/<tr>\s*(.*?)\s*<\/tr>/s', $blockHtml, $rowMatches);
    
    $conceptoOrden = 1;
    foreach ($rowMatches[1] as $rowHtml) {
        // Extraer las celdas <td>
        preg_match_all('/<td[^>]*>(.*?)<\/td>/s', $rowHtml, $cellMatches);
        if (empty($cellMatches[1])) continue;
        
        $cells = $cellMatches[1];
        $firstCell = trim(strip_tags(html_entity_decode($cells[0], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        
        // Verificar si es un concepto (empieza con número)
        if (!preg_match('/^(\d+)\s*[\.\-\)]+\s*(.+)$/u', $firstCell, $conceptMatch)) continue;
        
        $numero = (int)$conceptMatch[1];
        $nombre = trim($conceptMatch[2]);
        $nombre = str_replace("'", "\\'", $nombre);
        
        echo "INSERT INTO `seac_conceptos` (`bloque_id`, `numero`, `nombre`, `orden`) VALUES\n";
        echo "(@bloque_{$year}_id, $numero, '$nombre', $conceptoOrden)\n";
        echo "ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);\n";
        echo "SET @concepto_{$year}_{$numero}_id = LAST_INSERT_ID();\n";
        // Also get the ID if it already existed
        echo "SET @concepto_{$year}_{$numero}_id = (SELECT `id` FROM `seac_conceptos` WHERE `bloque_id` = @bloque_{$year}_id AND `numero` = $numero LIMIT 1);\n\n";
        
        // Extraer PDFs de las celdas de trimestre (celdas 1-4)
        for ($t = 1; $t <= 4; $t++) {
            if (!isset($cells[$t])) continue;
            $cellContent = $cells[$t];
            
            // Buscar data-pdf
            if (preg_match('/data-pdf="\.\.\/([^"#]+)/', $cellContent, $pdfMatch)) {
                $pdfPath = $pdfMatch[1];
                $pdfPath = str_replace("'", "\\'", $pdfPath);
                
                echo "INSERT INTO `seac_pdfs` (`bloque_id`, `concepto_id`, `trimestre`, `pdf_path`) VALUES\n";
                echo "(@bloque_{$year}_id, @concepto_{$year}_{$numero}_id, $t, '$pdfPath')\n";
                echo "ON DUPLICATE KEY UPDATE `pdf_path` = VALUES(`pdf_path`);\n";
            }
        }
        
        $conceptoOrden++;
    }
    
    echo "\n";
    $bloqueOrden++;
}

echo "-- ═══════════════════════════════════════════════════════════════════════════\n";
echo "-- Migración completada\n";
echo "-- ═══════════════════════════════════════════════════════════════════════════\n";
