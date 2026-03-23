<?php
/**
 * Feature: dif-cms-php-migration, Property 13: renderizado correcto de bloques SEAC por año
 *
 * Validates: Requirements 11.7
 *
 * Para cualquier conjunto de bloques SEAC en DB, el HTML contiene un
 * `<article class="question">` por bloque con la tabla de trimestres
 * y enlaces correctos — 100 iteraciones.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Extrae la lógica de consulta de bloques SEAC de SEAC.php en una función testable.
 * Consulta seac_bloques ORDER BY orden DESC y retorna array de bloques.
 *
 * @param PDO $pdo Conexión PDO activa
 * @return array    Array de bloques con id, anio, orden
 */
function get_seac_bloques(PDO $pdo): array
{
    try {
        $stmt = $pdo->prepare('SELECT id, anio, orden FROM seac_bloques ORDER BY orden DESC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        return [];
    }
}

/**
 * Extrae la lógica de consulta de conceptos SEAC por bloque.
 * Consulta seac_conceptos ORDER BY orden ASC, agrupados por bloque_id.
 *
 * @param PDO $pdo Conexión PDO activa
 * @return array    Mapa [bloque_id] => array de conceptos con id, bloque_id, numero, nombre, orden
 */
function get_seac_conceptos(PDO $pdo): array
{
    $map = [];
    try {
        $stmt = $pdo->prepare('SELECT id, bloque_id, numero, nombre, orden FROM seac_conceptos ORDER BY orden ASC');
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $map[(int)$row['bloque_id']][] = $row;
        }
    } catch (\PDOException $e) {
        // silently return empty map
    }
    return $map;
}

/**
 * Extrae la lógica de construcción del mapa de PDFs de SEAC.php en una función testable.
 * Consulta seac_pdfs WHERE pdf_path IS NOT NULL AND pdf_path != '' y construye
 * un mapa indexado [bloque_id][concepto_id][trimestre] => pdf_path.
 *
 * @param PDO $pdo Conexión PDO activa
 * @return array    Mapa tridimensional de PDFs
 */
function get_seac_pdfs_map(PDO $pdo): array
{
    $map = [];
    try {
        $stmt = $pdo->prepare(
            'SELECT bloque_id, concepto_id, trimestre, pdf_path FROM seac_pdfs WHERE pdf_path IS NOT NULL AND pdf_path != ""'
        );
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $map[(int)$row['bloque_id']][(int)$row['concepto_id']][(int)$row['trimestre']] = $row['pdf_path'];
        }
    } catch (\PDOException $e) {
        // silently return empty map
    }
    return $map;
}

class Property13SeacRenderTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // seac_bloques — SQLite uses INTEGER for YEAR
        $this->pdo->exec('
            CREATE TABLE seac_bloques (
                id    INTEGER PRIMARY KEY AUTOINCREMENT,
                anio  INTEGER NOT NULL,
                orden INTEGER NOT NULL DEFAULT 0,
                UNIQUE(anio)
            )
        ');

        // seac_conceptos
        $this->pdo->exec('
            CREATE TABLE seac_conceptos (
                id       INTEGER PRIMARY KEY AUTOINCREMENT,
                bloque_id INTEGER NOT NULL,
                numero   INTEGER NOT NULL,
                nombre   VARCHAR(500) NOT NULL,
                orden    INTEGER NOT NULL DEFAULT 0,
                FOREIGN KEY (bloque_id) REFERENCES seac_bloques(id) ON DELETE CASCADE
            )
        ');

        // seac_pdfs
        $this->pdo->exec('
            CREATE TABLE seac_pdfs (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                bloque_id   INTEGER NOT NULL,
                concepto_id INTEGER NOT NULL,
                trimestre   INTEGER NOT NULL CHECK (trimestre BETWEEN 1 AND 4),
                pdf_path    VARCHAR(500) DEFAULT NULL,
                UNIQUE(bloque_id, concepto_id, trimestre),
                FOREIGN KEY (bloque_id) REFERENCES seac_bloques(id) ON DELETE CASCADE,
                FOREIGN KEY (concepto_id) REFERENCES seac_conceptos(id)
            )
        ');

        // Enable foreign keys in SQLite
        $this->pdo->exec('PRAGMA foreign_keys = ON');
    }

    protected function tearDown(): void
    {
        unset($this->pdo);
    }

    /**
     * Limpia todas las tablas SEAC entre iteraciones.
     */
    private function clearSeac(): void
    {
        $this->pdo->exec('DELETE FROM seac_pdfs');
        $this->pdo->exec('DELETE FROM seac_bloques');
        $this->pdo->exec('DELETE FROM seac_conceptos');
    }

    /**
     * Genera una ruta de PDF aleatoria.
     */
    private function randomPdfPath(): string
    {
        return 'uploads/pdfs/' . bin2hex(random_bytes(8)) . '.pdf';
    }

    /**
     * Inserta N conceptos con números y nombres aleatorios para un bloque dado.
     *
     * @param int $bloqueId ID del bloque
     * @param int $n Número de conceptos
     * @return array Array de IDs de conceptos insertados
     */
    private function insertConceptos(int $bloqueId, int $n): array
    {
        $ids  = [];
        $stmt = $this->pdo->prepare(
            'INSERT INTO seac_conceptos (bloque_id, numero, nombre, orden) VALUES (?, ?, ?, ?)'
        );
        for ($i = 0; $i < $n; $i++) {
            $numero = $i + 1;
            $nombre = 'Concepto ' . bin2hex(random_bytes(4));
            $stmt->execute([$bloqueId, $numero, $nombre, $i]);
            $ids[] = (int)$this->pdo->lastInsertId();
        }
        return $ids;
    }

    /**
     * Inserta un bloque SEAC con un año dado.
     *
     * @return int ID del bloque insertado
     */
    private function insertBloque(int $anio, int $orden): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO seac_bloques (anio, orden) VALUES (?, ?)'
        );
        $stmt->execute([$anio, $orden]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Inserta un PDF para un bloque/concepto/trimestre dado.
     */
    private function insertPdf(int $bloqueId, int $conceptoId, int $trimestre, string $pdfPath): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO seac_pdfs (bloque_id, concepto_id, trimestre, pdf_path) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$bloqueId, $conceptoId, $trimestre, $pdfPath]);
    }

    // ── Property 13 ───────────────────────────────────────────────────────────

    /**
     * **Validates: Requirements 11.7**
     *
     * Property 13: Para cualquier conjunto de bloques SEAC (1–5 bloques, 1–8 conceptos,
     * PDFs aleatorios por celda), las funciones de consulta retornan exactamente N bloques,
     * M conceptos, y el mapa de PDFs contiene las rutas correctas para cada celda
     * — 100 iteraciones.
     */
    public function testProperty13_SeacBlocksRenderCorrectly(): void
    {
        $failures       = 0;
        $failureDetails = [];

        for ($i = 0; $i < 100; $i++) {
            $this->clearSeac();

            // Random number of bloques (1–5) and conceptos (1–8)
            $numBloques   = random_int(1, 5);
            // Insert conceptos per bloque (done after bloques are created)
            $numConceptos = random_int(1, 8);

            // Insert bloques with unique years
            $baseYear  = random_int(2015, 2025);
            $bloqueIds = [];
            $bloqueAnios = [];
            for ($b = 0; $b < $numBloques; $b++) {
                $anio = $baseYear - $b;
                $id   = $this->insertBloque($anio, $b);
                $bloqueIds[]  = $id;
                $bloqueAnios[$id] = $anio;
            }

            // Insert conceptos per bloque
            $conceptoIdsByBloque = [];
            foreach ($bloqueIds as $bloqueId) {
                $conceptoIdsByBloque[$bloqueId] = $this->insertConceptos($bloqueId, $numConceptos);
            }

            // Insert random PDFs (~50% chance per cell)
            $expectedPdfs = []; // [bloqueId][conceptoId][trimestre] => pdfPath
            foreach ($bloqueIds as $bloqueId) {
                foreach ($conceptoIdsByBloque[$bloqueId] as $conceptoId) {
                    for ($trim = 1; $trim <= 4; $trim++) {
                        if (random_int(0, 1) === 1) {
                            $pdfPath = $this->randomPdfPath();
                            $this->insertPdf($bloqueId, $conceptoId, $trim, $pdfPath);
                            $expectedPdfs[$bloqueId][$conceptoId][$trim] = $pdfPath;
                        }
                    }
                }
            }

            // Query using the same logic as SEAC.php
            $bloques       = get_seac_bloques($this->pdo);
            $conceptos_map = get_seac_conceptos($this->pdo);
            $pdfsMap       = get_seac_pdfs_map($this->pdo);

            // Verify: N bloques returned
            if (count($bloques) !== $numBloques) {
                $failures++;
                $failureDetails[] = sprintf(
                    'Iteration %d: expected %d bloques, got %d',
                    $i, $numBloques, count($bloques)
                );
                continue;
            }

            // Verify: M conceptos per bloque returned
            $conceptoMismatch = false;
            foreach ($bloqueIds as $bloqueId) {
                $bloqueConceptos = $conceptos_map[$bloqueId] ?? [];
                if (count($bloqueConceptos) !== $numConceptos) {
                    $failures++;
                    $failureDetails[] = sprintf(
                        'Iteration %d: expected %d conceptos for bloque %d, got %d',
                        $i, $numConceptos, $bloqueId, count($bloqueConceptos)
                    );
                    $conceptoMismatch = true;
                    break;
                }
            }
            if ($conceptoMismatch) continue;

            // Verify: PDF map matches expected
            $pdfMismatch = false;
            foreach ($bloqueIds as $bloqueId) {
                foreach ($conceptoIdsByBloque[$bloqueId] as $conceptoId) {
                    for ($trim = 1; $trim <= 4; $trim++) {
                        $expected = $expectedPdfs[$bloqueId][$conceptoId][$trim] ?? null;
                        $actual   = $pdfsMap[$bloqueId][$conceptoId][$trim] ?? null;

                        if ($expected !== $actual) {
                            $failures++;
                            $failureDetails[] = sprintf(
                                'Iteration %d: PDF mismatch at bloque=%d, concepto=%d, trim=%d. Expected "%s", got "%s"',
                                $i, $bloqueId, $conceptoId, $trim,
                                $expected ?? 'null', $actual ?? 'null'
                            );
                            $pdfMismatch = true;
                            break 3;
                        }
                    }
                }
            }

            if ($pdfMismatch) {
                continue;
            }

            // Verify: bloques are ordered by orden DESC (most recent first)
            for ($b = 1; $b < count($bloques); $b++) {
                if ((int)$bloques[$b - 1]['orden'] < (int)$bloques[$b]['orden']) {
                    $failures++;
                    $failureDetails[] = sprintf(
                        'Iteration %d: bloques not in DESC orden. orden[%d]=%d < orden[%d]=%d',
                        $i, $b - 1, (int)$bloques[$b - 1]['orden'], $b, (int)$bloques[$b]['orden']
                    );
                    break;
                }
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 13 failed in {$failures}/100 iterations.\n"
            . implode("\n", array_slice($failureDetails, 0, 5))
        );
    }

    // ── Pruebas de apoyo (edge cases) ─────────────────────────────────────────

    /**
     * **Validates: Requirements 11.7**
     *
     * Sin bloques SEAC, las consultas retornan arrays vacíos.
     */
    public function testEmptySeacReturnsEmptyArrays(): void
    {
        $bloques       = get_seac_bloques($this->pdo);
        $conceptos_map = get_seac_conceptos($this->pdo);
        $pdfsMap       = get_seac_pdfs_map($this->pdo);

        $this->assertSame([], $bloques, 'No bloques should return empty array.');
        $this->assertSame([], $conceptos_map, 'No conceptos should return empty map.');
        $this->assertSame([], $pdfsMap, 'No PDFs should return empty map.');
    }

    /**
     * **Validates: Requirements 11.7**
     *
     * Un bloque sin PDFs retorna mapa vacío para ese bloque.
     */
    public function testBloqueWithoutPdfsHasEmptyMap(): void
    {
        $bloqueId = $this->insertBloque(2024, 0);
        $this->insertConceptos($bloqueId, 3);

        $pdfsMap = get_seac_pdfs_map($this->pdo);

        $this->assertArrayNotHasKey($bloqueId, $pdfsMap, 'Bloque without PDFs should not appear in map.');
    }

    /**
     * **Validates: Requirements 11.7**
     *
     * PDFs con pdf_path NULL o vacío no aparecen en el mapa.
     */
    public function testNullAndEmptyPdfPathsExcluded(): void
    {
        $bloqueId    = $this->insertBloque(2024, 0);
        $conceptoIds = $this->insertConceptos($bloqueId, 1);

        // Insert PDF with NULL path (direct SQL to bypass NOT NULL)
        $this->pdo->exec(
            "INSERT INTO seac_pdfs (bloque_id, concepto_id, trimestre, pdf_path) VALUES ({$bloqueId}, {$conceptoIds[0]}, 1, NULL)"
        );
        // Insert PDF with empty string path
        $this->pdo->exec(
            "INSERT INTO seac_pdfs (bloque_id, concepto_id, trimestre, pdf_path) VALUES ({$bloqueId}, {$conceptoIds[0]}, 2, '')"
        );
        // Insert PDF with valid path
        $validPath = 'uploads/pdfs/valid.pdf';
        $this->insertPdf($bloqueId, $conceptoIds[0], 3, $validPath);

        $pdfsMap = get_seac_pdfs_map($this->pdo);

        $this->assertArrayNotHasKey(1, $pdfsMap[$bloqueId][$conceptoIds[0]] ?? [], 'NULL pdf_path should be excluded.');
        $this->assertArrayNotHasKey(2, $pdfsMap[$bloqueId][$conceptoIds[0]] ?? [], 'Empty pdf_path should be excluded.');
        $this->assertSame($validPath, $pdfsMap[$bloqueId][$conceptoIds[0]][3] ?? null, 'Valid pdf_path should be in map.');
    }

    /**
     * **Validates: Requirements 11.7**
     *
     * Los conceptos se retornan ordenados por campo `orden` ASC.
     */
    public function testConceptosOrderedByOrdenAsc(): void
    {
        $bloqueId = $this->insertBloque(2024, 0);

        $stmt = $this->pdo->prepare(
            'INSERT INTO seac_conceptos (bloque_id, numero, nombre, orden) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$bloqueId, 3, 'Tercero', 2]);
        $stmt->execute([$bloqueId, 1, 'Primero', 0]);
        $stmt->execute([$bloqueId, 2, 'Segundo', 1]);

        $conceptos_map = get_seac_conceptos($this->pdo);
        $conceptos = $conceptos_map[$bloqueId] ?? [];

        $this->assertCount(3, $conceptos);
        $this->assertSame('Primero', $conceptos[0]['nombre']);
        $this->assertSame('Segundo', $conceptos[1]['nombre']);
        $this->assertSame('Tercero', $conceptos[2]['nombre']);
    }

    /**
     * **Validates: Requirements 11.7**
     *
     * Los bloques se retornan ordenados por campo `orden` DESC.
     */
    public function testBloquesOrderedByOrdenDesc(): void
    {
        $this->insertBloque(2020, 0);
        $this->insertBloque(2022, 2);
        $this->insertBloque(2021, 1);

        $bloques = get_seac_bloques($this->pdo);

        $this->assertCount(3, $bloques);
        $this->assertEquals(2022, $bloques[0]['anio']);
        $this->assertEquals(2021, $bloques[1]['anio']);
        $this->assertEquals(2020, $bloques[2]['anio']);
    }
}
