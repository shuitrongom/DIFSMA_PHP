<?php
/**
 * Feature: dif-cms-php-migration, Property 15: sentencias preparadas previenen inyección SQL
 *
 * Validates: Requirements 15.1
 *
 * Para cualquier string con caracteres SQL especiales como input, la consulta PDO
 * se ejecuta correctamente sin alterar su estructura lógica ni retornar datos
 * no autorizados — 100 iteraciones.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class Property15SqlInjectionTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        // Create tables used in the CMS
        $this->pdo->exec('
            CREATE TABLE admin (
                id       INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL
            )
        ');

        $this->pdo->exec('
            CREATE TABLE direcciones (
                id           INTEGER PRIMARY KEY AUTOINCREMENT,
                departamento VARCHAR(200) NOT NULL,
                nombre       VARCHAR(200) NOT NULL,
                cargo        VARCHAR(300) NOT NULL,
                imagen_path  VARCHAR(500) DEFAULT NULL,
                orden        INT NOT NULL DEFAULT 0
            )
        ');

        $this->pdo->exec('
            CREATE TABLE noticias_imagenes (
                id            INTEGER PRIMARY KEY AUTOINCREMENT,
                imagen_path   VARCHAR(500) NOT NULL,
                fecha_noticia DATE NOT NULL,
                activo        TINYINT(1) NOT NULL DEFAULT 1,
                created_at    DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $this->pdo->exec('
            CREATE TABLE tramites (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                slug        VARCHAR(50) NOT NULL UNIQUE,
                titulo      VARCHAR(200) NOT NULL,
                imagen_path VARCHAR(500) DEFAULT NULL,
                contenido   TEXT DEFAULT NULL,
                updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        // Seed with known data for verification
        $hash = password_hash('secret123', PASSWORD_BCRYPT);
        $this->pdo->prepare('INSERT INTO admin (username, password) VALUES (?, ?)')
            ->execute(['admin', $hash]);

        $this->pdo->prepare('INSERT INTO direcciones (departamento, nombre, cargo, orden) VALUES (?, ?, ?, ?)')
            ->execute(['Dirección General', 'Juan Pérez', 'Director General', 1]);

        $this->pdo->prepare('INSERT INTO tramites (slug, titulo, contenido) VALUES (?, ?, ?)')
            ->execute(['PMPNNA', 'Procuraduría Municipal', '<p>Contenido original</p>']);
    }

    protected function tearDown(): void
    {
        unset($this->pdo);
    }

    /**
     * Generate a random SQL injection payload.
     */
    private function randomSqlPayload(): string
    {
        $payloads = [
            "'; DROP TABLE admin;--",
            "' OR '1'='1",
            "' OR '1'='1'--",
            "' OR '1'='1'/*",
            "\" OR \"1\"=\"1\"",
            "' UNION SELECT * FROM admin--",
            "' UNION SELECT id, username, password FROM admin--",
            "'; DELETE FROM direcciones;--",
            "'; UPDATE admin SET password='hacked';--",
            "1; DROP TABLE tramites",
            "' AND 1=0 UNION SELECT NULL, username, password FROM admin--",
            "admin'--",
            "' OR 1=1#",
            "' OR ''='",
            "'; INSERT INTO admin (username, password) VALUES ('hacker','pass');--",
            "1' OR '1' = '1",
            "' OR 'x'='x",
            "'; EXEC xp_cmdshell('dir');--",
            "' HAVING 1=1--",
            "' GROUP BY id HAVING 1=1--",
            "' ORDER BY 1--",
            "SLEEP(5)--",
            "' WAITFOR DELAY '0:0:5'--",
            "' AND SUBSTRING(username,1,1)='a'--",
            "'; TRUNCATE TABLE admin;--",
            "' UNION ALL SELECT NULL,NULL,NULL--",
            "1 OR 1=1",
            "' OR 'a'='a",
            "admin' /*",
            "') OR ('1'='1",
            "')) OR (('1'='1",
            "' AND '1'='2' UNION SELECT 1,2,3--",
        ];

        // Mix predefined payloads with random variations
        if (random_int(0, 2) === 0) {
            // Generate a random variation
            $base = $payloads[array_rand($payloads)];
            $suffix = bin2hex(random_bytes(random_int(1, 8)));
            return $base . $suffix;
        }

        return $payloads[array_rand($payloads)];
    }

    // ── Property 15: INSERT with SQL injection payloads ───────────────────────

    /**
     * **Validates: Requirements 15.1**
     *
     * Property 15 (INSERT): For any string containing SQL injection characters,
     * PDO prepared INSERT executes without error and stores the exact payload
     * as data — 100 iterations.
     */
    public function testProperty15_InsertWithSqlPayloads(): void
    {
        $failures       = 0;
        $failureDetails = [];

        for ($i = 0; $i < 100; $i++) {
            $payload = $this->randomSqlPayload();

            try {
                // INSERT using prepared statement
                $stmt = $this->pdo->prepare(
                    'INSERT INTO noticias_imagenes (imagen_path, fecha_noticia, activo) VALUES (?, ?, 1)'
                );
                $stmt->execute([$payload, '2025-01-15']);
                $id = (int) $this->pdo->lastInsertId();

                // Verify data stored exactly as provided
                $stmt = $this->pdo->prepare('SELECT imagen_path FROM noticias_imagenes WHERE id = ?');
                $stmt->execute([$id]);
                $row = $stmt->fetch();

                if (!$row) {
                    $failures++;
                    $failureDetails[] = "Iteration {$i}: row not found after INSERT (payload: {$payload})";
                    continue;
                }

                if ($row['imagen_path'] !== $payload) {
                    $failures++;
                    $failureDetails[] = "Iteration {$i}: stored value differs from input. Expected: {$payload}, Got: {$row['imagen_path']}";
                    continue;
                }

                // Verify admin table is intact (no DROP/DELETE side effects)
                $adminCount = (int) $this->pdo->query('SELECT COUNT(*) FROM admin')->fetchColumn();
                if ($adminCount !== 1) {
                    $failures++;
                    $failureDetails[] = "Iteration {$i}: admin table altered, count={$adminCount} (payload: {$payload})";
                    continue;
                }
            } catch (\Exception $e) {
                $failures++;
                $failureDetails[] = "Iteration {$i}: Exception: {$e->getMessage()} (payload: {$payload})";
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 15 (INSERT) failed in {$failures}/100 iterations.\n" . implode("\n", array_slice($failureDetails, 0, 10))
        );
    }

    // ── Property 15: SELECT with SQL injection payloads ───────────────────────

    /**
     * **Validates: Requirements 15.1**
     *
     * Property 15 (SELECT): For any string containing SQL injection characters
     * used as a search parameter, PDO prepared SELECT executes without error
     * and does not return unauthorized data — 100 iterations.
     */
    public function testProperty15_SelectWithSqlPayloads(): void
    {
        $failures       = 0;
        $failureDetails = [];

        for ($i = 0; $i < 100; $i++) {
            $payload = $this->randomSqlPayload();

            try {
                // SELECT using prepared statement with injection payload as parameter
                $stmt = $this->pdo->prepare('SELECT * FROM admin WHERE username = ?');
                $stmt->execute([$payload]);
                $results = $stmt->fetchAll();

                // Payload should NOT match the real admin username
                // so results must be empty (no unauthorized data returned)
                if (!empty($results)) {
                    $failures++;
                    $failureDetails[] = "Iteration {$i}: SELECT returned rows for injection payload: {$payload}";
                    continue;
                }

                // Also test SELECT on direcciones
                $stmt = $this->pdo->prepare('SELECT * FROM direcciones WHERE nombre = ?');
                $stmt->execute([$payload]);
                $results = $stmt->fetchAll();

                if (!empty($results)) {
                    $failures++;
                    $failureDetails[] = "Iteration {$i}: direcciones SELECT returned rows for payload: {$payload}";
                    continue;
                }

                // Verify DB structure is intact
                $adminCount = (int) $this->pdo->query('SELECT COUNT(*) FROM admin')->fetchColumn();
                if ($adminCount !== 1) {
                    $failures++;
                    $failureDetails[] = "Iteration {$i}: admin table altered after SELECT, count={$adminCount}";
                    continue;
                }
            } catch (\Exception $e) {
                $failures++;
                $failureDetails[] = "Iteration {$i}: Exception: {$e->getMessage()} (payload: {$payload})";
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 15 (SELECT) failed in {$failures}/100 iterations.\n" . implode("\n", array_slice($failureDetails, 0, 10))
        );
    }

    // ── Property 15: UPDATE with SQL injection payloads ───────────────────────

    /**
     * **Validates: Requirements 15.1**
     *
     * Property 15 (UPDATE): For any string containing SQL injection characters
     * used as UPDATE values, PDO prepared UPDATE executes without error and
     * stores the exact payload without altering other records — 100 iterations.
     */
    public function testProperty15_UpdateWithSqlPayloads(): void
    {
        $failures       = 0;
        $failureDetails = [];

        for ($i = 0; $i < 100; $i++) {
            $payload = $this->randomSqlPayload();

            try {
                // UPDATE tramites contenido with injection payload
                $stmt = $this->pdo->prepare('UPDATE tramites SET contenido = ? WHERE slug = ?');
                $stmt->execute([$payload, 'PMPNNA']);

                // Verify the value was stored exactly
                $stmt = $this->pdo->prepare('SELECT contenido FROM tramites WHERE slug = ?');
                $stmt->execute(['PMPNNA']);
                $row = $stmt->fetch();

                if (!$row) {
                    $failures++;
                    $failureDetails[] = "Iteration {$i}: tramites row disappeared after UPDATE (payload: {$payload})";
                    continue;
                }

                if ($row['contenido'] !== $payload) {
                    $failures++;
                    $failureDetails[] = "Iteration {$i}: stored value differs. Expected: {$payload}, Got: {$row['contenido']}";
                    continue;
                }

                // Verify no extra rows were created in tramites
                $tramiteCount = (int) $this->pdo->query('SELECT COUNT(*) FROM tramites')->fetchColumn();
                if ($tramiteCount !== 1) {
                    $failures++;
                    $failureDetails[] = "Iteration {$i}: tramites count changed to {$tramiteCount} (payload: {$payload})";
                    continue;
                }

                // Verify admin table is intact
                $adminCount = (int) $this->pdo->query('SELECT COUNT(*) FROM admin')->fetchColumn();
                if ($adminCount !== 1) {
                    $failures++;
                    $failureDetails[] = "Iteration {$i}: admin table altered after UPDATE, count={$adminCount}";
                    continue;
                }

                // Verify direcciones table is intact
                $dirCount = (int) $this->pdo->query('SELECT COUNT(*) FROM direcciones')->fetchColumn();
                if ($dirCount !== 1) {
                    $failures++;
                    $failureDetails[] = "Iteration {$i}: direcciones table altered after UPDATE, count={$dirCount}";
                    continue;
                }
            } catch (\Exception $e) {
                $failures++;
                $failureDetails[] = "Iteration {$i}: Exception: {$e->getMessage()} (payload: {$payload})";
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 15 (UPDATE) failed in {$failures}/100 iterations.\n" . implode("\n", array_slice($failureDetails, 0, 10))
        );
    }

    // ── Supporting unit tests ─────────────────────────────────────────────────

    /**
     * **Validates: Requirements 15.1**
     *
     * Classic SQL injection in WHERE clause does not bypass authentication.
     */
    public function testClassicAuthBypassInjectionFails(): void
    {
        $injections = [
            "' OR '1'='1",
            "' OR '1'='1'--",
            "' OR '1'='1'/*",
            "admin'--",
            "' OR 1=1#",
        ];

        foreach ($injections as $injection) {
            $stmt = $this->pdo->prepare('SELECT * FROM admin WHERE username = ?');
            $stmt->execute([$injection]);
            $results = $stmt->fetchAll();

            $this->assertEmpty(
                $results,
                "Auth bypass injection should return no results: {$injection}"
            );
        }
    }

    /**
     * **Validates: Requirements 15.1**
     *
     * UNION-based injection does not leak data from other tables.
     */
    public function testUnionInjectionDoesNotLeakData(): void
    {
        $injections = [
            "' UNION SELECT id, username, password FROM admin--",
            "' UNION ALL SELECT NULL,NULL,NULL--",
            "' UNION SELECT 1,2,3--",
        ];

        foreach ($injections as $injection) {
            $stmt = $this->pdo->prepare('SELECT * FROM direcciones WHERE nombre = ?');
            $stmt->execute([$injection]);
            $results = $stmt->fetchAll();

            $this->assertEmpty(
                $results,
                "UNION injection should return no results: {$injection}"
            );
        }
    }

    /**
     * **Validates: Requirements 15.1**
     *
     * Destructive SQL injection payloads do not alter DB structure.
     */
    public function testDestructivePayloadsDoNotAlterDb(): void
    {
        $destructive = [
            "'; DROP TABLE admin;--",
            "'; DELETE FROM direcciones;--",
            "'; TRUNCATE TABLE tramites;--",
            "'; UPDATE admin SET password='hacked';--",
            "'; INSERT INTO admin (username, password) VALUES ('hacker','pass');--",
        ];

        foreach ($destructive as $payload) {
            // Use payload as INSERT value
            $stmt = $this->pdo->prepare(
                'INSERT INTO noticias_imagenes (imagen_path, fecha_noticia) VALUES (?, ?)'
            );
            $stmt->execute([$payload, '2025-06-01']);

            // All tables must remain intact
            $this->assertSame(
                1,
                (int) $this->pdo->query('SELECT COUNT(*) FROM admin')->fetchColumn(),
                "admin table should have 1 row after payload: {$payload}"
            );
            $this->assertSame(
                1,
                (int) $this->pdo->query('SELECT COUNT(*) FROM direcciones')->fetchColumn(),
                "direcciones table should have 1 row after payload: {$payload}"
            );
            $this->assertSame(
                1,
                (int) $this->pdo->query('SELECT COUNT(*) FROM tramites')->fetchColumn(),
                "tramites table should have 1 row after payload: {$payload}"
            );
        }
    }
}
