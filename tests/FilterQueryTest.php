<?php
/**
 * Feature: admin-historial-reportes, Property 1: Equivalencia de filtros entre historial.php y reportes_historial.php
 *
 * Validates: Requisito 2.3
 *
 * Para cualquier combinación de parámetros de filtro (fecha_ini, fecha_fin, usuario,
 * seccion, accion), la función build_filter_query() debe producir exactamente el mismo
 * WHERE clause y array de parámetros que la lógica de filtrado de admin/historial.php.
 * 100 iteraciones con inputs generados aleatoriamente.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Cargar la función pura build_filter_query() desde reportes_historial.php.
// El archivo solo define funciones (sin código de ejecución a nivel raíz),
// por lo que es seguro incluirlo directamente en el contexto de pruebas.
require_once __DIR__ . '/../admin/reportes_historial.php';

class FilterQueryTest extends TestCase
{
    // ── Generadores de datos ──────────────────────────────────────────────────

    /**
     * Genera una fecha aleatoria en formato YYYY-MM-DD, o cadena vacía.
     */
    private function randomDate(bool $allowEmpty = true): string
    {
        if ($allowEmpty && random_int(0, 4) === 0) {
            return '';
        }
        $year  = random_int(2020, 2026);
        $month = random_int(1, 12);
        $day   = random_int(1, 28); // 28 es seguro para todos los meses
        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    /**
     * Genera un string alfanumérico aleatorio o cadena vacía.
     */
    private function randomAlphanumeric(bool $allowEmpty = true): string
    {
        if ($allowEmpty && random_int(0, 3) === 0) {
            return '';
        }
        $chars  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $length = random_int(1, 20);
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $result;
    }

    /**
     * Genera una acción aleatoria de la lista permitida (incluyendo vacío).
     */
    private function randomAccion(): string
    {
        $acciones = ['crear', 'editar', 'eliminar', 'subir', 'login', 'logout', 'reorden', ''];
        return $acciones[array_rand($acciones)];
    }

    /**
     * Genera 100 combinaciones aleatorias de filtros para el @dataProvider.
     *
     * @return array<int, array{array}>
     */
    public function filterCombinationsProvider(): array
    {
        $cases = [];
        for ($i = 0; $i < 100; $i++) {
            $get = [];

            // fecha_ini: puede estar ausente (omitida) o ser una fecha aleatoria
            $fechaIni = $this->randomDate(allowEmpty: true);
            if ($fechaIni !== '') {
                $get['fecha_ini'] = $fechaIni;
            }

            // fecha_fin: puede estar ausente o ser una fecha aleatoria
            $fechaFin = $this->randomDate(allowEmpty: true);
            if ($fechaFin !== '') {
                $get['fecha_fin'] = $fechaFin;
            }

            // usuario: puede estar ausente, vacío o tener valor
            $usuario = $this->randomAlphanumeric(allowEmpty: true);
            if (random_int(0, 4) !== 0) { // 80% de probabilidad de incluir la clave
                $get['usuario'] = $usuario;
            }

            // seccion: puede estar ausente, vacío o tener valor
            $seccion = $this->randomAlphanumeric(allowEmpty: true);
            if (random_int(0, 4) !== 0) {
                $get['seccion'] = $seccion;
            }

            // accion: de la lista permitida
            $accion = $this->randomAccion();
            if (random_int(0, 4) !== 0) {
                $get['accion'] = $accion;
            }

            $cases[] = [$get];
        }
        return $cases;
    }

    // ── Lógica de referencia de admin/historial.php ───────────────────────────

    /**
     * Replica exactamente la lógica de construcción del WHERE de admin/historial.php.
     *
     * @param  array $get  Parámetros de filtro (equivalente a $_GET)
     * @return array{whereStr: string, params: array}
     */
    private function referenceFilterLogic(array $get): array
    {
        $filtro_fecha_ini = trim($get['fecha_ini'] ?? date('Y-m-01'));
        $filtro_fecha_fin = trim($get['fecha_fin'] ?? date('Y-m-d'));
        $filtro_usuario   = trim($get['usuario']   ?? '');
        $filtro_seccion   = trim($get['seccion']   ?? '');
        $filtro_accion    = trim($get['accion']    ?? '');

        $where  = ['DATE(created_at) BETWEEN ? AND ?'];
        $params = [$filtro_fecha_ini, $filtro_fecha_fin];

        if ($filtro_usuario) {
            $where[]  = 'username LIKE ?';
            $params[] = "%{$filtro_usuario}%";
        }
        if ($filtro_seccion) {
            $where[]  = 'seccion LIKE ?';
            $params[] = "%{$filtro_seccion}%";
        }
        if ($filtro_accion) {
            $where[]  = 'accion = ?';
            $params[] = $filtro_accion;
        }

        $whereStr = implode(' AND ', $where);

        return ['whereStr' => $whereStr, 'params' => $params];
    }

    // ── Propiedad 1: Equivalencia de filtros ─────────────────────────────────

    /**
     * **Validates: Requisito 2.3**
     *
     * Propiedad 1: Para cualquier combinación de parámetros de filtro,
     * build_filter_query() produce exactamente el mismo WHERE string y array
     * de parámetros que la lógica de referencia de admin/historial.php.
     *
     * @dataProvider filterCombinationsProvider
     */
    public function testFilterEquivalenceWithHistorial(array $get): void
    {
        // Resultado de la función bajo prueba
        $result = build_filter_query($get);

        // Resultado de la lógica de referencia (historial.php)
        $reference = $this->referenceFilterLogic($get);

        $this->assertSame(
            $reference['whereStr'],
            $result['where'],
            sprintf(
                "WHERE string difiere para GET=%s\nEsperado: %s\nObtenido: %s",
                json_encode($get),
                $reference['whereStr'],
                $result['where']
            )
        );

        $this->assertSame(
            $reference['params'],
            $result['params'],
            sprintf(
                "Params difieren para GET=%s\nEsperado: %s\nObtenido: %s",
                json_encode($get),
                json_encode($reference['params']),
                json_encode($result['params'])
            )
        );
    }

    // ── Casos concretos adicionales ───────────────────────────────────────────

    /**
     * Sin ningún filtro (GET vacío): solo el rango de fechas por defecto.
     */
    public function testEmptyGetProducesDefaultDateRange(): void
    {
        $result    = build_filter_query([]);
        $reference = $this->referenceFilterLogic([]);

        $this->assertSame($reference['whereStr'], $result['where']);
        $this->assertSame($reference['params'],   $result['params']);
        $this->assertStringContainsString('DATE(created_at) BETWEEN ? AND ?', $result['where']);
        $this->assertCount(2, $result['params']);
    }

    /**
     * Todos los filtros activos: WHERE debe tener 5 condiciones.
     */
    public function testAllFiltersActiveProducesFiveConditions(): void
    {
        $get = [
            'fecha_ini' => '2025-01-01',
            'fecha_fin' => '2025-01-31',
            'usuario'   => 'admin',
            'seccion'   => 'Noticias',
            'accion'    => 'crear',
        ];

        $result    = build_filter_query($get);
        $reference = $this->referenceFilterLogic($get);

        $this->assertSame($reference['whereStr'], $result['where']);
        $this->assertSame($reference['params'],   $result['params']);
        $this->assertCount(5, $result['params']);
    }

    /**
     * Solo fecha_ini y fecha_fin: WHERE con una sola condición (sin filtros adicionales).
     */
    public function testOnlyDatesProducesOneCondition(): void
    {
        $get = [
            'fecha_ini' => '2025-06-01',
            'fecha_fin' => '2025-06-30',
        ];

        $result    = build_filter_query($get);
        $reference = $this->referenceFilterLogic($get);

        $this->assertSame($reference['whereStr'], $result['where']);
        $this->assertSame($reference['params'],   $result['params']);
        $this->assertCount(2, $result['params']);
        // Solo debe haber la condición BETWEEN, sin cláusulas adicionales (username, seccion, accion)
        $this->assertStringNotContainsString('username', $result['where']);
        $this->assertStringNotContainsString('seccion',  $result['where']);
        $this->assertStringNotContainsString('accion',   $result['where']);
    }

    // ── Propiedad 10: Parámetros preparados (nunca interpolación directa) ────

    /**
     * Genera 100 combinaciones con caracteres especiales SQL para verificar
     * que build_filter_query() nunca interpola valores en el WHERE string.
     *
     * @return array<int, array{string, string, string}>
     */
    public function sqlInjectionInputsProvider(): array
    {
        $specialParts = ["'", '"', ';', '--', 'DROP TABLE', 'OR 1=1', '\\', '%', '_'];
        $cases = [];

        for ($i = 0; $i < 100; $i++) {
            // Construir un string aleatorio combinando partes especiales
            $buildRandom = function () use ($specialParts): string {
                $count  = random_int(1, 4);
                $result = '';
                for ($j = 0; $j < $count; $j++) {
                    $result .= $specialParts[array_rand($specialParts)];
                    // Añadir relleno alfanumérico ocasional
                    if (random_int(0, 1)) {
                        $result .= chr(random_int(ord('a'), ord('z')));
                    }
                }
                return $result;
            };

            $cases[] = [$buildRandom(), $buildRandom(), $buildRandom()];
        }

        return $cases;
    }

    /**
     * **Validates: Requisito 6.3**
     *
     * Propiedad 10: Para cualquier valor de parámetro de filtro (incluyendo
     * cadenas con caracteres especiales SQL), build_filter_query() nunca
     * interpola los valores directamente en el WHERE string; siempre los
     * coloca en el array params usando `?` como placeholder.
     *
     * @dataProvider sqlInjectionInputsProvider
     */
    public function testQueryBuilderAlwaysUsesParameters(
        string $usuario,
        string $seccion,
        string $accion
    ): void {
        $get = [
            'fecha_ini' => '2025-01-01',
            'fecha_fin' => '2025-12-31',
            'usuario'   => $usuario,
            'seccion'   => $seccion,
            'accion'    => $accion,
        ];

        $result = build_filter_query($get);
        $where  = $result['where'];
        $params = $result['params'];

        $usuarioTrimmed = trim($usuario);
        $seccionTrimmed = trim($seccion);
        $accionTrimmed  = trim($accion);

        // El WHERE solo debe contener '?' como placeholders.
        // Verificamos que el WHERE es exactamente la estructura SQL esperada
        // (sin ningún valor de usuario interpolado).
        $this->assertMatchesRegularExpression(
            '/^[A-Za-z0-9_()\s\?\=\<\>\!\%\.\,\'\"AND|OR|BETWEEN|LIKE|DATE]+$/',
            $where,
            "El WHERE solo debe contener SQL estructural con '?' como placeholders"
        );

        // Los valores con contenido deben estar en params, no en el WHERE
        if ($usuarioTrimmed !== '') {
            $this->assertStringContainsString(
                '?',
                $where,
                "WHERE debe contener '?' para usuario con valor"
            );
            $this->assertContains(
                '%' . $usuarioTrimmed . '%',
                $params,
                "El valor de usuario debe estar en el array params (envuelto en %)"
            );
            // Verificar que el valor NO está directamente en el WHERE
            // comparando la cantidad de '?' con la cantidad de params
            $this->assertSame(
                substr_count($where, '?'),
                count($params),
                "El número de '?' en WHERE debe coincidir con el número de params"
            );
        }

        if ($seccionTrimmed !== '') {
            $this->assertStringContainsString(
                '?',
                $where,
                "WHERE debe contener '?' para seccion con valor"
            );
            $this->assertContains(
                '%' . $seccionTrimmed . '%',
                $params,
                "El valor de seccion debe estar en el array params (envuelto en %)"
            );
        }

        if ($accionTrimmed !== '') {
            $this->assertStringContainsString(
                '?',
                $where,
                "WHERE debe contener '?' para accion con valor"
            );
            $this->assertContains(
                $accionTrimmed,
                $params,
                "El valor de accion debe estar en el array params"
            );
        }

        // Invariante clave: el número de '?' en WHERE == número de params
        $this->assertSame(
            substr_count($where, '?'),
            count($params),
            "El número de '?' en WHERE debe coincidir exactamente con el número de params"
        );

        // El WHERE nunca debe contener comillas simples/dobles ni punto y coma
        // provenientes de los valores de filtro (señal de interpolación directa).
        // Solo verificamos patrones que NO pueden aparecer en SQL estructural.
        $this->assertStringNotContainsString(
            "' OR",
            $where,
            "El WHERE no debe contener patrones de inyección SQL"
        );
        $this->assertStringNotContainsString(
            '; DROP',
            $where,
            "El WHERE no debe contener sentencias SQL adicionales"
        );
    }

    /**
     * Accion vacía no debe agregar condición de accion al WHERE.
     */
    public function testEmptyAccionIsIgnored(): void
    {
        $get = [
            'fecha_ini' => '2025-01-01',
            'fecha_fin' => '2025-12-31',
            'accion'    => '',
        ];

        $result    = build_filter_query($get);
        $reference = $this->referenceFilterLogic($get);

        $this->assertSame($reference['whereStr'], $result['where']);
        $this->assertSame($reference['params'],   $result['params']);
        $this->assertStringNotContainsString('accion', $result['where']);
    }

    /**
     * Usuario con espacios al inicio/fin: trim debe aplicarse.
     */
    public function testUsuarioWithWhitespaceIsTrimmed(): void
    {
        $get = [
            'fecha_ini' => '2025-01-01',
            'fecha_fin' => '2025-01-31',
            'usuario'   => '  admin  ',
        ];

        $result    = build_filter_query($get);
        $reference = $this->referenceFilterLogic($get);

        $this->assertSame($reference['whereStr'], $result['where']);
        $this->assertSame($reference['params'],   $result['params']);
        // El valor en params debe estar trimmed y envuelto en %...%
        $this->assertContains('%admin%', $result['params']);
    }
}
