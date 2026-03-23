<?php
/**
 * generate_seac_sql.php — Lee SEAC.html y genera SQL de migración en UTF-8
 *
 * Uso: php database/generate_seac_sql.php
 * Genera: database/seac_migration.sql (UTF-8, listo para DBeaver/phpMyAdmin)
 *
 * NO usa SET @variable — compatible con DBeaver y cualquier cliente SQL.
 */

$htmlFile = __DIR__ . '/../trasparencia/SEAC.html';
if (!file_exists($htmlFile)) {
    die("ERROR: No se encontró $htmlFile\n");
}

$lines = file($htmlFile, FILE_IGNORE_NEW_LINES);
$totalLines = count($lines);

$yearRanges = [
    ['year' => 2025, 'start' => 107, 'end' => 518],
    ['year' => 2024, 'start' => 519, 'end' => 2281],
    ['year' => 2023, 'start' => 2282, 'end' => 3619],
    ['year' => 2022, 'start' => 3620, 'end' => 4878],
    ['year' => 2021, 'start' => 4879, 'end' => 5664],
    ['year' => 2020, 'start' => 5665, 'end' => 6071],
    ['year' => 2019, 'start' => 6072, 'end' => 6604],
    ['year' => 2018, 'start' => 6605, 'end' => 7000],
];

$output = [];
$output[] = "-- =============================================================================";
$output[] = "-- SEAC Migration — Generado desde SEAC.html";
$output[] = "-- Compatible con DBeaver, phpMyAdmin, mysql CLI";
$output[] = "-- Ejecutar DESPUES de schema.sql";
$output[] = "-- =============================================================================";
$output[] = "";

$bloqueOrden = 1;

foreach ($yearRanges as $range) {
    $year = $range['year'];
    $startLine = $range['start'] - 1;
    $endLine = min($range['end'] - 1, $totalLines - 1);

    $blockLines = array_slice($lines, $startLine, $endLine - $startLine);
    $blockHtml = implode("\n", $blockLines);

    $output[] = "-- ── Bloque $year ──────────────────────────────────────────────────────────";
    $output[] = "INSERT INTO `seac_bloques` (`anio`, `orden`) VALUES ($year, $bloqueOrden) ON DUPLICATE KEY UPDATE `orden` = VALUES(`orden`);";
    $output[] = "";

    // Subquery para obtener bloque_id inline
    $bloqueSubq = "(SELECT `id` FROM `seac_bloques` WHERE `anio` = $year)";

    preg_match_all('/<tr>(.*?)<\/tr>/s', $blockHtml, $rowMatches);

    $conceptoOrden = 1;

    foreach ($rowMatches[1] as $rowHtml) {
        preg_match_all('/<(?:td|th)[^>]*>(.*?)<\/(?:td|th)>/s', $rowHtml, $cellMatches);
        if (empty($cellMatches[1])) continue;

        $cells = $cellMatches[1];

        $firstCell = trim(strip_tags(html_entity_decode($cells[0], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $firstCell = preg_replace('/\s+/', ' ', $firstCell);

        if (empty($firstCell) || $firstCell === '&nbsp;' || $firstCell === 'Concepto') continue;

        $numero = $conceptoOrden;
        $nombre = $firstCell;
        if (preg_match('/^(\d+)\s*[\.\-\)\:]+\s*(.+)$/u', $firstCell, $m)) {
            $numero = (int)$m[1];
            $nombre = trim($m[2]);
        }

        $nombreSql = str_replace("'", "''", $nombre);

        $output[] = "INSERT INTO `seac_conceptos` (`bloque_id`, `numero`, `nombre`, `orden`) VALUES ($bloqueSubq, $numero, '$nombreSql', $conceptoOrden) ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);";

        // Subquery para concepto_id inline
        $conceptoSubq = "(SELECT `id` FROM `seac_conceptos` WHERE `bloque_id` = $bloqueSubq AND `numero` = $numero LIMIT 1)";

        // Buscar PDFs
        $pdfPaths = [];

        if ($year == 2024) {
            $trimMap = [1 => 1, 3 => 2, 5 => 3, 7 => 4];
            foreach ($trimMap as $cellIdx => $trim) {
                if (isset($cells[$cellIdx]) && preg_match('/data-pdf="\.\.\/([^"#]+)/', $cells[$cellIdx], $pdfMatch)) {
                    $pdfPaths[$trim] = urldecode($pdfMatch[1]);
                }
            }
        } else {
            for ($t = 1; $t <= 4; $t++) {
                if (isset($cells[$t]) && preg_match('/data-pdf="\.\.\/([^"#]+)/', $cells[$t], $pdfMatch)) {
                    $pdfPaths[$t] = urldecode($pdfMatch[1]);
                }
            }
        }

        foreach ($pdfPaths as $trim => $pdfPath) {
            $pdfPathSql = str_replace("'", "''", $pdfPath);
            $output[] = "INSERT INTO `seac_pdfs` (`bloque_id`, `concepto_id`, `trimestre`, `pdf_path`) VALUES ($bloqueSubq, $conceptoSubq, $trim, '$pdfPathSql') ON DUPLICATE KEY UPDATE `pdf_path` = VALUES(`pdf_path`);";
        }

        $output[] = "";
        $conceptoOrden++;
    }

    $output[] = "";
    $bloqueOrden++;
}

$output[] = "-- ═══════════════════════════════════════════════════════════════════════════";
$output[] = "-- Migracion completada - " . ($bloqueOrden - 1) . " bloques procesados";
$output[] = "-- ═══════════════════════════════════════════════════════════════════════════";

$sqlFile = __DIR__ . '/seac_migration.sql';
file_put_contents($sqlFile, implode("\n", $output) . "\n");

echo "SQL generado en: $sqlFile\n";
echo "Bloques procesados: " . ($bloqueOrden - 1) . "\n";
