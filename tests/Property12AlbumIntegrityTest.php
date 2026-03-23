<?php
/**
 * Feature: dif-cms-php-migration, Property 12: integridad referencial de álbumes de galería
 *
 * Validates: Requirements 9.5
 *
 * Para cualquier álbum eliminado de `galeria_albumes`, todas las filas de
 * `galeria_imagenes` con ese `album_id` son eliminadas en cascada (ON DELETE CASCADE),
 * y los archivos de imagen correspondientes son eliminados del servidor — 100 iteraciones.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Deletes an album and cleans up associated image files from disk.
 * Mirrors the logic in admin/galeria.php (action=delete_album).
 *
 * 1. Fetches album portada_path and all galeria_imagenes paths
 * 2. DELETEs the album row (CASCADE removes galeria_imagenes rows)
 * 3. Unlinks image files and portada file from disk
 *
 * @param PDO    $pdo      Active PDO connection
 * @param int    $albumId  The album ID to delete
 * @param string $basePath Base filesystem path prepended to stored paths
 * @return bool  True if album was found and deleted
 */
function delete_album(PDO $pdo, int $albumId, string $basePath): bool
{
    // Fetch album
    $stmt = $pdo->prepare('SELECT portada_path FROM galeria_albumes WHERE id = ?');
    $stmt->execute([$albumId]);
    $album = $stmt->fetch();

    if (!$album) {
        return false;
    }

    // Fetch all image paths before cascade delete removes them
    $stmtImgs = $pdo->prepare('SELECT imagen_path FROM galeria_imagenes WHERE album_id = ?');
    $stmtImgs->execute([$albumId]);
    $imagenes = $stmtImgs->fetchAll();

    // Delete album row — CASCADE removes galeria_imagenes rows
    $stmt = $pdo->prepare('DELETE FROM galeria_albumes WHERE id = ?');
    $stmt->execute([$albumId]);

    // Remove image files from disk
    foreach ($imagenes as $img) {
        $filePath = $basePath . '/' . $img['imagen_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Remove portada file from disk
    if (!empty($album['portada_path'])) {
        $portadaPath = $basePath . '/' . $album['portada_path'];
        if (file_exists($portadaPath)) {
            unlink($portadaPath);
        }
    }

    return true;
}


class Property12AlbumIntegrityTest extends TestCase
{
    private PDO $pdo;
    private string $tmpDir;

    protected function setUp(): void
    {
        // In-memory SQLite with foreign keys enabled for CASCADE
        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // SQLite requires explicit foreign key enforcement
        $this->pdo->exec('PRAGMA foreign_keys = ON');

        $this->pdo->exec('
            CREATE TABLE galeria_albumes (
                id           INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre       VARCHAR(200) NOT NULL,
                fecha_album  DATE         NOT NULL,
                portada_path VARCHAR(500) DEFAULT NULL,
                activo       TINYINT(1)   NOT NULL DEFAULT 1,
                created_at   DATETIME     DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $this->pdo->exec('
            CREATE TABLE galeria_imagenes (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                album_id    INT          NOT NULL,
                imagen_path VARCHAR(500) NOT NULL,
                orden       INT          NOT NULL DEFAULT 0,
                FOREIGN KEY (album_id) REFERENCES galeria_albumes(id) ON DELETE CASCADE
            )
        ');

        // Create a temporary directory for dummy image files
        $this->tmpDir = sys_get_temp_dir() . '/prop12_test_' . bin2hex(random_bytes(8));
        mkdir($this->tmpDir, 0777, true);
        mkdir($this->tmpDir . '/uploads/images', 0777, true);
    }

    protected function tearDown(): void
    {
        unset($this->pdo);
        $this->removeDir($this->tmpDir);
    }

    /**
     * Recursively remove a directory and its contents.
     */
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

    /**
     * Generate a random image path under uploads/images/.
     */
    private function randomImagePath(): string
    {
        return 'uploads/images/' . bin2hex(random_bytes(8)) . '.jpg';
    }

    /**
     * Generate a random album name.
     */
    private function randomAlbumName(): string
    {
        $words = ['Evento', 'Fiesta', 'Reunión', 'Taller', 'Conferencia', 'Visita', 'Campaña'];
        return $words[array_rand($words)] . ' ' . random_int(1, 9999);
    }

    /**
     * Generate a random date string (YYYY-MM-DD).
     */
    private function randomDate(): string
    {
        $year  = random_int(2020, 2025);
        $month = random_int(1, 12);
        $day   = random_int(1, 28);
        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    /**
     * Create a dummy file on disk and return its relative path.
     */
    private function createDummyFile(string $relativePath): void
    {
        $fullPath = $this->tmpDir . '/' . $relativePath;
        file_put_contents($fullPath, 'dummy-image-content-' . bin2hex(random_bytes(4)));
    }

    /**
     * Clear all rows from both tables between iterations.
     */
    private function clearTables(): void
    {
        $this->pdo->exec('DELETE FROM galeria_imagenes');
        $this->pdo->exec('DELETE FROM galeria_albumes');
    }

    // ── Property 12 ───────────────────────────────────────────────────────────

    /**
     * **Validates: Requirements 9.5**
     *
     * Property 12: Para cualquier álbum eliminado, todas las filas de
     * `galeria_imagenes` con ese `album_id` son eliminadas en cascada y los
     * archivos de imagen son eliminados del servidor — 100 iteraciones.
     */
    public function testProperty12_AlbumReferentialIntegrity(): void
    {
        $failures       = 0;
        $failureDetails = [];

        for ($i = 0; $i < 100; $i++) {
            $this->clearTables();

            // --- Arrange: create album with random images ---
            $albumName   = $this->randomAlbumName();
            $albumDate   = $this->randomDate();
            $portadaPath = $this->randomImagePath();
            $numImages   = random_int(1, 8);

            // Create portada file on disk
            $this->createDummyFile($portadaPath);

            // Insert album
            $stmt = $this->pdo->prepare(
                'INSERT INTO galeria_albumes (nombre, fecha_album, portada_path, activo) VALUES (?, ?, ?, 1)'
            );
            $stmt->execute([$albumName, $albumDate, $portadaPath]);
            $albumId = (int) $this->pdo->lastInsertId();

            // Insert images and create files on disk
            $imagePaths = [];
            $stmtImg = $this->pdo->prepare(
                'INSERT INTO galeria_imagenes (album_id, imagen_path, orden) VALUES (?, ?, ?)'
            );
            for ($j = 0; $j < $numImages; $j++) {
                $imgPath      = $this->randomImagePath();
                $imagePaths[] = $imgPath;
                $stmtImg->execute([$albumId, $imgPath, $j]);
                $this->createDummyFile($imgPath);
            }

            // Verify pre-conditions: files exist and DB rows exist
            $stmtCount = $this->pdo->prepare('SELECT COUNT(*) FROM galeria_imagenes WHERE album_id = ?');
            $stmtCount->execute([$albumId]);
            $preCount = (int) $stmtCount->fetchColumn();

            if ($preCount !== $numImages) {
                $failures++;
                $failureDetails[] = "Iteration {$i}: pre-condition failed, expected {$numImages} images, got {$preCount}";
                continue;
            }

            // --- Act: delete the album ---
            $deleted = delete_album($this->pdo, $albumId, $this->tmpDir);

            if (!$deleted) {
                $failures++;
                $failureDetails[] = "Iteration {$i}: delete_album returned false";
                continue;
            }

            // --- Assert 1: album row is gone ---
            $stmtAlbum = $this->pdo->prepare('SELECT COUNT(*) FROM galeria_albumes WHERE id = ?');
            $stmtAlbum->execute([$albumId]);
            $albumCount = (int) $stmtAlbum->fetchColumn();

            if ($albumCount !== 0) {
                $failures++;
                $failureDetails[] = "Iteration {$i}: album row still exists after delete";
                continue;
            }

            // --- Assert 2: no galeria_imagenes rows remain (CASCADE) ---
            $stmtCount->execute([$albumId]);
            $postCount = (int) $stmtCount->fetchColumn();

            if ($postCount !== 0) {
                $failures++;
                $failureDetails[] = "Iteration {$i}: {$postCount} galeria_imagenes rows remain after cascade delete";
                continue;
            }

            // --- Assert 3: all image files are removed from disk ---
            $filesRemaining = [];
            foreach ($imagePaths as $imgPath) {
                $fullPath = $this->tmpDir . '/' . $imgPath;
                if (file_exists($fullPath)) {
                    $filesRemaining[] = $imgPath;
                }
            }

            if (!empty($filesRemaining)) {
                $failures++;
                $failureDetails[] = "Iteration {$i}: " . count($filesRemaining) . " image files still on disk";
                continue;
            }

            // --- Assert 4: portada file is removed from disk ---
            $portadaFull = $this->tmpDir . '/' . $portadaPath;
            if (file_exists($portadaFull)) {
                $failures++;
                $failureDetails[] = "Iteration {$i}: portada file still on disk";
                continue;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 12 failed in {$failures}/100 iterations.\n" . implode("\n", array_slice($failureDetails, 0, 5))
        );
    }

    // ── Supporting unit tests ─────────────────────────────────────────────────

    /**
     * **Validates: Requirements 9.5**
     *
     * CASCADE delete removes galeria_imagenes rows when album is deleted directly via SQL.
     */
    public function testCascadeDeleteRemovesImageRows(): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO galeria_albumes (nombre, fecha_album, portada_path) VALUES (?, ?, ?)'
        );
        $stmt->execute(['Test Album', '2024-01-01', 'uploads/images/portada.jpg']);
        $albumId = (int) $this->pdo->lastInsertId();

        $stmtImg = $this->pdo->prepare(
            'INSERT INTO galeria_imagenes (album_id, imagen_path, orden) VALUES (?, ?, ?)'
        );
        $stmtImg->execute([$albumId, 'uploads/images/img1.jpg', 0]);
        $stmtImg->execute([$albumId, 'uploads/images/img2.jpg', 1]);

        // Direct SQL delete triggers CASCADE
        $this->pdo->prepare('DELETE FROM galeria_albumes WHERE id = ?')->execute([$albumId]);

        $count = (int) $this->pdo->prepare('SELECT COUNT(*) FROM galeria_imagenes WHERE album_id = ?')
            ->execute([$albumId]) ? $this->pdo->query("SELECT COUNT(*) FROM galeria_imagenes WHERE album_id = {$albumId}")->fetchColumn() : 0;

        $this->assertSame(0, (int) $count, 'CASCADE should remove all image rows when album is deleted.');
    }

    /**
     * **Validates: Requirements 9.5**
     *
     * Deleting a non-existent album returns false.
     */
    public function testDeleteNonExistentAlbumReturnsFalse(): void
    {
        $result = delete_album($this->pdo, 99999, $this->tmpDir);
        $this->assertFalse($result, 'Deleting a non-existent album should return false.');
    }

    /**
     * **Validates: Requirements 9.5**
     *
     * Album with zero images: delete removes album row and portada file only.
     */
    public function testDeleteAlbumWithNoImages(): void
    {
        $portadaPath = 'uploads/images/portada_empty.jpg';
        $this->createDummyFile($portadaPath);

        $stmt = $this->pdo->prepare(
            'INSERT INTO galeria_albumes (nombre, fecha_album, portada_path) VALUES (?, ?, ?)'
        );
        $stmt->execute(['Empty Album', '2024-06-15', $portadaPath]);
        $albumId = (int) $this->pdo->lastInsertId();

        $result = delete_album($this->pdo, $albumId, $this->tmpDir);

        $this->assertTrue($result);

        // Album row gone
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM galeria_albumes WHERE id = ?');
        $stmt->execute([$albumId]);
        $this->assertSame(0, (int) $stmt->fetchColumn());

        // Portada file gone
        $this->assertFileDoesNotExist($this->tmpDir . '/' . $portadaPath);
    }
}
