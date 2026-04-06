<?php
/**
 * Feature: admin-historial-reportes
 *
 * Pruebas de propiedades para la función build_excel():
 *   - Propiedad 6: La hoja "Historial" del Excel contiene todos los registros con todas las columnas
 *   - Propiedad 7: La hoja "Estadísticas" agrega correctamente los conteos
 *   - Propiedad 8: La celda A1 del Excel contiene el título con el periodo para cualquier rango de fechas
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../admin/reportes_historial.php';

class ExcelBuilderTest extends TestCase
{
    // ── Helpers de generación aleatoria ──────────────────────────────────────

    /** Genera una fecha aleatoria en formato YYYY-MM-DD. */
    private function randomDate(): string
    {
        return sprintf(
            '%04d-%02d-%02d',
            random_int(2020, 2026),
            random_int(1, 12),
            random_int(1, 28)
        );
    }

    /** Genera un string alfanumérico aleatorio no vacío. */
    private function randomString(int $minLen = 3, int $maxLen = 20): string
    {
        $chars  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 ';
        $length = random_int($minLen, $maxLen);
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return trim($result) ?: 'texto';
    }

    /** Genera un array de registros aleatorios (0-20 elementos). */
    private function randomRegistros(): array
    {
        $count = random_int(0, 20);
        $registros = [];
        for ($i = 0; $i < $count; $i++) {
            $registros[] = [
                'id'          => random_int(1, 9999),
                'fecha'       => $this->randomDate(),
                'hora'        => sprintf('%02d:%02d:%02d', random_int(0, 23), random_int(0, 59), random_int(0, 59)),
                'username'    => $this->randomString(3, 15),
                'accion'      => $this->randomString(3, 10),
                'seccion'     => $this->randomString(3, 15),
                'descripcion' => $this->randomString(5, 40),
                'ip'          => sprintf('%d.%d.%d.%d', random_int(1, 254), random_int(0, 255), random_int(0, 255), random_int(1, 254)),
            ];
        }
        return $registros;
    }

    /** Genera un array de stats por acción aleatorios (1-7 elementos). */
    private function randomStats(): array
    {
        $count    = random_int(1, 7);
        $acciones = ['crear', 'editar', 'eliminar', 'subir', 'login', 'logout', 'reorden'];
        shuffle($acciones);
        $stats = [];
        for ($i = 0; $i < $count; $i++) {
            $stats[] = [
                'accion' => $acciones[$i],
                'total'  => random_int(1, 100),
            ];
        }
        return $stats;
    }

    /** Genera un array de stats por sección aleatorios (1-5 elementos). */
    private function randomStatsSeccion(): array
    {
        $count    = random_int(1, 5);
        $secciones = ['Noticias', 'Galería', 'Programas', 'Usuarios', 'Transparencia'];
        shuffle($secciones);
        $stats = [];
        for ($i = 0; $i < $count; $i++) {
            $stats[] = [
                'seccion' => $secciones[$i],
                'total'   => random_int(1, 100),
            ];
        }
        return $stats;
    }

    // ── DataProviders ─────────────────────────────────────────────────────────

    /**
     * 100 arrays de registros aleatorios (0-20 registros).
     *
     * @return array<int, array{array}>
     */
    public function randomRegistrosProvider(): array
    {
        $cases = [];
        for ($i = 0; $i < 100; $i++) {
            $cases[] = [$this->randomRegistros()];
        }
        return $cases;
    }

    /**
     * 100 pares de arrays de stats y stats_seccion aleatorios.
     *
     * @return array<int, array{array, array}>
     */
    public function randomStatsProvider(): array
    {
        $cases = [];
        for ($i = 0; $i < 100; $i++) {
            $cases[] = [
                $this->randomStats(),
                $this->randomStatsSeccion(),
            ];
        }
        return $cases;
    }

    /**
     * 100 pares de fechas aleatorias.
     *
     * @return array<int, array{string, string}>
     */
    public function dateRangeProvider(): array
    {
        $cases = [];
        for ($i = 0; $i < 100; $i++) {
            $cases[] = [
                $this->randomDate(),
                $this->randomDate(),
            ];
        }
        return $cases;
    }

    // ── Propiedad 6: Hoja "Historial" contiene todos los registros ────────────

    /**
     * **Validates: Requisito 4.2**
     *
     * Propiedad 6: Para cualquier array de registros del historial, la hoja
     * "Historial" generada por build_excel() debe contener exactamente una fila
     * de datos por registro (a partir de la fila 3), con los ocho campos
     * requeridos: ID, Fecha, Hora, Usuario, Acción, Sección, Descripción e IP.
     *
     * @dataProvider randomRegistrosProvider
     */
    public function testHistorialSheetContainsAllRecords(array $registros): void
    {
        $filtros     = ['fecha_ini' => '2025-01-01', 'fecha_fin' => '2025-01-31'];
        $spreadsheet = build_excel($registros, [], [], $filtros);

        // Verificar que la hoja "Historial" existe
        $this->assertTrue(
            $spreadsheet->sheetNameExists('Historial'),
            'El Spreadsheet debe contener una hoja llamada "Historial"'
        );

        $sheet = $spreadsheet->getSheetByName('Historial');

        // Verificar encabezados en fila 2
        $expectedHeaders = ['ID', 'Fecha', 'Hora', 'Usuario', 'Acción', 'Sección', 'Descripción', 'IP'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        foreach ($expectedHeaders as $idx => $header) {
            $cell = $cols[$idx] . '2';
            $this->assertSame(
                $header,
                $sheet->getCell($cell)->getValue(),
                "La celda {$cell} debe contener el encabezado \"{$header}\""
            );
        }

        // Verificar que el número de filas de datos (a partir de fila 3) es exactamente count($registros)
        $dataRowCount = 0;
        $maxRow = $sheet->getHighestRow();
        for ($row = 3; $row <= $maxRow; $row++) {
            // Una fila de datos existe si al menos la columna A tiene valor
            $val = $sheet->getCell('A' . $row)->getValue();
            if ($val !== null && $val !== '') {
                $dataRowCount++;
            }
        }

        $this->assertSame(
            count($registros),
            $dataRowCount,
            sprintf(
                'La hoja "Historial" debe contener exactamente %d fila(s) de datos a partir de la fila 3, encontradas: %d',
                count($registros),
                $dataRowCount
            )
        );
    }

    // ── Propiedad 7: Hoja "Estadísticas" agrega correctamente los conteos ─────

    /**
     * **Validates: Requisitos 4.3, 5.3**
     *
     * Propiedad 7: Para cualquier array de stats y stats_seccion, los conteos en
     * la hoja "Estadísticas" generada por build_excel() deben ser consistentes
     * con los datos de entrada: la suma de todos los totales en columna B (acción)
     * debe igualar la suma de $stats totales, y la suma de todos los totales en
     * columna E (sección) debe igualar la suma de $stats_seccion totales.
     *
     * @dataProvider randomStatsProvider
     */
    public function testStatsSheetCountsAreConsistent(array $stats, array $stats_seccion): void
    {
        $filtros     = ['fecha_ini' => '2025-01-01', 'fecha_fin' => '2025-01-31'];
        $spreadsheet = build_excel([], $stats, $stats_seccion, $filtros);

        // Verificar que la hoja "Estadísticas" existe
        $this->assertTrue(
            $spreadsheet->sheetNameExists('Estadísticas'),
            'El Spreadsheet debe contener una hoja llamada "Estadísticas"'
        );

        $statsSheet = $spreadsheet->getSheetByName('Estadísticas');

        // Sumar todos los totales en columna B (acción), a partir de fila 2
        $sumAccion = 0;
        $maxRow    = $statsSheet->getHighestRow();
        for ($row = 2; $row <= $maxRow; $row++) {
            $val = $statsSheet->getCell('B' . $row)->getValue();
            if (is_numeric($val)) {
                $sumAccion += (int)$val;
            }
        }

        $expectedSumAccion = array_sum(array_column($stats, 'total'));
        $this->assertSame(
            $expectedSumAccion,
            $sumAccion,
            sprintf(
                'La suma de totales en columna B (acción) debe ser %d, obtenida: %d',
                $expectedSumAccion,
                $sumAccion
            )
        );

        // Sumar todos los totales en columna E (sección), a partir de fila 2
        $sumSeccion = 0;
        for ($row = 2; $row <= $maxRow; $row++) {
            $val = $statsSheet->getCell('E' . $row)->getValue();
            if (is_numeric($val)) {
                $sumSeccion += (int)$val;
            }
        }

        $expectedSumSeccion = array_sum(array_column($stats_seccion, 'total'));
        $this->assertSame(
            $expectedSumSeccion,
            $sumSeccion,
            sprintf(
                'La suma de totales en columna E (sección) debe ser %d, obtenida: %d',
                $expectedSumSeccion,
                $sumSeccion
            )
        );
    }

    // ── Propiedad 8: Celda A1 contiene título y periodo ───────────────────────

    /**
     * **Validates: Requisito 4.5**
     *
     * Propiedad 8: Para cualquier par de fechas válidas (fecha_ini, fecha_fin),
     * la celda A1 de la hoja "Historial" generada por build_excel() debe contener
     * una cadena que incluya "DIF San Mateo Atenco" y las fechas del periodo.
     *
     * @dataProvider dateRangeProvider
     */
    public function testCellA1ContainsTitleAndPeriod(string $fecha_ini, string $fecha_fin): void
    {
        $filtros     = ['fecha_ini' => $fecha_ini, 'fecha_fin' => $fecha_fin];
        $spreadsheet = build_excel([], [], [], $filtros);

        $sheet  = $spreadsheet->getSheetByName('Historial');
        $cellA1 = (string)$sheet->getCell('A1')->getValue();

        $this->assertStringContainsString(
            'DIF San Mateo Atenco',
            $cellA1,
            'La celda A1 debe contener "DIF San Mateo Atenco"'
        );

        $this->assertStringContainsString(
            $fecha_ini,
            $cellA1,
            "La celda A1 debe contener la fecha_ini: {$fecha_ini}"
        );

        $this->assertStringContainsString(
            $fecha_fin,
            $cellA1,
            "La celda A1 debe contener la fecha_fin: {$fecha_fin}"
        );
    }
}
