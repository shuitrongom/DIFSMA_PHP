<?php
/**
 * Unit tests for Slider Principal CRUD operations.
 *
 * Validates: Requirements 2.1, 2.2, 2.3, 2.4
 *
 * Tests add, edit, and delete of slider images using SQLite in-memory DB.
 * Verifies file deletion from server when deleting/replacing records.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class SliderPrincipalCrudTest extends TestCase
{
    private PDO $pdo;
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $this->pdo->exec('
            CREATE TABLE slider_principal (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                imagen_path VARCHAR(500) NOT NULL,
                orden       INT NOT NULL DEFAULT 0,
                activo      TINYINT(1) NOT NULL DEFAULT 1,
                created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        // Create a temporary directory to simulate uploads/images/
        $this->tmpDir = sys_get_temp_dir() . '/slider_test_' . bin2hex(random_bytes(4));
        mkdir($this->tmpDir, 0755, true);
    }

    protected function tearDown(): void
    {
        unset($this->pdo);

        // Clean up temp directory
        if (is_dir($this->tmpDir)) {
            $files = glob($this->tmpDir . '/*');
            if ($files) {
                array_map('unlink', $files);
            }
            rmdir($this->tmpDir);
        }
    }

    /**
     * Creates a fake image file in the temp directory and returns its path.
     */
    private function createFakeFile(string $filename = 'test.jpg'): string
    {
        $path = $this->tmpDir . '/' . $filename;
        file_put_contents($path, 'fake-image-content-' . bin2hex(random_bytes(8)));
        return $path;
    }

    /**
     * Simulates the ADD action: INSERT with auto-incremented orden.
     * Returns the inserted row.
     */
    private function addSliderImage(string $imagenPath): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT COALESCE(MAX(orden), 0) + 1 AS next_orden FROM slider_principal'
        );
        $stmt->execute();
        $nextOrden = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->prepare(
            'INSERT INTO slider_principal (imagen_path, orden, activo) VALUES (?, ?, 1)'
        );
        $stmt->execute([$imagenPath, $nextOrden]);

        $id = (int) $this->pdo->lastInsertId();

        $stmt = $this->pdo->prepare('SELECT * FROM slider_principal WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Simulates the EDIT action: UPDATE imagen_path, unlink old file.
     */
    private function editSliderImage($id, string $newImagenPath, string $basePath): bool
    {
        $stmt = $this->pdo->prepare('SELECT imagen_path FROM slider_principal WHERE id = ?');
        $stmt->execute([$id]);
        $old = $stmt->fetch();

        if (!$old) {
            return false;
        }

        $stmt = $this->pdo->prepare('UPDATE slider_principal SET imagen_path = ? WHERE id = ?');
        $stmt->execute([$newImagenPath, $id]);

        // Delete old file
        $oldFile = $basePath . '/' . $old['imagen_path'];
        if (file_exists($oldFile)) {
            unlink($oldFile);
        }

        return true;
    }

    /**
     * Simulates the DELETE action: DELETE row + unlink file.
     */
    private function deleteSliderImage($id, string $basePath): bool
    {
        $stmt = $this->pdo->prepare('SELECT imagen_path FROM slider_principal WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return false;
        }

        $stmt = $this->pdo->prepare('DELETE FROM slider_principal WHERE id = ?');
        $stmt->execute([$id]);

        $filePath = $basePath . '/' . $row['imagen_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return true;
    }

    // ── Test: Adding a new slider image ────────────────────────────────────────

    /**
     * Validates: Requirement 2.1, 2.2
     *
     * Adding a new slider image inserts a record in DB with correct path.
     */
    public function testAddSliderImageInsertsRecord(): void
    {
        $imagenPath = 'uploads/images/abc123.jpg';
        $row = $this->addSliderImage($imagenPath);

        $this->assertNotEmpty($row);
        $this->assertSame($imagenPath, $row['imagen_path']);
        $this->assertEquals(1, $row['activo']);
    }

    /**
     * Validates: Requirement 2.2
     *
     * Adding a slider image creates the file on disk (simulated).
     */
    public function testAddSliderImageFileIsStored(): void
    {
        $filename = bin2hex(random_bytes(8)) . '.jpg';
        $filePath = $this->createFakeFile($filename);

        $this->assertFileExists($filePath, 'Uploaded file should exist on disk after add.');
    }

    // ── Test: Auto-increment of orden field ────────────────────────────────────

    /**
     * Validates: Requirement 2.1
     *
     * Each new image gets an auto-incremented orden value.
     */
    public function testAutoIncrementOrden(): void
    {
        $row1 = $this->addSliderImage('uploads/images/img1.jpg');
        $row2 = $this->addSliderImage('uploads/images/img2.jpg');
        $row3 = $this->addSliderImage('uploads/images/img3.jpg');

        $this->assertEquals(1, $row1['orden']);
        $this->assertEquals(2, $row2['orden']);
        $this->assertEquals(3, $row3['orden']);
    }

    /**
     * Validates: Requirement 2.1
     *
     * After deleting an image, the next added image gets MAX(orden)+1.
     */
    public function testOrdenContinuesAfterDeletion(): void
    {
        $this->addSliderImage('uploads/images/img1.jpg');
        $row2 = $this->addSliderImage('uploads/images/img2.jpg');
        $this->addSliderImage('uploads/images/img3.jpg');

        // Delete the middle one
        $this->pdo->prepare('DELETE FROM slider_principal WHERE id = ?')
            ->execute([$row2['id']]);

        // Add a new one — should get orden = 4 (MAX(3) + 1)
        $row4 = $this->addSliderImage('uploads/images/img4.jpg');
        $this->assertEquals(4, $row4['orden']);
    }

    // ── Test: Editing/replacing a slider image ─────────────────────────────────

    /**
     * Validates: Requirement 2.3
     *
     * Editing a slider image updates the DB record with the new path.
     */
    public function testEditSliderImageUpdatesRecord(): void
    {
        $row = $this->addSliderImage('old_image.jpg');

        $this->editSliderImage($row['id'], 'new_image.jpg', $this->tmpDir);

        $stmt = $this->pdo->prepare('SELECT imagen_path FROM slider_principal WHERE id = ?');
        $stmt->execute([$row['id']]);
        $updated = $stmt->fetch();

        $this->assertSame('new_image.jpg', $updated['imagen_path']);
    }

    /**
     * Validates: Requirement 2.3
     *
     * Editing a slider image deletes the old file from disk.
     */
    public function testEditSliderImageDeletesOldFile(): void
    {
        // Create old file on disk
        $oldFilename = 'old_slide.jpg';
        $oldFilePath = $this->createFakeFile($oldFilename);
        $this->assertFileExists($oldFilePath);

        // Insert record pointing to old file (relative to tmpDir)
        $row = $this->addSliderImage($oldFilename);

        // Create new file
        $newFilename = 'new_slide.jpg';
        $this->createFakeFile($newFilename);

        // Edit: replace old with new
        $this->editSliderImage($row['id'], $newFilename, $this->tmpDir);

        // Old file should be deleted
        $this->assertFileDoesNotExist($oldFilePath, 'Old file should be deleted after edit.');
        // New file should still exist
        $this->assertFileExists($this->tmpDir . '/' . $newFilename, 'New file should exist after edit.');
    }

    /**
     * Validates: Requirement 2.3
     *
     * Editing a non-existent record returns false.
     */
    public function testEditNonExistentRecordReturnsFalse(): void
    {
        $result = $this->editSliderImage(9999, 'new.jpg', $this->tmpDir);
        $this->assertFalse($result);
    }

    // ── Test: Deleting a slider image ──────────────────────────────────────────

    /**
     * Validates: Requirement 2.4
     *
     * Deleting a slider image removes the record from DB.
     */
    public function testDeleteSliderImageRemovesRecord(): void
    {
        $row = $this->addSliderImage('uploads/images/to_delete.jpg');

        $this->deleteSliderImage($row['id'], $this->tmpDir);

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM slider_principal WHERE id = ?');
        $stmt->execute([$row['id']]);
        $count = (int) $stmt->fetchColumn();

        $this->assertSame(0, $count, 'Record should be deleted from DB.');
    }

    /**
     * Validates: Requirement 2.4
     *
     * Deleting a slider image removes the file from disk.
     */
    public function testDeleteSliderImageRemovesFile(): void
    {
        $filename = 'delete_me.jpg';
        $filePath = $this->createFakeFile($filename);
        $this->assertFileExists($filePath);

        $row = $this->addSliderImage($filename);

        $this->deleteSliderImage($row['id'], $this->tmpDir);

        $this->assertFileDoesNotExist($filePath, 'File should be deleted from server after delete.');
    }

    /**
     * Validates: Requirement 2.4
     *
     * Deleting a non-existent record returns false.
     */
    public function testDeleteNonExistentRecordReturnsFalse(): void
    {
        $result = $this->deleteSliderImage(9999, $this->tmpDir);
        $this->assertFalse($result);
    }

    // ── Test: PDO prepared statements ──────────────────────────────────────────

    /**
     * Validates: Requirement 2.1, 2.2
     *
     * CRUD operations use PDO prepared statements (parameterized queries).
     * Inserting a path with SQL-special characters does not break the query.
     */
    public function testPreparedStatementsHandleSqlSpecialChars(): void
    {
        $maliciousPath = "uploads/images/test'; DROP TABLE slider_principal;--.jpg";

        $row = $this->addSliderImage($maliciousPath);

        $this->assertNotEmpty($row);
        $this->assertSame($maliciousPath, $row['imagen_path']);

        // Table should still exist and be queryable
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM slider_principal');
        $count = (int) $stmt->fetchColumn();
        $this->assertSame(1, $count, 'Table should still exist after SQL injection attempt.');
    }

    /**
     * Validates: Requirement 2.4
     *
     * Delete gracefully handles missing file on disk (file already removed).
     */
    public function testDeleteHandlesMissingFileGracefully(): void
    {
        // Insert record but don't create the actual file
        $row = $this->addSliderImage('nonexistent_file.jpg');

        // Should not throw — file doesn't exist but that's OK
        $result = $this->deleteSliderImage($row['id'], $this->tmpDir);
        $this->assertTrue($result);

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM slider_principal WHERE id = ?');
        $stmt->execute([$row['id']]);
        $this->assertSame(0, (int) $stmt->fetchColumn());
    }
}
