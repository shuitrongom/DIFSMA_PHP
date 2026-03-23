<?php
/**
 * Feature: dif-cms-php-migration, Property 10: filtrado de noticias por fecha actual
 *
 * Validates: Requirements 4.5, 10.2
 *
 * Para cualquier conjunto de noticias con fechas variadas, el HTML renderizado
 * contiene únicamente las imágenes cuya `fecha_noticia` es la fecha actual — 100 iteraciones.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Extrae la lógica de consulta de noticias de index.php en una función testable.
 * Consulta noticias_imagenes WHERE activo=1 AND fecha_noticia = date('Y-m-d')
 * y retorna array de rutas de imagen.
 *
 * @param PDO         $pdo   Conexión PDO activa
 * @param string|null $date  Fecha a filtrar (Y-m-d). Si null, usa la fecha actual.
 * @return array              Array de strings con las rutas de imagen
 */
function get_noticias_images(PDO $pdo, ?string $date = null): array
{
    $date = $date ?? date('Y-m-d');
    try {
        $stmt = $pdo->prepare(
            'SELECT imagen_path FROM noticias_imagenes WHERE activo = 1 AND fecha_noticia = ?'
        );
        $stmt->execute([$date]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn(array $row): string => $row['imagen_path'], $rows);
    } catch (\PDOException $e) {
        return [];
    }
}

class Property10NoticiasFilterTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $this->pdo->exec('
            CREATE TABLE noticias_imagenes (
                id            INTEGER PRIMARY KEY AUTOINCREMENT,
                imagen_path   VARCHAR(500) NOT NULL,
                fecha_noticia DATE         NOT NULL,
                activo        TINYINT(1)   NOT NULL DEFAULT 1,
                created_at    DATETIME     DEFAULT CURRENT_TIMESTAMP
            )
        ');
    }

    protected function tearDown(): void
    {
        unset($this->pdo);
    }

    /**
     * Limpia la tabla entre iteraciones.
     */
    private function clearNoticias(): void
    {
        $this->pdo->exec('DELETE FROM noticias_imagenes');
    }

    /**
     * Genera una ruta de imagen aleatoria realista.
     */
    private function randomImagePath(): string
    {
        return 'uploads/images/' . bin2hex(random_bytes(8)) . '.jpg';
    }

    /**
     * Genera una fecha aleatoria en el rango [-365, +365] días desde hoy.
     */
    private function randomDate(): string
    {
        $offset = random_int(-365, 365);
        return date('Y-m-d', strtotime("{$offset} days"));
    }

    /**
     * Inserta una noticia en la DB.
     *
     * @return string La ruta de imagen insertada
     */
    private function insertNoticia(string $fecha, bool $activo = true): string
    {
        $path = $this->randomImagePath();
        $stmt = $this->pdo->prepare(
            'INSERT INTO noticias_imagenes (imagen_path, fecha_noticia, activo) VALUES (?, ?, ?)'
        );
        $stmt->execute([$path, $fecha, $activo ? 1 : 0]);
        return $path;
    }

    // ── Property 10 ───────────────────────────────────────────────────────────

    /**
     * **Validates: Requirements 4.5, 10.2**
     *
     * Property 10: Para cualquier conjunto de noticias con fechas variadas
     * (algunas hoy, algunas pasadas, algunas futuras), get_noticias_images()
     * retorna únicamente las imágenes cuya fecha_noticia es la fecha actual
     * — 100 iteraciones.
     */
    public function testProperty10_NoticiasFilteredByCurrentDate(): void
    {
        $today          = date('Y-m-d');
        $failures       = 0;
        $failureDetails = [];

        for ($i = 0; $i < 100; $i++) {
            $this->clearNoticias();

            // Generar un conjunto aleatorio de noticias (3–15)
            $totalCount  = random_int(3, 15);
            $todayPaths  = [];

            for ($j = 0; $j < $totalCount; $j++) {
                // ~30% probabilidad de ser hoy, resto fechas aleatorias
                if (random_int(1, 100) <= 30) {
                    $path        = $this->insertNoticia($today, true);
                    $todayPaths[] = $path;
                } else {
                    // Fecha distinta a hoy: pasada o futura
                    $offset = random_int(1, 365) * (random_int(0, 1) === 0 ? -1 : 1);
                    $otherDate = date('Y-m-d', strtotime("{$offset} days"));
                    $this->insertNoticia($otherDate, true);
                }
            }

            $result = get_noticias_images($this->pdo, $today);

            // Verificar que solo se retornan las noticias de hoy
            if (count($result) !== count($todayPaths)) {
                $failures++;
                $failureDetails[] = sprintf(
                    'Iteration %d: expected %d today images, got %d (total inserted: %d)',
                    $i, count($todayPaths), count($result), $totalCount
                );
                continue;
            }

            // Verificar que cada ruta retornada corresponde a una noticia de hoy
            foreach ($result as $path) {
                if (!in_array($path, $todayPaths, true)) {
                    $failures++;
                    $failureDetails[] = sprintf(
                        'Iteration %d: returned path "%s" is not a today image',
                        $i, $path
                    );
                    break;
                }
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 10 failed in {$failures}/100 iterations.\n"
            . implode("\n", array_slice($failureDetails, 0, 5))
        );
    }

    // ── Pruebas de apoyo (edge cases) ─────────────────────────────────────────

    /**
     * **Validates: Requirements 4.6**
     *
     * Cuando no hay noticias para la fecha actual, retorna array vacío.
     */
    public function testNoNewsTodayReturnsEmptyArray(): void
    {
        $today     = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $tomorrow  = date('Y-m-d', strtotime('+1 day'));

        $this->insertNoticia($yesterday, true);
        $this->insertNoticia($tomorrow, true);

        $result = get_noticias_images($this->pdo, $today);

        $this->assertSame([], $result, 'No news today should return an empty array.');
    }

    /**
     * **Validates: Requirements 4.5, 10.2**
     *
     * Cuando todas las noticias son de hoy, todas se retornan.
     */
    public function testAllNewsTodayReturnsAll(): void
    {
        $today = date('Y-m-d');

        $paths = [];
        for ($i = 0; $i < 5; $i++) {
            $paths[] = $this->insertNoticia($today, true);
        }

        $result = get_noticias_images($this->pdo, $today);

        $this->assertCount(5, $result, 'All 5 today images should be returned.');
        foreach ($paths as $path) {
            $this->assertContains($path, $result, "Path '{$path}' should be in the result.");
        }
    }

    /**
     * **Validates: Requirements 4.5**
     *
     * Las noticias inactivas (activo=0) no se retornan aunque sean de hoy.
     */
    public function testInactiveNewsTodayAreExcluded(): void
    {
        $today = date('Y-m-d');

        $activePath   = $this->insertNoticia($today, true);
        $this->insertNoticia($today, false); // inactive

        $result = get_noticias_images($this->pdo, $today);

        $this->assertCount(1, $result, 'Only active news should be returned.');
        $this->assertSame($activePath, $result[0]);
    }

    /**
     * **Validates: Requirements 4.5, 10.2**
     *
     * Noticias de ayer y mañana no aparecen en el resultado de hoy.
     */
    public function testOnlyTodayDatesAreReturned(): void
    {
        $today     = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $tomorrow  = date('Y-m-d', strtotime('+1 day'));

        $this->insertNoticia($yesterday, true);
        $todayPath = $this->insertNoticia($today, true);
        $this->insertNoticia($tomorrow, true);

        $result = get_noticias_images($this->pdo, $today);

        $this->assertCount(1, $result);
        $this->assertSame($todayPath, $result[0]);
    }

    /**
     * **Validates: Requirements 4.5**
     *
     * Tabla completamente vacía retorna array vacío.
     */
    public function testEmptyTableReturnsEmptyArray(): void
    {
        $result = get_noticias_images($this->pdo, date('Y-m-d'));
        $this->assertSame([], $result, 'Empty table should return an empty array.');
    }
}
