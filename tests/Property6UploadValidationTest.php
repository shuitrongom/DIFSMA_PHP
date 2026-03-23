<?php
/**
 * Feature: dif-cms-php-migration, Property 6: validación de archivos — tipo y tamaño
 *
 * Validates: Requirements 2.2, 3.2, 4.2, 5.2, 6.2, 7.2, 8.2, 9.3, 11.3, 12.3, 15.2
 *
 * Para cualquier archivo con MIME inválido o tamaño > límite,
 * handle_upload() retorna success=false — 100 iteraciones.
 *
 * Strategy:
 *   - We cannot call handle_upload() directly because move_uploaded_file()
 *     only works with real PHP-uploaded files (SAPI check).
 *   - Instead we extract the three pure validation steps into testable helpers
 *     that mirror the exact logic in upload_handler.php, and verify each step
 *     independently with 100 property iterations.
 *   - For MIME validation we create real temp files with known binary signatures
 *     so finfo_file() returns a deterministic result.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// ── Bootstrap: load constants from config.php ─────────────────────────────────
if (!defined('UPLOAD_MAX_IMAGE_MB')) {
    require_once __DIR__ . '/../config.php';
}

// ── Pure validation helpers (mirror upload_handler.php logic) ─────────────────

/**
 * Returns true when $size exceeds the allowed limit for $type.
 *
 * Mirrors step 3 of handle_upload().
 */
function upload_size_exceeds_limit(int $size, string $type): bool
{
    if ($type === 'image') {
        $maxBytes = UPLOAD_MAX_IMAGE_MB * 1024 * 1024;
    } elseif ($type === 'pdf') {
        $maxBytes = UPLOAD_MAX_PDF_MB * 1024 * 1024;
    } else {
        return true; // unsupported type → reject
    }

    return $size > $maxBytes;
}

/**
 * Returns true when $ext is allowed for $type.
 *
 * Mirrors step 4 of handle_upload().
 */
function upload_extension_allowed(string $ext, string $type): bool
{
    if ($type === 'image') {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    } elseif ($type === 'pdf') {
        $allowed = ['pdf'];
    } else {
        return false;
    }

    return in_array(strtolower($ext), $allowed, true);
}

/**
 * Returns true when the real MIME of $tmpPath is allowed for $type.
 *
 * Mirrors step 5 of handle_upload().
 */
function upload_mime_allowed(string $tmpPath, string $type): bool
{
    if ($type === 'image') {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    } elseif ($type === 'pdf') {
        $allowedMimes = ['application/pdf'];
    } else {
        return false;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if ($finfo === false) {
        return false;
    }

    $realMime = finfo_file($finfo, $tmpPath);
    finfo_close($finfo);

    return $realMime !== false && in_array($realMime, $allowedMimes, true);
}

// ── Test class ────────────────────────────────────────────────────────────────

class Property6UploadValidationTest extends TestCase
{
    // ── Temp file tracking ────────────────────────────────────────────────────

    /** @var string[] */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $path) {
            if (file_exists($path)) {
                @unlink($path);
            }
        }
        $this->tempFiles = [];
    }

    // ── Generators ────────────────────────────────────────────────────────────

    /**
     * Returns a random MIME type that is NOT in the allowed list for images or PDFs.
     */
    private function randomInvalidMime(): string
    {
        $invalid = [
            'text/plain',
            'text/html',
            'application/javascript',
            'application/x-php',
            'application/x-httpd-php',
            'application/zip',
            'application/x-rar-compressed',
            'application/octet-stream',
            'image/gif',
            'image/bmp',
            'image/tiff',
            'image/svg+xml',
            'video/mp4',
            'audio/mpeg',
            'application/msword',
            'application/vnd.ms-excel',
            'application/x-sh',
            'application/x-executable',
        ];

        return $invalid[array_rand($invalid)];
    }

    /**
     * Returns a random file extension that is NOT allowed for images.
     */
    private function randomInvalidImageExtension(): string
    {
        $invalid = [
            'php', 'php3', 'php4', 'php5', 'phtml',
            'exe', 'sh', 'bat', 'cmd',
            'gif', 'bmp', 'tiff', 'svg', 'ico',
            'html', 'htm', 'js', 'css',
            'zip', 'rar', 'tar', 'gz',
            'doc', 'docx', 'xls', 'xlsx',
            'txt', 'csv', 'xml', 'json',
            'mp4', 'avi', 'mov',
            'mp3', 'wav',
            '',   // empty extension
        ];

        return $invalid[array_rand($invalid)];
    }

    /**
     * Returns a random file extension that is NOT allowed for PDFs.
     */
    private function randomInvalidPdfExtension(): string
    {
        $invalid = [
            'php', 'exe', 'sh',
            'jpg', 'jpeg', 'png', 'webp', 'gif',
            'doc', 'docx', 'xls',
            'txt', 'html', 'zip',
            '',
        ];

        return $invalid[array_rand($invalid)];
    }

    /**
     * Returns a size in bytes that exceeds the image limit (5 MB).
     * Range: 5 MB + 1 byte  to  50 MB.
     */
    private function randomOversizedImageBytes(): int
    {
        $limitBytes = UPLOAD_MAX_IMAGE_MB * 1024 * 1024;
        return random_int($limitBytes + 1, $limitBytes * 10);
    }

    /**
     * Returns a size in bytes that exceeds the PDF limit (20 MB).
     * Range: 20 MB + 1 byte  to  100 MB.
     */
    private function randomOversizedPdfBytes(): int
    {
        $limitBytes = UPLOAD_MAX_PDF_MB * 1024 * 1024;
        return random_int($limitBytes + 1, $limitBytes * 5);
    }

    /**
     * Returns a valid size in bytes for an image (1 byte to 5 MB).
     */
    private function randomValidImageBytes(): int
    {
        return random_int(1, UPLOAD_MAX_IMAGE_MB * 1024 * 1024);
    }

    /**
     * Creates a real temp file with the given binary content and returns its path.
     */
    private function createTempFile(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'dif_pbt_');
        file_put_contents($path, $content);
        $this->tempFiles[] = $path;
        return $path;
    }

    /**
     * Returns the binary magic bytes for a given MIME type.
     * Used to create temp files that finfo_file() will identify correctly.
     */
    private function magicBytesFor(string $mime): string
    {
        switch ($mime) {
            case 'image/jpeg':
                return "\xFF\xD8\xFF\xE0" . str_repeat("\x00", 16);
            case 'image/png':
                // Minimal valid PNG: signature + IHDR chunk (13 bytes data)
                // finfo needs the IHDR chunk to identify the file as image/png
                $sig  = "\x89PNG\r\n\x1A\n";
                $ihdr = "\x00\x00\x00\x0D" . "IHDR"
                      . "\x00\x00\x00\x01"  // width = 1
                      . "\x00\x00\x00\x01"  // height = 1
                      . "\x08"              // bit depth = 8
                      . "\x02"              // color type = RGB
                      . "\x00\x00\x00"      // compression, filter, interlace
                      . "\x90\x77\x53\xDE"; // CRC32 of IHDR data
                return $sig . $ihdr;
            case 'image/webp':
                // RIFF....WEBPVP8 .... — minimal lossy WebP container
                return "RIFF\x24\x00\x00\x00WEBPVP8 \x18\x00\x00\x00" . str_repeat("\x00", 16);
            case 'application/pdf':
                return "%PDF-1.4\n" . str_repeat("\x00", 16);
            default:
                return str_repeat("\x00", 32);
        }
    }

    // =========================================================================
    // Property 6a — oversized images are rejected (100 iterations)
    // =========================================================================

    /**
     * **Validates: Requirements 2.2, 3.2, 4.2, 5.2, 6.2, 7.2, 8.2, 9.3, 11.3, 12.3, 15.2**
     *
     * For any image file whose size exceeds UPLOAD_MAX_IMAGE_MB (5 MB),
     * the size validation MUST return true (exceeds limit → reject).
     */
    public function testProperty6a_OversizedImagesAreRejected(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $size = $this->randomOversizedImageBytes();

            if (!upload_size_exceeds_limit($size, 'image')) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 6a failed: oversized image was NOT rejected in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Property 6b — oversized PDFs are rejected (100 iterations)
    // =========================================================================

    /**
     * **Validates: Requirements 7.2, 11.3, 15.2**
     *
     * For any PDF file whose size exceeds UPLOAD_MAX_PDF_MB (20 MB),
     * the size validation MUST return true (exceeds limit → reject).
     */
    public function testProperty6b_OversizedPdfsAreRejected(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $size = $this->randomOversizedPdfBytes();

            if (!upload_size_exceeds_limit($size, 'pdf')) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 6b failed: oversized PDF was NOT rejected in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Property 6c — invalid image extensions are rejected (100 iterations)
    // =========================================================================

    /**
     * **Validates: Requirements 2.2, 3.2, 4.2, 5.2, 6.2, 7.2, 8.2, 9.3, 12.3, 15.2**
     *
     * For any file extension that is not in [jpg, jpeg, png, webp],
     * the extension validation MUST return false (not allowed → reject).
     */
    public function testProperty6c_InvalidImageExtensionsAreRejected(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $ext = $this->randomInvalidImageExtension();

            if (upload_extension_allowed($ext, 'image')) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 6c failed: invalid image extension was accepted in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Property 6d — invalid PDF extensions are rejected (100 iterations)
    // =========================================================================

    /**
     * **Validates: Requirements 7.2, 11.3, 15.2**
     *
     * For any file extension that is not 'pdf',
     * the extension validation MUST return false (not allowed → reject).
     */
    public function testProperty6d_InvalidPdfExtensionsAreRejected(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $ext = $this->randomInvalidPdfExtension();

            if (upload_extension_allowed($ext, 'pdf')) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 6d failed: invalid PDF extension was accepted in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Property 6e — files with invalid MIME are rejected by finfo (100 iter)
    // =========================================================================

    /**
     * **Validates: Requirements 2.2, 3.2, 4.2, 5.2, 6.2, 7.2, 8.2, 9.3, 11.3, 12.3, 15.2**
     *
     * For any temp file whose binary content does NOT match a valid image or PDF
     * magic signature, upload_mime_allowed() MUST return false.
     *
     * We create real temp files with plain text / null bytes so finfo detects
     * them as text/plain or application/octet-stream — both invalid.
     */
    public function testProperty6e_InvalidMimeFilesAreRejected(): void
    {
        $failures = 0;

        // Content patterns that finfo will NOT identify as jpeg/png/webp/pdf
        $invalidContents = [
            '<?php echo "hello"; ?>',                    // PHP source
            '<html><body>test</body></html>',            // HTML
            'GIF89a' . str_repeat("\x00", 20),           // GIF (not in allowed list)
            str_repeat("\x00", 32),                      // null bytes
            'PK' . str_repeat("\x00", 30),               // ZIP magic
            'MZ' . str_repeat("\x00", 30),               // EXE magic
            'This is a plain text file.',                // plain text
            '#!/bin/bash' . "\n" . 'echo hello',         // shell script
            str_repeat("A", 64),                         // arbitrary ASCII
            "\x47\x49\x46\x38\x39\x61" . str_repeat("\x00", 20), // GIF89a
        ];

        for ($i = 0; $i < 100; $i++) {
            $content = $invalidContents[$i % count($invalidContents)];
            $tmpPath = $this->createTempFile($content);

            // Test against both types — neither should accept these files
            $acceptedAsImage = upload_mime_allowed($tmpPath, 'image');
            $acceptedAsPdf   = upload_mime_allowed($tmpPath, 'pdf');

            if ($acceptedAsImage || $acceptedAsPdf) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 6e failed: file with invalid MIME was accepted in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Property 6f — valid MIME files pass MIME check (sanity / 100 iter)
    // =========================================================================

    /**
     * **Validates: Requirements 15.2**
     *
     * For any temp file whose binary content matches a valid magic signature,
     * upload_mime_allowed() MUST return true for the correct type.
     * This is the positive counterpart — valid files must NOT be rejected.
     */
    public function testProperty6f_ValidMimeFilesAreAccepted(): void
    {
        $validCases = [
            ['mime' => 'image/jpeg', 'type' => 'image'],
            ['mime' => 'image/png',  'type' => 'image'],
            ['mime' => 'image/webp', 'type' => 'image'],
            ['mime' => 'application/pdf', 'type' => 'pdf'],
        ];

        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $case    = $validCases[$i % count($validCases)];
            $content = $this->magicBytesFor($case['mime']);
            $tmpPath = $this->createTempFile($content);

            if (!upload_mime_allowed($tmpPath, $case['type'])) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 6f failed: valid MIME file was rejected in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Property 6g — valid sizes pass size check (sanity / 100 iter)
    // =========================================================================

    /**
     * **Validates: Requirements 15.2**
     *
     * For any image file whose size is within the 5 MB limit,
     * upload_size_exceeds_limit() MUST return false (within limit → accept).
     */
    public function testProperty6g_ValidSizesAreAccepted(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $size = $this->randomValidImageBytes();

            if (upload_size_exceeds_limit($size, 'image')) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 6g failed: valid-sized image was rejected in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Edge cases — specific boundary and security values
    // =========================================================================

    /**
     * **Validates: Requirements 15.2**
     *
     * Exact limit boundary: size == limit must be accepted; size == limit+1 rejected.
     */
    public function testSizeBoundaryConditions(): void
    {
        $imageLimit = UPLOAD_MAX_IMAGE_MB * 1024 * 1024;
        $pdfLimit   = UPLOAD_MAX_PDF_MB * 1024 * 1024;

        // Exactly at limit → accepted
        $this->assertFalse(
            upload_size_exceeds_limit($imageLimit, 'image'),
            'File exactly at image limit must be accepted.'
        );
        $this->assertFalse(
            upload_size_exceeds_limit($pdfLimit, 'pdf'),
            'File exactly at PDF limit must be accepted.'
        );

        // One byte over → rejected
        $this->assertTrue(
            upload_size_exceeds_limit($imageLimit + 1, 'image'),
            'File one byte over image limit must be rejected.'
        );
        $this->assertTrue(
            upload_size_exceeds_limit($pdfLimit + 1, 'pdf'),
            'File one byte over PDF limit must be rejected.'
        );

        // Zero bytes → accepted (empty file, size check passes; MIME check would catch it)
        $this->assertFalse(
            upload_size_exceeds_limit(0, 'image'),
            'Zero-byte file must pass size check (MIME check handles it).'
        );
    }

    /**
     * **Validates: Requirements 15.2**
     *
     * Extension check is case-insensitive: JPG, PNG, WEBP, PDF must all be accepted.
     */
    public function testExtensionCheckIsCaseInsensitive(): void
    {
        foreach (['jpg', 'JPG', 'Jpg', 'jpeg', 'JPEG', 'png', 'PNG', 'webp', 'WEBP'] as $ext) {
            $this->assertTrue(
                upload_extension_allowed($ext, 'image'),
                "Extension '{$ext}' must be accepted for images."
            );
        }

        foreach (['pdf', 'PDF', 'Pdf'] as $ext) {
            $this->assertTrue(
                upload_extension_allowed($ext, 'pdf'),
                "Extension '{$ext}' must be accepted for PDFs."
            );
        }
    }

    /**
     * **Validates: Requirements 15.2**
     *
     * Dangerous extensions (PHP, shell scripts, executables) must always be rejected
     * for both image and PDF types.
     */
    public function testDangerousExtensionsAlwaysRejected(): void
    {
        $dangerous = ['php', 'php3', 'php4', 'php5', 'phtml', 'sh', 'bash', 'exe', 'bat', 'cmd', 'py', 'rb'];

        foreach ($dangerous as $ext) {
            $this->assertFalse(
                upload_extension_allowed($ext, 'image'),
                "Dangerous extension '{$ext}' must be rejected for images."
            );
            $this->assertFalse(
                upload_extension_allowed($ext, 'pdf'),
                "Dangerous extension '{$ext}' must be rejected for PDFs."
            );
        }
    }

    /**
     * **Validates: Requirements 15.2**
     *
     * Unsupported $type must always reject size and extension checks.
     */
    public function testUnsupportedTypeAlwaysRejects(): void
    {
        $this->assertTrue(
            upload_size_exceeds_limit(1, 'video'),
            'Unsupported type must fail size check.'
        );
        $this->assertFalse(
            upload_extension_allowed('mp4', 'video'),
            'Unsupported type must fail extension check.'
        );
        $this->assertFalse(
            upload_extension_allowed('jpg', 'video'),
            'Unsupported type must fail extension check even for image extensions.'
        );
    }

    /**
     * **Validates: Requirements 15.2**
     *
     * A PHP file disguised with a .jpg extension must be rejected by MIME check
     * even though the extension passes.
     */
    public function testPhpFileDisguisedAsJpgIsRejectedByMime(): void
    {
        $phpContent = '<?php echo "malicious"; ?>';
        $tmpPath    = $this->createTempFile($phpContent);

        // Extension check would pass (jpg is allowed)
        $this->assertTrue(
            upload_extension_allowed('jpg', 'image'),
            'jpg extension must pass extension check.'
        );

        // But MIME check must catch it
        $this->assertFalse(
            upload_mime_allowed($tmpPath, 'image'),
            'PHP file disguised as .jpg must be rejected by MIME check.'
        );
    }
}
