<?php
/**
 * Feature: dif-cms-php-migration, Property 11: round-trip de texto enriquecido en trámites
 *
 * Validates: Requirements 8.3, 8.4
 *
 * Para cualquier string HTML guardado en `tramites` para un slug, al recuperarlo
 * y renderizarlo el contenido es idéntico al guardado — 100 iteraciones.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Extrae la lógica de consulta de trámites en una función testable.
 * Consulta tramites WHERE slug = ? y retorna el registro completo.
 *
 * @param PDO    $pdo  Conexión PDO activa
 * @param string $slug Slug del trámite
 * @return array|null  Registro del trámite o null si no existe
 */
function get_tramite_by_slug(PDO $pdo, string $slug): ?array
{
    try {
        $stmt = $pdo->prepare('SELECT titulo, imagen_path, contenido FROM tramites WHERE slug = ?');
        $stmt->execute([$slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    } catch (\PDOException $e) {
        return null;
    }
}

class Property11TramiteRoundTripTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $this->pdo->exec('
            CREATE TABLE tramites (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                slug        VARCHAR(50)  NOT NULL UNIQUE,
                titulo      VARCHAR(200) NOT NULL,
                imagen_path VARCHAR(500) DEFAULT NULL,
                contenido   TEXT         DEFAULT NULL,
                updated_at  DATETIME     DEFAULT CURRENT_TIMESTAMP
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
    private function clearTramites(): void
    {
        $this->pdo->exec('DELETE FROM tramites');
    }

    /**
     * Genera un slug aleatorio.
     */
    private function randomSlug(): string
    {
        return 'SLUG_' . strtoupper(bin2hex(random_bytes(4)));
    }

    /**
     * Genera contenido HTML aleatorio con etiquetas variadas.
     */
    private function randomHtmlContent(): string
    {
        $tags = [
            '<p>%s</p>',
            '<h2>%s</h2>',
            '<ul><li>%s</li><li>%s</li></ul>',
            '<ol><li>%s</li></ol>',
            '<strong>%s</strong>',
            '<em>%s</em>',
            '<div class="info">%s</div>',
            '<table><tr><td>%s</td><td>%s</td></tr></table>',
            '<blockquote>%s</blockquote>',
            '<a href="https://example.com/%s">%s</a>',
        ];

        $parts = [];
        $numParts = random_int(1, 5);
        for ($i = 0; $i < $numParts; $i++) {
            $tag = $tags[array_rand($tags)];
            $text = bin2hex(random_bytes(random_int(4, 20)));
            $parts[] = sprintf($tag, $text, $text);
        }

        return implode("\n", $parts);
    }

    /**
     * Inserta un trámite en la DB.
     */
    private function insertTramite(string $slug, string $titulo, ?string $imagenPath, ?string $contenido): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO tramites (slug, titulo, imagen_path, contenido) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$slug, $titulo, $imagenPath, $contenido]);
    }

    // ── Property 11 ───────────────────────────────────────────────────────────

    /**
     * **Validates: Requirements 8.3, 8.4**
     *
     * Property 11: Para cualquier string HTML guardado en `tramites` para un slug,
     * al recuperarlo el contenido es idéntico al guardado — 100 iteraciones.
     */
    public function testProperty11_TramiteRichTextRoundTrip(): void
    {
        $failures       = 0;
        $failureDetails = [];

        for ($i = 0; $i < 100; $i++) {
            $this->clearTramites();

            $slug      = $this->randomSlug();
            $titulo    = 'Trámite ' . bin2hex(random_bytes(4));
            $imagen    = 'uploads/images/' . bin2hex(random_bytes(8)) . '.jpg';
            $contenido = $this->randomHtmlContent();

            $this->insertTramite($slug, $titulo, $imagen, $contenido);

            $result = get_tramite_by_slug($this->pdo, $slug);

            if ($result === null) {
                $failures++;
                $failureDetails[] = sprintf(
                    'Iteration %d: get_tramite_by_slug returned null for slug "%s"',
                    $i, $slug
                );
                continue;
            }

            if ($result['contenido'] !== $contenido) {
                $failures++;
                $failureDetails[] = sprintf(
                    'Iteration %d: content mismatch for slug "%s". Expected length %d, got %d',
                    $i, $slug, strlen($contenido), strlen($result['contenido'] ?? '')
                );
                continue;
            }

            if ($result['titulo'] !== $titulo) {
                $failures++;
                $failureDetails[] = sprintf(
                    'Iteration %d: titulo mismatch for slug "%s". Expected "%s", got "%s"',
                    $i, $slug, $titulo, $result['titulo']
                );
                continue;
            }

            if ($result['imagen_path'] !== $imagen) {
                $failures++;
                $failureDetails[] = sprintf(
                    'Iteration %d: imagen_path mismatch for slug "%s".',
                    $i, $slug
                );
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 11 failed in {$failures}/100 iterations.\n"
            . implode("\n", array_slice($failureDetails, 0, 5))
        );
    }

    // ── Pruebas de apoyo (edge cases) ─────────────────────────────────────────

    /**
     * **Validates: Requirements 8.4**
     *
     * Contenido vacío se almacena y recupera correctamente.
     */
    public function testEmptyContentRoundTrip(): void
    {
        $slug = 'EMPTY_TEST';
        $this->insertTramite($slug, 'Test Vacío', null, '');

        $result = get_tramite_by_slug($this->pdo, $slug);

        $this->assertNotNull($result);
        $this->assertSame('', $result['contenido']);
    }

    /**
     * **Validates: Requirements 8.3, 8.4**
     *
     * Contenido con caracteres especiales HTML se preserva exactamente.
     */
    public function testSpecialCharactersPreserved(): void
    {
        $slug      = 'SPECIAL_CHARS';
        $contenido = '<p>Atención: "comillas" & \'apóstrofes\' < > © ® ñ Ñ á é í ó ú ü</p>';

        $this->insertTramite($slug, 'Caracteres Especiales', null, $contenido);

        $result = get_tramite_by_slug($this->pdo, $slug);

        $this->assertNotNull($result);
        $this->assertSame($contenido, $result['contenido']);
    }

    /**
     * **Validates: Requirements 8.4**
     *
     * Contenido largo (>10KB) se almacena y recupera sin truncamiento.
     */
    public function testLongContentPreserved(): void
    {
        $slug      = 'LONG_CONTENT';
        $contenido = '<div>' . str_repeat('<p>' . bin2hex(random_bytes(50)) . '</p>', 100) . '</div>';

        $this->insertTramite($slug, 'Contenido Largo', null, $contenido);

        $result = get_tramite_by_slug($this->pdo, $slug);

        $this->assertNotNull($result);
        $this->assertSame($contenido, $result['contenido']);
        $this->assertGreaterThan(10000, strlen($result['contenido']));
    }

    /**
     * **Validates: Requirements 8.4**
     *
     * Slug inexistente retorna null.
     */
    public function testNonExistentSlugReturnsNull(): void
    {
        $result = get_tramite_by_slug($this->pdo, 'NO_EXISTE');
        $this->assertNull($result);
    }

    /**
     * **Validates: Requirements 8.3, 8.4**
     *
     * Contenido NULL se almacena y recupera como null.
     */
    public function testNullContentRoundTrip(): void
    {
        $slug = 'NULL_CONTENT';
        $this->insertTramite($slug, 'Sin Contenido', null, null);

        $result = get_tramite_by_slug($this->pdo, $slug);

        $this->assertNotNull($result);
        $this->assertNull($result['contenido']);
    }
}
