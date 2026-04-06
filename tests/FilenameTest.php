<?php
/**
 * Feature: admin-historial-reportes, Property 5: El nombre de archivo sigue el patrón requerido para cualquier rango de fechas
 *
 * Validates: Requisitos 3.8, 4.6
 *
 * Para cualquier par de fechas válidas (fecha_ini, fecha_fin), la función
 * report_filename() debe retornar una cadena que coincida exactamente con el
 * patrón historial_{fecha_ini}_{fecha_fin}.{ext}, tanto para PDF como para Excel.
 * 100 iteraciones con pares de fechas generados aleatoriamente.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../admin/reportes_historial.php';

class FilenameTest extends TestCase
{
    // ── Generador de datos ────────────────────────────────────────────────────

    /**
     * Genera 100 pares de fechas aleatorias en formato YYYY-MM-DD,
     * más casos concretos (mismo día, año bisiesto).
     *
     * @return array<int, array{string, string}>
     */
    public function dateRangeProvider(): array
    {
        $cases = [];

        // 100 pares aleatorios
        for ($i = 0; $i < 100; $i++) {
            $year1  = random_int(2020, 2030);
            $month1 = random_int(1, 12);
            $day1   = random_int(1, 28); // 28 es seguro para todos los meses
            $fechaIni = sprintf('%04d-%02d-%02d', $year1, $month1, $day1);

            $year2  = random_int(2020, 2030);
            $month2 = random_int(1, 12);
            $day2   = random_int(1, 28);
            $fechaFin = sprintf('%04d-%02d-%02d', $year2, $month2, $day2);

            $cases[] = [$fechaIni, $fechaFin];
        }

        // Caso concreto: mismo día inicio y fin
        $cases[] = ['2025-06-15', '2025-06-15'];

        // Caso concreto: fechas con año bisiesto (29 de febrero)
        $cases[] = ['2024-02-29', '2024-02-29'];
        $cases[] = ['2024-02-01', '2024-02-29'];

        return $cases;
    }

    // ── Propiedad 5: Nombre de archivo sigue el patrón requerido ─────────────

    /**
     * **Validates: Requisitos 3.8, 4.6**
     *
     * Propiedad 5: Para cualquier par de fechas válidas, report_filename()
     * retorna exactamente "historial_{fecha_ini}_{fecha_fin}.{ext}".
     *
     * @dataProvider dateRangeProvider
     */
    public function testFilenameMatchesPattern(string $fecha_ini, string $fecha_fin): void
    {
        $resultPdf  = report_filename($fecha_ini, $fecha_fin, 'pdf');
        $resultXlsx = report_filename($fecha_ini, $fecha_fin, 'xlsx');

        // Verificar patrón exacto para PDF
        $expectedPdf = "historial_{$fecha_ini}_{$fecha_fin}.pdf";
        $this->assertSame(
            $expectedPdf,
            $resultPdf,
            "El nombre de archivo PDF no coincide con el patrón esperado para fechas: $fecha_ini / $fecha_fin"
        );

        // Verificar patrón exacto para Excel
        $expectedXlsx = "historial_{$fecha_ini}_{$fecha_fin}.xlsx";
        $this->assertSame(
            $expectedXlsx,
            $resultXlsx,
            "El nombre de archivo XLSX no coincide con el patrón esperado para fechas: $fecha_ini / $fecha_fin"
        );

        // Verificar que el resultado PDF termina en .pdf
        $this->assertStringEndsWith(
            '.pdf',
            $resultPdf,
            "El nombre de archivo para PDF debe terminar en .pdf"
        );

        // Verificar que el resultado Excel termina en .xlsx
        $this->assertStringEndsWith(
            '.xlsx',
            $resultXlsx,
            "El nombre de archivo para Excel debe terminar en .xlsx"
        );

        // Verificar que ambos resultados comienzan con "historial_"
        $this->assertStringStartsWith(
            'historial_',
            $resultPdf,
            "El nombre de archivo PDF debe comenzar con 'historial_'"
        );
        $this->assertStringStartsWith(
            'historial_',
            $resultXlsx,
            "El nombre de archivo XLSX debe comenzar con 'historial_'"
        );

        // Verificar que las fechas están presentes en el nombre
        $this->assertStringContainsString(
            $fecha_ini,
            $resultPdf,
            "El nombre de archivo PDF debe contener la fecha de inicio"
        );
        $this->assertStringContainsString(
            $fecha_fin,
            $resultPdf,
            "El nombre de archivo PDF debe contener la fecha de fin"
        );
    }
}
