<?php
/**
 * Feature: admin-historial-reportes
 *
 * Pruebas de propiedades para la función build_pdf_html():
 *   - Propiedad 2:  El PDF contiene encabezado institucional para cualquier dataset
 *   - Propiedad 3:  La gráfica PDF refleja todos los tipos de acción presentes
 *   - Propiedad 4:  La tabla PDF contiene todos los registros con todas las columnas requeridas
 *   - Propiedad 11: La gráfica de distribución por día se omite cuando hay menos de 2 días activos
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../admin/reportes_historial.php';

class PdfBuilderTest extends TestCase
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
    private function randomRegistros(int $count = -1): array
    {
        if ($count < 0) {
            $count = random_int(0, 20);
        }
        $registros = [];
        for ($i = 0; $i < $count; $i++) {
            $registros[] = [
                'created_at'  => $this->randomDate() . ' ' . sprintf('%02d:%02d:%02d', random_int(0, 23), random_int(0, 59), random_int(0, 59)),
                'username'    => $this->randomString(3, 15),
                'accion'      => $this->randomString(3, 10),
                'seccion'     => $this->randomString(3, 15),
                'descripcion' => $this->randomString(5, 40),
            ];
        }
        return $registros;
    }

    /** Genera un array de stats aleatorios (1-7 tipos de acción). */
    private function randomStats(int $count = -1): array
    {
        if ($count < 0) {
            $count = random_int(1, 7);
        }
        $acciones = ['crear', 'editar', 'eliminar', 'subir', 'login', 'logout', 'reorden'];
        shuffle($acciones);
        $stats = [];
        for ($i = 0; $i < $count; $i++) {
            $stats[] = [
                'accion' => $acciones[$i % count($acciones)],
                'total'  => random_int(1, 100),
            ];
        }
        return $stats;
    }

    /** Genera un array de stats_dia aleatorios. */
    private function randomStatsDia(int $count): array
    {
        $stats = [];
        for ($i = 0; $i < $count; $i++) {
            $stats[] = [
                'dia'   => $this->randomDate(),
                'total' => random_int(1, 50),
            ];
        }
        return $stats;
    }

    /** Genera un array de filtros con fechas aleatorias. */
    private function randomFiltros(): array
    {
        return [
            'fecha_ini' => $this->randomDate(),
            'fecha_fin' => $this->randomDate(),
        ];
    }

    // ── DataProviders ─────────────────────────────────────────────────────────

    /**
     * 100 datasets aleatorios: registros, stats, stats_dia y filtros.
     *
     * @return array<int, array{array, array, array, array}>
     */
    public function randomDatasetProvider(): array
    {
        $cases = [];
        for ($i = 0; $i < 100; $i++) {
            $cases[] = [
                $this->randomRegistros(),
                $this->randomStats(),
                $this->randomStatsDia(random_int(0, 5)),
                $this->randomFiltros(),
            ];
        }
        return $cases;
    }

    /**
     * 100 arrays de stats aleatorios (1-7 tipos de acción).
     *
     * @return array<int, array{array}>
     */
    public function randomStatsProvider(): array
    {
        $cases = [];
        for ($i = 0; $i < 100; $i++) {
            $cases[] = [$this->randomStats()];
        }
        return $cases;
    }

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

    // ── Propiedad 2: Encabezado institucional ─────────────────────────────────

    /**
     * **Validates: Requisito 3.2**
     *
     * Propiedad 2: Para cualquier rango de fechas válido y conjunto de registros,
     * el HTML generado por build_pdf_html() debe contener el nombre institucional,
     * el título del reporte y las fechas del periodo.
     *
     * @dataProvider randomDatasetProvider
     */
    public function testPdfContainsInstitutionalHeader(
        array $registros,
        array $stats,
        array $stats_dia,
        array $filtros
    ): void {
        $html = build_pdf_html($registros, $stats, $stats_dia, $filtros);

        $this->assertStringContainsString(
            'DIF San Mateo Atenco',
            $html,
            'El HTML debe contener el nombre institucional "DIF San Mateo Atenco"'
        );

        $this->assertStringContainsString(
            'Reporte de Historial de Actividad',
            $html,
            'El HTML debe contener el título "Reporte de Historial de Actividad"'
        );

        $this->assertStringContainsString(
            $filtros['fecha_ini'],
            $html,
            "El HTML debe contener la fecha_ini: {$filtros['fecha_ini']}"
        );

        $this->assertStringContainsString(
            $filtros['fecha_fin'],
            $html,
            "El HTML debe contener la fecha_fin: {$filtros['fecha_fin']}"
        );
    }

    // ── Propiedad 3: Gráfica refleja todos los tipos de acción ────────────────

    /**
     * **Validates: Requisitos 3.3, 5.1**
     *
     * Propiedad 3: Para cualquier array de estadísticas no vacío, el HTML de la
     * gráfica generado por build_pdf_html() debe contener un elemento de barra
     * (div con background:#C8102E) por cada tipo de acción presente en stats,
     * y cada nombre de acción debe aparecer en el HTML.
     *
     * @dataProvider randomStatsProvider
     */
    public function testPdfChartContainsAllActionTypes(array $stats): void
    {
        $filtros = $this->randomFiltros();
        $html    = build_pdf_html([], $stats, [], $filtros);

        // Contar cuántos divs de barra roja hay en el HTML
        $barCount = substr_count($html, 'background:#C8102E;height:14px');

        $this->assertSame(
            count($stats),
            $barCount,
            sprintf(
                'El HTML debe contener exactamente %d barra(s) de acción, encontradas: %d. Stats: %s',
                count($stats),
                $barCount,
                json_encode($stats)
            )
        );

        // Verificar que cada nombre de acción aparece en el HTML
        foreach ($stats as $s) {
            $this->assertStringContainsString(
                htmlspecialchars($s['accion'], ENT_QUOTES, 'UTF-8'),
                $html,
                "El HTML debe contener el nombre de acción: {$s['accion']}"
            );
        }
    }

    // ── Propiedad 4: Tabla contiene todos los registros ───────────────────────

    /**
     * **Validates: Requisito 3.4**
     *
     * Propiedad 4: Para cualquier array de registros del historial, el HTML de
     * tabla generado por build_pdf_html() debe contener exactamente una fila <tr
     * por registro en el tbody, y las 5 columnas requeridas deben estar en el thead.
     *
     * @dataProvider randomRegistrosProvider
     */
    public function testPdfTableContainsAllRecords(array $registros): void
    {
        $filtros = $this->randomFiltros();
        $html    = build_pdf_html($registros, [], [], $filtros);

        // Extraer el tbody de la tabla de datos (data-table)
        // La tabla de datos tiene class="data-table"
        $tbodyMatches = [];
        preg_match('/<table class="data-table">.*?<tbody>(.*?)<\/tbody>/s', $html, $tbodyMatches);

        $this->assertNotEmpty(
            $tbodyMatches,
            'El HTML debe contener una tabla con class="data-table" y un tbody'
        );

        $tbody    = $tbodyMatches[1] ?? '';
        $rowCount = substr_count($tbody, '<tr');

        $this->assertSame(
            count($registros),
            $rowCount,
            sprintf(
                'El tbody debe contener exactamente %d fila(s) <tr, encontradas: %d',
                count($registros),
                $rowCount
            )
        );

        // Verificar que las 5 columnas requeridas están en el thead
        $theadMatches = [];
        preg_match('/<thead>(.*?)<\/thead>/s', $html, $theadMatches);
        $thead = $theadMatches[1] ?? '';

        $this->assertStringContainsString(
            'Fecha/Hora',
            $thead,
            'El thead debe contener la columna "Fecha/Hora"'
        );
        $this->assertStringContainsString(
            'Usuario',
            $thead,
            'El thead debe contener la columna "Usuario"'
        );
        $this->assertStringContainsString(
            'Acción',
            $thead,
            'El thead debe contener la columna "Acción"'
        );
        $this->assertStringContainsString(
            'Sección',
            $thead,
            'El thead debe contener la columna "Sección"'
        );
        $this->assertStringContainsString(
            'Descripción',
            $thead,
            'El thead debe contener la columna "Descripción"'
        );
    }

    // ── Propiedad 11: Omisión de gráfica por día ──────────────────────────────

    /**
     * **Validates: Requisito 5.4**
     *
     * Propiedad 11: Cuando el número de días distintos con actividad es menor a 2,
     * el HTML generado por build_pdf_html() no debe contener la sección de gráfica
     * de distribución por día. Con 2 o más días, sí debe aparecer.
     */
    public function testDailyChartOmittedWhenLessThanTwoDays(): void
    {
        $filtros = ['fecha_ini' => '2025-01-01', 'fecha_fin' => '2025-01-01'];

        // 0 días: sin stats_dia
        $html0 = build_pdf_html([], [], [], $filtros);
        $this->assertStringNotContainsString(
            'Distribución por Día',
            $html0,
            'Con 0 días activos, el HTML NO debe contener "Distribución por Día"'
        );

        // 1 día: stats_dia con un solo elemento
        $statsDia1 = [['dia' => '2025-01-01', 'total' => 5]];
        $html1     = build_pdf_html([], [], $statsDia1, $filtros);
        $this->assertStringNotContainsString(
            'Distribución por Día',
            $html1,
            'Con 1 día activo, el HTML NO debe contener "Distribución por Día"'
        );

        // 2 días: stats_dia con dos elementos
        $statsDia2 = [
            ['dia' => '2025-01-01', 'total' => 3],
            ['dia' => '2025-01-02', 'total' => 7],
        ];
        $html2 = build_pdf_html([], [], $statsDia2, $filtros);
        $this->assertStringContainsString(
            'Distribución por Día',
            $html2,
            'Con 2 días activos, el HTML SÍ debe contener "Distribución por Día"'
        );

        // 3+ días: stats_dia con tres elementos
        $statsDia3 = [
            ['dia' => '2025-01-01', 'total' => 2],
            ['dia' => '2025-01-02', 'total' => 4],
            ['dia' => '2025-01-03', 'total' => 6],
        ];
        $html3 = build_pdf_html([], [], $statsDia3, $filtros);
        $this->assertStringContainsString(
            'Distribución por Día',
            $html3,
            'Con 3+ días activos, el HTML SÍ debe contener "Distribución por Día"'
        );
    }
}
