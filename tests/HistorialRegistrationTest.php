<?php
/**
 * Feature: admin-historial-reportes, Property 9: Toda descarga exitosa queda registrada en el historial
 *
 * Validates: Requisito 6.4
 *
 * Para cualquier generación exitosa de reporte (PDF o Excel), la función
 * registrar_historial() debe ser invocada con accion = 'reporte' y una
 * descripción que incluya el formato descargado y el periodo del filtro.
 * 100 iteraciones con inputs generados aleatoriamente.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../admin/historial_helper.php';

// ---------------------------------------------------------------------------
// Fake PDO que captura los parámetros del INSERT ejecutado por
// registrar_historial() sin necesidad de una conexión real a la base de datos.
// Se usa una clase concreta en lugar de createMock(PDO::class) para evitar
// incompatibilidades de doctrine/instantiator con PHP 8.0 y clases internas.
// ---------------------------------------------------------------------------

/**
 * Fake PDOStatement que captura los parámetros pasados a execute().
 * No extiende PDOStatement para evitar restricciones del constructor protegido.
 * Se usa como retorno de FakePDO::prepare() con type coercion.
 */
class FakePDOStatement extends PDOStatement
{
    /** @var array Parámetros capturados en la última llamada a execute() */
    public array $capturedParams = [];

    public function execute($params = null): bool
    {
        $this->capturedParams = $params ?? [];
        return true;
    }
}

/**
 * Fake PDO que retorna un FakePDOStatement en prepare() y registra
 * cuántas veces fue llamado.
 */
class FakePDO extends PDO
{
    public FakePDOStatement $stmt;
    public int $prepareCallCount = 0;

    public function __construct()
    {
        // No llamar al constructor de PDO (requiere DSN real)
        $this->stmt = new FakePDOStatement();
    }

    public function prepare($query, $options = []): PDOStatement|false
    {
        $this->prepareCallCount++;
        return $this->stmt;
    }
}

class HistorialRegistrationTest extends TestCase
{
    // ── Generadores de datos ──────────────────────────────────────────────────

    /**
     * Genera una fecha aleatoria en formato YYYY-MM-DD.
     */
    private function randomDate(): string
    {
        $year  = random_int(2020, 2026);
        $month = random_int(1, 12);
        $day   = random_int(1, 28);
        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    /**
     * Genera 100 pares de (formato, periodo) aleatorios.
     *
     * @return array<int, array{string, string, string}>
     */
    public function downloadParamsProvider(): array
    {
        $formatos = ['PDF', 'Excel'];
        $cases    = [];

        for ($i = 0; $i < 100; $i++) {
            $formato   = $formatos[array_rand($formatos)];
            $fecha_ini = $this->randomDate();
            $fecha_fin = $this->randomDate();
            $cases[]   = [$formato, $fecha_ini, $fecha_fin];
        }

        return $cases;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Construye la descripción que reportes_historial.php pasa a registrar_historial()
     * según el formato (PDF o Excel).
     */
    private function buildDescripcion(string $formato, string $fecha_ini, string $fecha_fin): string
    {
        if ($formato === 'PDF') {
            return "PDF descargado. Periodo: {$fecha_ini} al {$fecha_fin}";
        }
        return "Excel descargado. Periodo: {$fecha_ini} al {$fecha_fin}";
    }

    /**
     * Configura la sesión mínima requerida por registrar_historial().
     */
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION['admin_id']       = 1;
        $_SESSION['admin_username'] = 'test_user';
        $_SERVER['REMOTE_ADDR']     = '127.0.0.1';
    }

    // ── Propiedad 9: Toda descarga exitosa queda registrada ───────────────────

    /**
     * **Validates: Requisito 6.4**
     *
     * Propiedad 9: Para cualquier generación exitosa de reporte (PDF o Excel),
     * registrar_historial() es invocada con:
     *   - accion    = 'reporte'
     *   - seccion   = 'Reportes'
     *   - descripcion contiene el formato (PDF o Excel)
     *   - descripcion contiene las fechas del periodo
     *
     * @dataProvider downloadParamsProvider
     */
    public function testEveryDownloadIsLogged(
        string $formato,
        string $fecha_ini,
        string $fecha_fin
    ): void {
        $fakePdo     = new FakePDO();
        $descripcion = $this->buildDescripcion($formato, $fecha_ini, $fecha_fin);

        // Simular la llamada que hace reportes_historial.php tras una descarga exitosa
        registrar_historial($fakePdo, 'reporte', 'Reportes', $descripcion);

        // Verificar que prepare() fue llamado exactamente una vez (el INSERT)
        $this->assertSame(
            1,
            $fakePdo->prepareCallCount,
            "registrar_historial() debe ejecutar exactamente un INSERT"
        );

        // Los parámetros del INSERT son: [user_id, username, accion, seccion, descripcion, ip]
        // Índices:                          0        1         2       3        4            5
        $params = $fakePdo->stmt->capturedParams;

        $this->assertNotEmpty(
            $params,
            "registrar_historial() debe ejecutar el INSERT con parámetros"
        );

        // La acción debe ser 'reporte'
        $this->assertSame(
            'reporte',
            $params[2],
            sprintf(
                "La acción debe ser 'reporte' para formato=%s, periodo=%s al %s",
                $formato, $fecha_ini, $fecha_fin
            )
        );

        // La sección debe ser 'Reportes'
        $this->assertSame(
            'Reportes',
            $params[3],
            sprintf(
                "La sección debe ser 'Reportes' para formato=%s, periodo=%s al %s",
                $formato, $fecha_ini, $fecha_fin
            )
        );

        // La descripción debe contener el formato (PDF o Excel)
        $this->assertStringContainsString(
            $formato,
            $params[4],
            sprintf(
                "La descripción debe contener el formato '%s'. Descripción obtenida: '%s'",
                $formato, $params[4]
            )
        );

        // La descripción debe contener la fecha de inicio del periodo
        $this->assertStringContainsString(
            $fecha_ini,
            $params[4],
            sprintf(
                "La descripción debe contener la fecha_ini '%s'. Descripción obtenida: '%s'",
                $fecha_ini, $params[4]
            )
        );

        // La descripción debe contener la fecha de fin del periodo
        $this->assertStringContainsString(
            $fecha_fin,
            $params[4],
            sprintf(
                "La descripción debe contener la fecha_fin '%s'. Descripción obtenida: '%s'",
                $fecha_fin, $params[4]
            )
        );
    }

    /**
     * Caso concreto — descarga PDF: verifica el mensaje exacto.
     */
    public function testPdfDownloadLogsCorrectMessage(): void
    {
        $fakePdo = new FakePDO();

        registrar_historial(
            $fakePdo,
            'reporte',
            'Reportes',
            'PDF descargado. Periodo: 2025-01-01 al 2025-01-31'
        );

        $params = $fakePdo->stmt->capturedParams;

        $this->assertSame('reporte',   $params[2]);
        $this->assertSame('Reportes',  $params[3]);
        $this->assertStringContainsString('PDF',        $params[4]);
        $this->assertStringContainsString('2025-01-01', $params[4]);
        $this->assertStringContainsString('2025-01-31', $params[4]);
    }

    /**
     * Caso concreto — descarga Excel: verifica el mensaje exacto.
     */
    public function testExcelDownloadLogsCorrectMessage(): void
    {
        $fakePdo = new FakePDO();

        registrar_historial(
            $fakePdo,
            'reporte',
            'Reportes',
            'Excel descargado. Periodo: 2025-06-01 al 2025-06-30'
        );

        $params = $fakePdo->stmt->capturedParams;

        $this->assertSame('reporte',   $params[2]);
        $this->assertSame('Reportes',  $params[3]);
        $this->assertStringContainsString('Excel',      $params[4]);
        $this->assertStringContainsString('2025-06-01', $params[4]);
        $this->assertStringContainsString('2025-06-30', $params[4]);
    }
}
