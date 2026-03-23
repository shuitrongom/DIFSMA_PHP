<?php
/**
 * Feature: dif-cms-php-migration, Property 8: round-trip de contenido de sliders
 *
 * Validates: Requirements 2.2, 2.5, 3.2, 3.5
 *
 * Para cualquier N imágenes insertadas en `slider_principal` (activo=1),
 * get_slider_images() retorna exactamente N rutas en el orden correcto — 100 iteraciones.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Extrae la lógica de consulta del slider de index.php en una función testable.
 * Consulta slider_principal WHERE activo=1 ORDER BY orden ASC y retorna array de rutas.
 *
 * @param PDO    $pdo   Conexión PDO activa
 * @param string $table Nombre de la tabla (por defecto 'slider_principal')
 * @return array        Array de strings con las rutas de imagen, en orden
 */
function get_slider_images(PDO $pdo, string $table = 'slider_principal'): array
{
    try {
        $stmt = $pdo->prepare(
            "SELECT imagen_path FROM {$table} WHERE activo = 1 ORDER BY orden ASC"
        );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn(array $row): string => $row['imagen_path'], $rows);
    } catch (\PDOException $e) {
        return [];
    }
}

class Property8SliderRoundTripTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $this->pdo->exec('
            CREATE TABLE slider_principal (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                imagen_path VARCHAR(500) NOT NULL,
                orden       INT NOT NULL DEFAULT 0,
                activo      TINYINT(1) NOT NULL DEFAULT 1,
                created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
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
    private function clearSlider(): void
    {
        $this->pdo->exec('DELETE FROM slider_principal');
    }

    /**
     * Genera una ruta de imagen aleatoria realista.
     */
    private function randomImagePath(): string
    {
        return 'uploads/images/' . bin2hex(random_bytes(8)) . '.jpg';
    }

    /**
     * Inserta N imágenes activas en slider_principal con orden secuencial.
     * Retorna el array de rutas en el orden insertado.
     *
     * @param int $n Número de imágenes a insertar
     * @return string[] Rutas en orden de inserción (orden 0..n-1)
     */
    private function insertSliderImages(int $n): array
    {
        $paths = [];
        $stmt  = $this->pdo->prepare(
            'INSERT INTO slider_principal (imagen_path, orden, activo) VALUES (?, ?, 1)'
        );
        for ($i = 0; $i < $n; $i++) {
            $path    = $this->randomImagePath();
            $paths[] = $path;
            $stmt->execute([$path, $i]);
        }
        return $paths;
    }

    // ── Property 8 ────────────────────────────────────────────────────────────

    /**
     * **Validates: Requirements 2.2, 2.5, 3.2, 3.5**
     *
     * Property 8: Para cualquier N imágenes (1–10) insertadas en slider_principal
     * con activo=1, get_slider_images() retorna exactamente N rutas en el orden
     * correcto — 100 iteraciones.
     */
    public function testProperty8_SliderRoundTrip(): void
    {
        $failures      = 0;
        $failureDetails = [];

        for ($i = 0; $i < 100; $i++) {
            $this->clearSlider();

            $n             = random_int(1, 10);
            $expectedPaths = $this->insertSliderImages($n);

            $result = get_slider_images($this->pdo);

            // Verificar cantidad exacta
            if (count($result) !== $n) {
                $failures++;
                $failureDetails[] = "Iteration {$i}: expected {$n} images, got " . count($result);
                continue;
            }

            // Verificar rutas en orden correcto
            for ($j = 0; $j < $n; $j++) {
                if ($result[$j] !== $expectedPaths[$j]) {
                    $failures++;
                    $failureDetails[] = "Iteration {$i}: image[{$j}] expected '{$expectedPaths[$j]}', got '{$result[$j]}'";
                    break;
                }
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 8 failed in {$failures}/100 iterations.\n" . implode("\n", array_slice($failureDetails, 0, 5))
        );
    }

    // ── Pruebas de apoyo ──────────────────────────────────────────────────────

    /**
     * **Validates: Requirements 2.5**
     *
     * Las imágenes inactivas (activo=0) no aparecen en el resultado.
     */
    public function testInactiveImagesAreExcluded(): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO slider_principal (imagen_path, orden, activo) VALUES (?, ?, ?)'
        );
        $stmt->execute(['uploads/images/active.jpg', 0, 1]);
        $stmt->execute(['uploads/images/inactive.jpg', 1, 0]);

        $result = get_slider_images($this->pdo);

        $this->assertCount(1, $result, 'Only active images should be returned.');
        $this->assertSame('uploads/images/active.jpg', $result[0]);
    }

    /**
     * **Validates: Requirements 2.5**
     *
     * Cuando no hay imágenes activas, retorna array vacío.
     */
    public function testEmptySliderReturnsEmptyArray(): void
    {
        $result = get_slider_images($this->pdo);
        $this->assertSame([], $result, 'Empty slider_principal should return an empty array.');
    }

    /**
     * **Validates: Requirements 2.5**
     *
     * Las imágenes se retornan ordenadas por el campo `orden` ASC.
     */
    public function testImagesAreReturnedInOrderAsc(): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO slider_principal (imagen_path, orden, activo) VALUES (?, ?, 1)'
        );
        $stmt->execute(['uploads/images/third.jpg', 2]);
        $stmt->execute(['uploads/images/first.jpg', 0]);
        $stmt->execute(['uploads/images/second.jpg', 1]);

        $result = get_slider_images($this->pdo);

        $this->assertSame(
            ['uploads/images/first.jpg', 'uploads/images/second.jpg', 'uploads/images/third.jpg'],
            $result,
            'Images must be returned ordered by `orden` ASC.'
        );
    }

    /**
     * **Validates: Requirements 2.2**
     *
     * La ruta almacenada se recupera exactamente como fue insertada (sin modificaciones).
     */
    public function testImagePathIsPreservedExactly(): void
    {
        $path = 'uploads/images/abc123def456.jpg';
        $stmt = $this->pdo->prepare(
            'INSERT INTO slider_principal (imagen_path, orden, activo) VALUES (?, 0, 1)'
        );
        $stmt->execute([$path]);

        $result = get_slider_images($this->pdo);

        $this->assertCount(1, $result);
        $this->assertSame($path, $result[0], 'The stored image path must be returned unchanged.');
    }

    /**
     * **Validates: Requirements 2.2, 2.5**
     *
     * Tabla alternativa (slider_comunica) funciona con el mismo parámetro $table.
     */
    public function testAlternativeTableParameterWorks(): void
    {
        $this->pdo->exec('
            CREATE TABLE slider_comunica (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                imagen_path VARCHAR(500) NOT NULL,
                orden       INT NOT NULL DEFAULT 0,
                activo      TINYINT(1) NOT NULL DEFAULT 1,
                created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $stmt = $this->pdo->prepare(
            'INSERT INTO slider_comunica (imagen_path, orden, activo) VALUES (?, 0, 1)'
        );
        $stmt->execute(['uploads/images/comunica.jpg']);

        $result = get_slider_images($this->pdo, 'slider_comunica');

        $this->assertCount(1, $result);
        $this->assertSame('uploads/images/comunica.jpg', $result[0]);
    }
}
