<?php
/**
 * upload_handler.php — Manejo seguro de subida de archivos
 *
 * Función principal: handle_upload(array $file, string $type = 'image'): array
 *
 * Requisitos: 15.2, 15.3, 15.4
 */

if (!defined('UPLOAD_MAX_IMAGE_MB')) {
    require_once __DIR__ . '/../config.php';
}

/**
 * Maneja la subida de un archivo validando MIME, extensión y tamaño.
 *
 * @param array  $file  Entrada de $_FILES (e.g. $_FILES['imagen'])
 * @param string $type  'image' o 'pdf'
 * @return array ['success' => bool, 'path' => string, 'error' => string]
 */
function handle_upload(array $file, string $type = 'image'): array
{
    $result = ['success' => false, 'path' => '', 'error' => ''];

    // ── 1. Errores de PHP en la subida ─────────────────────────────────────────
    if (!isset($file['error'])) {
        $result['error'] = 'Estructura de archivo inválida.';
        return $result;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = _upload_error_message($file['error']);
        return $result;
    }

    // ── 2. Configuración según tipo ────────────────────────────────────────────
    if ($type === 'image') {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $allowedExts  = ['jpg', 'jpeg', 'png', 'webp'];
        $maxBytes     = UPLOAD_MAX_IMAGE_MB * 1024 * 1024;
        $destDir      = rtrim(UPLOADS_PATH, '/') . '/images/';
        $relDir       = 'uploads/images/';
    } elseif ($type === 'video') {
        $allowedMimes = ['video/mp4', 'video/webm', 'video/ogg'];
        $allowedExts  = ['mp4', 'webm', 'ogv', 'ogg'];
        $maxBytes     = 200 * 1024 * 1024; // 200 MB para videos
        $destDir      = rtrim(UPLOADS_PATH, '/') . '/videos/';
        $relDir       = 'uploads/videos/';
        // Crear directorio si no existe
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);
    } elseif ($type === 'pdf') {
        $allowedMimes = ['application/pdf'];
        $allowedExts  = ['pdf'];
        $maxBytes     = UPLOAD_MAX_PDF_MB * 1024 * 1024;
        $destDir      = rtrim(UPLOADS_PATH, '/') . '/pdfs/';
        $relDir       = 'uploads/pdfs/';
    } else {
        $result['error'] = 'Tipo de archivo no soportado: ' . htmlspecialchars($type);
        return $result;
    }

    // ── 3. Validar tamaño ──────────────────────────────────────────────────────
    if ($file['size'] > $maxBytes) {
        $limitMb = ($type === 'image') ? UPLOAD_MAX_IMAGE_MB : UPLOAD_MAX_PDF_MB;
        $result['error'] = "El archivo supera el límite permitido de {$limitMb} MB.";
        return $result;
    }

    // ── 4. Validar extensión ───────────────────────────────────────────────────
    $originalName = $file['name'] ?? '';
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExts, true)) {
        $result['error'] = 'Extensión de archivo no permitida: ' . htmlspecialchars($ext);
        return $result;
    }

    // ── 5. Validar MIME real con finfo ─────────────────────────────────────────
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if ($finfo === false) {
        $result['error'] = 'No se pudo inicializar la validación de tipo de archivo.';
        return $result;
    }

    $realMime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if ($realMime === false || !in_array($realMime, $allowedMimes, true)) {
        $result['error'] = 'El tipo de archivo no está permitido.';
        return $result;
    }

    // ── 6. Generar nombre del archivo ─────────────────────────────────────────
    if ($type === 'pdf') {
        // PDFs: conservar nombre original (sanitizado)
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $baseName = preg_replace('/[^a-zA-Z0-9_\-\. áéíóúñÁÉÍÓÚÑ]/', '_', $baseName);
        $baseName = trim($baseName, '_');
        if (empty($baseName)) $baseName = 'documento';
        $newName = $baseName . '.' . $ext;
        // Si ya existe, agregar sufijo
        $counter = 1;
        while (file_exists($destDir . $newName)) {
            $newName = $baseName . '_' . $counter . '.' . $ext;
            $counter++;
        }
    } else {
        // Imágenes: conservar nombre original sanitizado
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $baseName = preg_replace('/[^a-zA-Z0-9_\-\. áéíóúñÁÉÍÓÚÑ]/', '_', $baseName);
        $baseName = trim($baseName, '_');
        if (empty($baseName)) $baseName = 'imagen';
        $newName = $baseName . '.' . $ext;
        $counter = 1;
        while (file_exists($destDir . $newName)) {
            $newName = $baseName . '_' . $counter . '.' . $ext;
            $counter++;
        }
    }
    $destPath = $destDir . $newName;

    // ── 7. Crear directorio si no existe ──────────────────────────────────────
    if (!is_dir($destDir)) {
        if (!mkdir($destDir, 0755, true)) {
            _log_upload_error("No se pudo crear el directorio de destino: {$destDir}");
            $result['error'] = 'Error interno al preparar el directorio de subida.';
            return $result;
        }
    }

    // ── 8. Mover archivo ──────────────────────────────────────────────────────
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        _log_upload_error("Error al mover archivo subido a: {$destPath}");
        $result['error'] = 'Error al guardar el archivo en el servidor.';
        return $result;
    }

    // ── 9. Comprimir imagen (calidad alta, max 1920px ancho) ──────────────────
    if ($type === 'image') {
        _compress_image($destPath, $realMime);
    }

    $result['success'] = true;
    $result['path']    = $relDir . $newName;
    return $result;
}

/**
 * Traduce los códigos de error de PHP upload a mensajes legibles.
 */
function _upload_error_message(int $code): string
{
    $messages = [
        UPLOAD_ERR_INI_SIZE   => 'El archivo supera el límite upload_max_filesize de PHP.',
        UPLOAD_ERR_FORM_SIZE  => 'El archivo supera el límite MAX_FILE_SIZE del formulario.',
        UPLOAD_ERR_PARTIAL    => 'El archivo fue subido parcialmente.',
        UPLOAD_ERR_NO_FILE    => 'No se seleccionó ningún archivo.',
        UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal del servidor.',
        UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en disco.',
        UPLOAD_ERR_EXTENSION  => 'Una extensión de PHP detuvo la subida.',
    ];

    return $messages[$code] ?? 'Error desconocido al subir el archivo (código: ' . $code . ').';
}

/**
 * Registra un error de escritura en logs/upload_errors.log.
 */
function _log_upload_error(string $message): void
{
    $logFile = rtrim(LOGS_PATH, '/') . '/upload_errors.log';
    $line    = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;

    // Silenciar: si el log falla no queremos romper el flujo principal
    @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

/**
 * Comprime y redimensiona una imagen manteniendo buena calidad.
 * - Max ancho: 1920px (redimensiona proporcionalmente si es mayor)
 * - JPEG: calidad 85%
 * - PNG: compresión nivel 6
 * - WEBP: calidad 85%
 */
function _compress_image(string $path, string $mime): void
{
    $maxWidth = 1920;
    $jpegQuality = 85;
    $webpQuality = 85;
    $pngCompression = 6;

    try {
        switch ($mime) {
            case 'image/jpeg':
                $img = @imagecreatefromjpeg($path);
                break;
            case 'image/png':
                $img = @imagecreatefrompng($path);
                break;
            case 'image/webp':
                $img = @imagecreatefromwebp($path);
                break;
            default:
                return;
        }

        if (!$img) return;

        $origW = imagesx($img);
        $origH = imagesy($img);

        // Redimensionar solo si es más ancha que el máximo
        if ($origW > $maxWidth) {
            $newW = $maxWidth;
            $newH = (int) round($origH * ($maxWidth / $origW));

            $resized = imagecreatetruecolor($newW, $newH);

            // Preservar transparencia para PNG y WEBP
            if ($mime === 'image/png' || $mime === 'image/webp') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                imagefill($resized, 0, 0, $transparent);
            }

            imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($img);
            $img = $resized;
        }

        // Guardar con compresión
        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($img, $path, $jpegQuality);
                break;
            case 'image/png':
                imagealphablending($img, false);
                imagesavealpha($img, true);
                imagepng($img, $path, $pngCompression);
                break;
            case 'image/webp':
                imagewebp($img, $path, $webpQuality);
                break;
        }

        imagedestroy($img);
    } catch (\Throwable $e) {
        // Si falla la compresión, la imagen original queda intacta
        _log_upload_error('Compresión falló: ' . $e->getMessage());
    }
}
