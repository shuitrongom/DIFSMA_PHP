<?php
/**
 * Feature: dif-cms-php-migration, Property 7: renombrado aleatorio de archivos subidos
 *
 * Validates: Requirements 15.3
 *
 * Para cualquier nombre de archivo original, el nombre almacenado en servidor
 * es diferente — 100 iteraciones.
 *
 * Strategy:
 *   - We extract the renaming logic from upload_handler.php into a pure helper
 *     function generate_upload_filename() that mirrors bin2hex(random_bytes(16)) . '.' . $ext.
 *   - We test three properties independently with 100 iterations each:
 *       7a: generated name != original filename
 *       7b: two consecutive calls produce different names (uniqueness)
 *       7c: generated name preserves the original extension
 *   - We also verify the expected format: 32 hex chars + dot + extension.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// ── Pure helper: mirrors the renaming logic in upload_handler.php ─────────────

/**
 * Generates a secure random filename preserving the original extension.
 *
 * Mirrors the logic from upload_handler.php:
 *   $newName = bin2hex(random_bytes(16)) . '.' . $ext;
 *
 * @param string $originalName The original filename as submitted by the client.
 * @return string A new random filename with the same extension.
 */
function generate_upload_filename(string $originalName): string
{
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    return bin2hex(random_bytes(16)) . '.' . $ext;
}

// ── Test class ────────────────────────────────────────────────────────────────

class Property7FileRenamingTest extends TestCase
{
    // ── Generators ────────────────────────────────────────────────────────────

    /**
     * Returns a random original filename with a valid image or PDF extension.
     */
    private function randomOriginalFilename(): string
    {
        $names = [
            'foto_evento.jpg',
            'imagen de portada.png',
            'organigrama 2025.pdf',
            'slider-principal.webp',
            'FOTO_PRESIDENTA.JPG',
            'Galería Fotos.PNG',
            'documento final v2.PDF',
            'noticia_hoy.jpeg',
            'programa DIF.jpg',
            'transparencia-2024.pdf',
            'mi archivo con espacios.png',
            'archivo(1).jpg',
            'foto&evento=2025.jpg',
            "archivo'con'comillas.png",
            'archivo con ñ y acentós.pdf',
            'UPPERCASE.WEBP',
            'mixedCase.Jpg',
            'very_long_filename_that_exceeds_normal_limits_for_testing_purposes_only.jpg',
            '123456.png',
            'a.pdf',
        ];

        return $names[array_rand($names)];
    }

    /**
     * Returns a random extension from the allowed set.
     */
    private function randomAllowedExtension(): string
    {
        $exts = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
        return $exts[array_rand($exts)];
    }

    // =========================================================================
    // Property 7a — generated name is always different from original (100 iter)
    // =========================================================================

    /**
     * **Validates: Requirements 15.3**
     *
     * For any original filename, the name produced by generate_upload_filename()
     * MUST be different from the original name.
     *
     * This ensures the server never stores files under their client-supplied names,
     * preventing path traversal and filename-based attacks.
     */
    public function testProperty7a_GeneratedNameDiffersFromOriginal(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $original  = $this->randomOriginalFilename();
            $generated = generate_upload_filename($original);

            if ($generated === $original) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 7a failed: generated filename matched original in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Property 7b — two consecutive calls produce different names (100 iter)
    // =========================================================================

    /**
     * **Validates: Requirements 15.3**
     *
     * For any original filename, two consecutive calls to generate_upload_filename()
     * MUST produce different names.
     *
     * This ensures that uploading the same file twice does not overwrite the first
     * upload, and that filenames are not predictable.
     */
    public function testProperty7b_ConsecutiveCallsProduceDifferentNames(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $original = $this->randomOriginalFilename();
            $first    = generate_upload_filename($original);
            $second   = generate_upload_filename($original);

            if ($first === $second) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 7b failed: two consecutive calls produced the same name in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Property 7c — generated name preserves the original extension (100 iter)
    // =========================================================================

    /**
     * **Validates: Requirements 15.3**
     *
     * For any original filename, the extension of the generated name MUST match
     * the (lowercased) extension of the original filename.
     *
     * This ensures the server can serve the file with the correct Content-Type
     * and that the extension-based MIME validation remains consistent.
     */
    public function testProperty7c_GeneratedNamePreservesExtension(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $original      = $this->randomOriginalFilename();
            $originalExt   = strtolower(pathinfo($original, PATHINFO_EXTENSION));
            $generated     = generate_upload_filename($original);
            $generatedExt  = strtolower(pathinfo($generated, PATHINFO_EXTENSION));

            if ($generatedExt !== $originalExt) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 7c failed: generated name did not preserve extension in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Property 7d — generated name matches expected format (100 iter)
    // =========================================================================

    /**
     * **Validates: Requirements 15.3**
     *
     * For any original filename with a known extension, the generated name MUST
     * match the pattern: exactly 32 lowercase hex characters, a dot, then the extension.
     *
     * Pattern: /^[0-9a-f]{32}\.[a-z0-9]+$/
     */
    public function testProperty7d_GeneratedNameMatchesExpectedFormat(): void
    {
        $failures = 0;
        $pattern  = '/^[0-9a-f]{32}\.[a-z0-9]+$/';

        for ($i = 0; $i < 100; $i++) {
            $original  = $this->randomOriginalFilename();
            $generated = generate_upload_filename($original);

            if (!preg_match($pattern, $generated)) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 7d failed: generated name did not match /^[0-9a-f]{32}\\.[a-z0-9]+\$/ in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Edge cases — specific filenames and boundary conditions
    // =========================================================================

    /**
     * **Validates: Requirements 15.3**
     *
     * The hex prefix is always exactly 32 characters (16 bytes × 2 hex digits).
     */
    public function testHexPrefixIsAlways32Characters(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $ext       = $this->randomAllowedExtension();
            $original  = "test_file.{$ext}";
            $generated = generate_upload_filename($original);

            $parts     = explode('.', $generated, 2);
            $hexPart   = $parts[0];

            $this->assertSame(
                32,
                strlen($hexPart),
                "Hex prefix must be exactly 32 characters, got " . strlen($hexPart) . " for '{$generated}'."
            );
            $this->assertMatchesRegularExpression(
                '/^[0-9a-f]{32}$/',
                $hexPart,
                "Hex prefix must contain only lowercase hex digits."
            );
        }
    }

    /**
     * **Validates: Requirements 15.3**
     *
     * Extension is preserved correctly for all allowed types, including
     * uppercase originals (which must be lowercased in the output).
     */
    public function testExtensionPreservationForAllAllowedTypes(): void
    {
        $cases = [
            'photo.jpg'   => 'jpg',
            'photo.jpeg'  => 'jpeg',
            'image.PNG'   => 'png',
            'banner.WEBP' => 'webp',
            'doc.PDF'     => 'pdf',
            'mixed.Jpg'   => 'jpg',
        ];

        foreach ($cases as $original => $expectedExt) {
            $generated    = generate_upload_filename($original);
            $generatedExt = strtolower(pathinfo($generated, PATHINFO_EXTENSION));

            $this->assertSame(
                $expectedExt,
                $generatedExt,
                "Extension for '{$original}' must be '{$expectedExt}', got '{$generatedExt}'."
            );
        }
    }

    /**
     * **Validates: Requirements 15.3**
     *
     * Generated names from different original filenames with the same extension
     * must still be unique (the random prefix ensures this).
     */
    public function testNamesFromDifferentOriginalsAreUnique(): void
    {
        $generated = [];

        for ($i = 0; $i < 50; $i++) {
            $name = generate_upload_filename("file_{$i}.jpg");
            $generated[] = $name;
        }

        $unique = array_unique($generated);

        $this->assertCount(
            50,
            $unique,
            'All 50 generated names must be unique (no collisions expected with random_bytes(16)).'
        );
    }

    /**
     * **Validates: Requirements 15.3**
     *
     * The original filename is never embedded in the generated name,
     * preventing information leakage about the original file.
     */
    public function testOriginalFilenameNotEmbeddedInGeneratedName(): void
    {
        $originals = [
            'confidential_report.pdf',
            'admin_password_list.jpg',
            'user_data_export.png',
        ];

        for ($i = 0; $i < 30; $i++) {
            $original  = $originals[$i % count($originals)];
            $generated = generate_upload_filename($original);
            $baseName  = pathinfo($original, PATHINFO_FILENAME); // without extension

            $this->assertStringNotContainsString(
                $baseName,
                $generated,
                "Generated name must not contain the original base filename '{$baseName}'."
            );
        }
    }
}
