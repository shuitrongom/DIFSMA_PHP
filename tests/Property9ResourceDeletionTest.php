<?php
/**
 * Feature: dif-cms-php-migration, Property 9: eliminación completa de recursos (archivos + DB)
 *
 * Validates: Requirements 2.4, 3.4, 4.4, 5.2, 9.4, 9.5, 11.5, 11.6, 12.5
 *
 * Para cualquier recurso registrado en DB, después de la operación de eliminación,
 * el registro no existe en DB Y el archivo no existe en el sistema de archivos — 100 iteraciones.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// ── Helper functions mirroring admin deletion logic ────────────────────────────

/**
 * Delete a slider image (slider_principal or slider_comunica).
 * Mirrors admin/slider_principal.php and admin/slider_comunica.php (action=delete).
 */
function delete_slider_image(PDO $pdo, string $table, int $id, string $basePath): bool
{
    $stmt = $pdo->prepare("SELECT imagen_path FROM {$table} WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if (!$row) {
        return false;
    }

    $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = ?");
    $stmt->execute([$id]);

    $filePath = $basePath . '/' . $row['imagen_path'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    return true;
}

/**
 * Delete a noticia image.
 * Mirrors admin/noticias.php (action=delete).
 */
function delete_noticia_image(PDO $pdo, int $id, string $basePath): bool
{
    $stmt = $pdo->prepare('SELECT imagen_path FROM noticias_imagenes WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if (!$row) {
        return false;
    }

    $stmt = $pdo->prepare('DELETE FROM noticias_imagenes WHERE id = ?');
    $stmt->execute([$id]);

    $filePath = $basePath . '/' . $row['imagen_path'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    return true;
}

/**
 * Delete a galeria image (individual image from album).
 * Mirrors admin/galeria.php (action=delete_image).
 */
function delete_galeria_image(PDO $pdo, int $imgId, string $basePath): bool
{
    $stmt = $pdo->prepare('SELECT imagen_path FROM galeria_imagenes WHERE id = ?');
    $stmt->execute([$imgId]);
    $row = $stmt->fetch();

    if (!$row) {
        return false;
    }

    $stmt = $pdo->prepare('DELETE FROM galeria_imagenes WHERE id = ?');
    $stmt->execute([$imgId]);

    $filePath = $basePath . '/' . $row['imagen_path'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    return true;
}

/**
 * Delete a SEAC PDF.
 * Mirrors admin/seac.php (action=delete_pdf).
 */
function delete_seac_pdf(PDO $pdo, int $pdfId, string $basePath): bool
{
    $stmt = $pdo->prepare('SELECT pdf_path FROM seac_pdfs WHERE id = ?');
    $stmt->execute([$pdfId]);
    $row = $stmt->fetch();

    if (!$row) {
        return false;
    }

    $stmt = $pdo->prepare('DELETE FROM seac_pdfs WHERE id = ?');
    $stmt->execute([$pdfId]);

    if (!empty($row['pdf_path'])) {
        $filePath = $basePath . '/' . $row['pdf_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    return true;
}

/**
 * Delete a programa (with cascade for secciones).
 * Mirrors admin/programas.php (action=delete).
 */
function delete_programa(PDO $pdo, int $id, string $basePath): bool
{
    $stmt = $pdo->prepare('SELECT imagen_path FROM programas WHERE id = ?');
    $stmt->execute([$id]);
    $programa = $stmt->fetch();

    if (!$programa) {
        return false;
    }

    // CASCADE will delete programas_secciones rows
    $stmt = $pdo->prepare('DELETE FROM programas WHERE id = ?');
    $stmt->execute([$id]);

    if (!empty($programa['imagen_path'])) {
        $filePath = $basePath . '/' . $programa['imagen_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    return true;
}


class Property9ResourceDeletionTest extends TestCase
{
    private PDO $pdo;
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $this->pdo->exec('PRAGMA foreign_keys = ON');

        // slider_principal
        $this->pdo->exec('
            CREATE TABLE slider_principal (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                imagen_path VARCHAR(500) NOT NULL,
                orden       INT NOT NULL DEFAULT 0,
                activo      TINYINT(1) NOT NULL DEFAULT 1,
                created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        // slider_comunica
        $this->pdo->exec('
            CREATE TABLE slider_comunica (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                imagen_path VARCHAR(500) NOT NULL,
                orden       INT NOT NULL DEFAULT 0,
                activo      TINYINT(1) NOT NULL DEFAULT 1,
                created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        // noticias_imagenes
        $this->pdo->exec('
            CREATE TABLE noticias_imagenes (
                id            INTEGER PRIMARY KEY AUTOINCREMENT,
                imagen_path   VARCHAR(500) NOT NULL,
                fecha_noticia DATE NOT NULL,
                activo        TINYINT(1) NOT NULL DEFAULT 1,
                created_at    DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        // galeria_albumes
        $this->pdo->exec('
            CREATE TABLE galeria_albumes (
                id           INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre       VARCHAR(200) NOT NULL,
                fecha_album  DATE NOT NULL,
                portada_path VARCHAR(500) DEFAULT NULL,
                activo       TINYINT(1) NOT NULL DEFAULT 1,
                created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        // galeria_imagenes
        $this->pdo->exec('
            CREATE TABLE galeria_imagenes (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                album_id    INT NOT NULL,
                imagen_path VARCHAR(500) NOT NULL,
                orden       INT NOT NULL DEFAULT 0,
                FOREIGN KEY (album_id) REFERENCES galeria_albumes(id) ON DELETE CASCADE
            )
        ');

        // seac_bloques
        $this->pdo->exec('
            CREATE TABLE seac_bloques (
                id    INTEGER PRIMARY KEY AUTOINCREMENT,
                anio  INT NOT NULL,
                orden INT NOT NULL DEFAULT 0
            )
        ');

        // seac_conceptos
        $this->pdo->exec('
            CREATE TABLE seac_conceptos (
                id       INTEGER PRIMARY KEY AUTOINCREMENT,
                bloque_id INT NOT NULL,
                numero   INT NOT NULL,
                nombre   VARCHAR(500) NOT NULL,
                orden    INT NOT NULL DEFAULT 0,
                FOREIGN KEY (bloque_id) REFERENCES seac_bloques(id) ON DELETE CASCADE
            )
        ');

        // seac_pdfs
        $this->pdo->exec('
            CREATE TABLE seac_pdfs (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                bloque_id   INT NOT NULL,
                concepto_id INT NOT NULL,
                trimestre   TINYINT NOT NULL,
                pdf_path    VARCHAR(500) DEFAULT NULL,
                FOREIGN KEY (bloque_id) REFERENCES seac_bloques(id) ON DELETE CASCADE,
                FOREIGN KEY (concepto_id) REFERENCES seac_conceptos(id)
            )
        ');

        // programas
        $this->pdo->exec('
            CREATE TABLE programas (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre      VARCHAR(200) NOT NULL,
                imagen_path VARCHAR(500) DEFAULT NULL,
                orden       INT NOT NULL DEFAULT 0,
                activo      TINYINT(1) NOT NULL DEFAULT 1
            )
        ');

        // programas_secciones
        $this->pdo->exec('
            CREATE TABLE programas_secciones (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                programa_id INT NOT NULL,
                titulo      VARCHAR(300) NOT NULL,
                contenido   TEXT NOT NULL,
                orden       INT NOT NULL DEFAULT 0,
                FOREIGN KEY (programa_id) REFERENCES programas(id) ON DELETE CASCADE
            )
        ');

        // Temp directory for dummy files
        $this->tmpDir = sys_get_temp_dir() . '/prop9_test_' . bin2hex(random_bytes(8));
        mkdir($this->tmpDir, 0777, true);
        mkdir($this->tmpDir . '/uploads/images', 0777, true);
        mkdir($this->tmpDir . '/uploads/pdfs', 0777, true);
    }

    protected function tearDown(): void
    {
        unset($this->pdo);
        $this->removeDir($this->tmpDir);
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }
        rmdir($dir);
    }

    private function randomImagePath(): string
    {
        return 'uploads/images/' . bin2hex(random_bytes(8)) . '.jpg';
    }

    private function randomPdfPath(): string
    {
        return 'uploads/pdfs/' . bin2hex(random_bytes(8)) . '.pdf';
    }

    private function createDummyFile(string $relativePath): void
    {
        $fullPath = $this->tmpDir . '/' . $relativePath;
        file_put_contents($fullPath, 'dummy-content-' . bin2hex(random_bytes(4)));
    }

    private function randomDate(): string
    {
        return sprintf('%04d-%02d-%02d', random_int(2020, 2025), random_int(1, 12), random_int(1, 28));
    }

    // ── Property 9 ────────────────────────────────────────────────────────────

    /**
     * **Validates: Requirements 2.4, 3.4, 4.4, 5.2, 9.4, 9.5, 11.5, 11.6, 12.5**
     *
     * Property 9: Para cualquier recurso registrado en DB, después de la operación
     * de eliminación, el registro no existe en DB Y el archivo no existe en el
     * sistema de archivos — 100 iteraciones.
     */
    public function testProperty9_ResourceDeletionCompleteness(): void
    {
        $failures       = 0;
        $failureDetails = [];

        // Resource types to cycle through
        $resourceTypes = [
            'slider_principal',
            'slider_comunica',
            'noticias',
            'galeria_image',
            'seac_pdf',
            'programa',
        ];

        for ($i = 0; $i < 100; $i++) {
            $type = $resourceTypes[$i % count($resourceTypes)];

            try {
                switch ($type) {
                    case 'slider_principal':
                    case 'slider_comunica':
                        $result = $this->runSliderDeletion($type);
                        break;
                    case 'noticias':
                        $result = $this->runNoticiaDeletion();
                        break;
                    case 'galeria_image':
                        $result = $this->runGaleriaImageDeletion();
                        break;
                    case 'seac_pdf':
                        $result = $this->runSeacPdfDeletion();
                        break;
                    case 'programa':
                        $result = $this->runProgramaDeletion();
                        break;
                    default:
                        $result = ['ok' => false, 'error' => "Unknown type: {$type}"];
                }

                if (!$result['ok']) {
                    $failures++;
                    $failureDetails[] = "Iteration {$i} ({$type}): {$result['error']}";
                }
            } catch (\Exception $e) {
                $failures++;
                $failureDetails[] = "Iteration {$i} ({$type}): Exception: {$e->getMessage()}";
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 9 failed in {$failures}/100 iterations.\n" . implode("\n", array_slice($failureDetails, 0, 10))
        );
    }

    // ── Per-resource-type deletion runners ─────────────────────────────────────

    private function runSliderDeletion(string $table): array
    {
        $imgPath = $this->randomImagePath();
        $this->createDummyFile($imgPath);

        $stmt = $this->pdo->prepare("INSERT INTO {$table} (imagen_path, orden, activo) VALUES (?, ?, 1)");
        $stmt->execute([$imgPath, random_int(0, 100)]);
        $id = (int) $this->pdo->lastInsertId();

        // Pre-condition: file and row exist
        if (!file_exists($this->tmpDir . '/' . $imgPath)) {
            return ['ok' => false, 'error' => 'pre-condition: file not created'];
        }

        $deleted = delete_slider_image($this->pdo, $table, $id, $this->tmpDir);

        if (!$deleted) {
            return ['ok' => false, 'error' => 'delete function returned false'];
        }

        // Assert: DB row gone
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE id = ?");
        $stmt->execute([$id]);
        if ((int) $stmt->fetchColumn() !== 0) {
            return ['ok' => false, 'error' => 'DB row still exists'];
        }

        // Assert: file gone
        if (file_exists($this->tmpDir . '/' . $imgPath)) {
            return ['ok' => false, 'error' => 'file still exists on disk'];
        }

        return ['ok' => true, 'error' => ''];
    }

    private function runNoticiaDeletion(): array
    {
        $imgPath = $this->randomImagePath();
        $this->createDummyFile($imgPath);

        $stmt = $this->pdo->prepare(
            'INSERT INTO noticias_imagenes (imagen_path, fecha_noticia, activo) VALUES (?, ?, 1)'
        );
        $stmt->execute([$imgPath, $this->randomDate()]);
        $id = (int) $this->pdo->lastInsertId();

        if (!file_exists($this->tmpDir . '/' . $imgPath)) {
            return ['ok' => false, 'error' => 'pre-condition: file not created'];
        }

        $deleted = delete_noticia_image($this->pdo, $id, $this->tmpDir);

        if (!$deleted) {
            return ['ok' => false, 'error' => 'delete function returned false'];
        }

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM noticias_imagenes WHERE id = ?');
        $stmt->execute([$id]);
        if ((int) $stmt->fetchColumn() !== 0) {
            return ['ok' => false, 'error' => 'DB row still exists'];
        }

        if (file_exists($this->tmpDir . '/' . $imgPath)) {
            return ['ok' => false, 'error' => 'file still exists on disk'];
        }

        return ['ok' => true, 'error' => ''];
    }

    private function runGaleriaImageDeletion(): array
    {
        // Create album first
        $portadaPath = $this->randomImagePath();
        $this->createDummyFile($portadaPath);

        $stmt = $this->pdo->prepare(
            'INSERT INTO galeria_albumes (nombre, fecha_album, portada_path, activo) VALUES (?, ?, ?, 1)'
        );
        $stmt->execute(['Album Test', $this->randomDate(), $portadaPath]);
        $albumId = (int) $this->pdo->lastInsertId();

        // Create image in album
        $imgPath = $this->randomImagePath();
        $this->createDummyFile($imgPath);

        $stmt = $this->pdo->prepare(
            'INSERT INTO galeria_imagenes (album_id, imagen_path, orden) VALUES (?, ?, 0)'
        );
        $stmt->execute([$albumId, $imgPath]);
        $imgId = (int) $this->pdo->lastInsertId();

        if (!file_exists($this->tmpDir . '/' . $imgPath)) {
            return ['ok' => false, 'error' => 'pre-condition: file not created'];
        }

        $deleted = delete_galeria_image($this->pdo, $imgId, $this->tmpDir);

        if (!$deleted) {
            return ['ok' => false, 'error' => 'delete function returned false'];
        }

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM galeria_imagenes WHERE id = ?');
        $stmt->execute([$imgId]);
        if ((int) $stmt->fetchColumn() !== 0) {
            return ['ok' => false, 'error' => 'DB row still exists'];
        }

        if (file_exists($this->tmpDir . '/' . $imgPath)) {
            return ['ok' => false, 'error' => 'file still exists on disk'];
        }

        // Cleanup: remove album
        $this->pdo->prepare('DELETE FROM galeria_albumes WHERE id = ?')->execute([$albumId]);
        $portadaFull = $this->tmpDir . '/' . $portadaPath;
        if (file_exists($portadaFull)) {
            unlink($portadaFull);
        }

        return ['ok' => true, 'error' => ''];
    }

    private function runSeacPdfDeletion(): array
    {
        // Create bloque and concepto
        $anio = random_int(2000, 2099);
        $stmt = $this->pdo->prepare('INSERT INTO seac_bloques (anio, orden) VALUES (?, 0)');
        $stmt->execute([$anio]);
        $bloqueId = (int) $this->pdo->lastInsertId();

        $stmt = $this->pdo->prepare('INSERT INTO seac_conceptos (bloque_id, numero, nombre, orden) VALUES (?, ?, ?, 0)');
        $stmt->execute([$bloqueId, random_int(1, 99), 'Concepto Test ' . $anio]);
        $conceptoId = (int) $this->pdo->lastInsertId();

        // Create PDF
        $pdfPath = $this->randomPdfPath();
        $this->createDummyFile($pdfPath);

        $trimestre = random_int(1, 4);
        $stmt = $this->pdo->prepare(
            'INSERT INTO seac_pdfs (bloque_id, concepto_id, trimestre, pdf_path) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$bloqueId, $conceptoId, $trimestre, $pdfPath]);
        $pdfId = (int) $this->pdo->lastInsertId();

        if (!file_exists($this->tmpDir . '/' . $pdfPath)) {
            return ['ok' => false, 'error' => 'pre-condition: file not created'];
        }

        $deleted = delete_seac_pdf($this->pdo, $pdfId, $this->tmpDir);

        if (!$deleted) {
            return ['ok' => false, 'error' => 'delete function returned false'];
        }

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM seac_pdfs WHERE id = ?');
        $stmt->execute([$pdfId]);
        if ((int) $stmt->fetchColumn() !== 0) {
            return ['ok' => false, 'error' => 'DB row still exists'];
        }

        if (file_exists($this->tmpDir . '/' . $pdfPath)) {
            return ['ok' => false, 'error' => 'file still exists on disk'];
        }

        // Cleanup
        $this->pdo->prepare('DELETE FROM seac_bloques WHERE id = ?')->execute([$bloqueId]);
        $this->pdo->prepare('DELETE FROM seac_conceptos WHERE id = ?')->execute([$conceptoId]);

        return ['ok' => true, 'error' => ''];
    }

    private function runProgramaDeletion(): array
    {
        $imgPath = $this->randomImagePath();
        $this->createDummyFile($imgPath);

        $stmt = $this->pdo->prepare(
            'INSERT INTO programas (nombre, imagen_path, orden, activo) VALUES (?, ?, ?, 1)'
        );
        $stmt->execute(['Programa Test ' . random_int(1, 9999), $imgPath, random_int(0, 100)]);
        $id = (int) $this->pdo->lastInsertId();

        // Add a section to verify cascade
        $stmt = $this->pdo->prepare(
            'INSERT INTO programas_secciones (programa_id, titulo, contenido, orden) VALUES (?, ?, ?, 0)'
        );
        $stmt->execute([$id, 'Sección Test', 'Contenido de prueba']);

        if (!file_exists($this->tmpDir . '/' . $imgPath)) {
            return ['ok' => false, 'error' => 'pre-condition: file not created'];
        }

        $deleted = delete_programa($this->pdo, $id, $this->tmpDir);

        if (!$deleted) {
            return ['ok' => false, 'error' => 'delete function returned false'];
        }

        // Assert: programa row gone
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM programas WHERE id = ?');
        $stmt->execute([$id]);
        if ((int) $stmt->fetchColumn() !== 0) {
            return ['ok' => false, 'error' => 'DB row still exists'];
        }

        // Assert: secciones cascade deleted
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM programas_secciones WHERE programa_id = ?');
        $stmt->execute([$id]);
        if ((int) $stmt->fetchColumn() !== 0) {
            return ['ok' => false, 'error' => 'programas_secciones rows still exist after cascade'];
        }

        // Assert: file gone
        if (file_exists($this->tmpDir . '/' . $imgPath)) {
            return ['ok' => false, 'error' => 'file still exists on disk'];
        }

        return ['ok' => true, 'error' => ''];
    }
}
