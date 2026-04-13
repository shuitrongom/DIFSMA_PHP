<?php
/**
 * admin/autismo.php — Gestión de la página Unidad Municipal de Autismo
 */
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload_handler.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';

    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF inválido.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: autismo'); exit;
    }

    // ── Guardar configuración de textos ───────────────────────────────────────
    if ($action === 'save_config') {
        $texto_derecha  = trim($_POST['texto_derecha']  ?? '');
        $texto_centro   = trim($_POST['texto_centro']   ?? '');
        $texto_inferior = trim($_POST['texto_inferior'] ?? '');

        $stmt = $pdo->query('SELECT id FROM autismo_config LIMIT 1');
        $current = $stmt->fetch();

        try {
            if ($current) {
                $pdo->prepare('UPDATE autismo_config SET texto_derecha=?, texto_centro=?, texto_inferior=?, updated_at=NOW() WHERE id=?')
                    ->execute([$texto_derecha, $texto_centro, $texto_inferior, $current['id']]);
            } else {
                $pdo->prepare('INSERT INTO autismo_config (texto_derecha, texto_centro, texto_inferior) VALUES (?,?,?)')
                    ->execute([$texto_derecha, $texto_centro, $texto_inferior]);
            }
            $_SESSION['flash_message'] = 'Textos actualizados correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = 'Error al guardar.';
            $_SESSION['flash_type']    = 'danger';
        }
        header('Location: autismo'); exit;
    }

    // ── Subir imagen (logo, centro, inferior) ─────────────────────────────────
    if ($action === 'upload_image') {
        $campo = $_POST['campo'] ?? '';
        $campos_validos = ['logo_path', 'imagen_centro_path', 'imagen_inferior_path'];
        if (!in_array($campo, $campos_validos)) {
            $_SESSION['flash_message'] = 'Campo inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: autismo'); exit;
        }
        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_message'] = 'Debe seleccionar una imagen.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: autismo'); exit;
        }
        if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            $php_errors = [
                UPLOAD_ERR_INI_SIZE  => 'La imagen supera el límite del servidor (máx. 20 MB).',
                UPLOAD_ERR_FORM_SIZE => 'La imagen supera el límite del formulario.',
                UPLOAD_ERR_PARTIAL   => 'La imagen se subió parcialmente. Intenta de nuevo.',
                UPLOAD_ERR_NO_TMP_DIR=> 'Error interno del servidor (sin carpeta temporal).',
                UPLOAD_ERR_CANT_WRITE=> 'No se pudo escribir el archivo en el servidor.',
            ];
            $_SESSION['flash_message'] = $php_errors[$_FILES['imagen']['error']] ?? 'Error al subir la imagen.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: autismo'); exit;
        }
        $upload = handle_upload($_FILES['imagen'], 'image');
        if (!$upload['success']) {
            $_SESSION['flash_message'] = $upload['error'];
            $_SESSION['flash_type']    = 'danger';
            header('Location: autismo'); exit;
        }
        $stmt = $pdo->query('SELECT * FROM autismo_config LIMIT 1');
        $current = $stmt->fetch();
        if ($current && !empty($current[$campo])) {
            $old = BASE_PATH . '/' . $current[$campo];
            if (file_exists($old)) unlink($old);
        }
        try {
            if ($current) {
                $pdo->prepare("UPDATE autismo_config SET {$campo}=?, updated_at=NOW() WHERE id=?")
                    ->execute([$upload['path'], $current['id']]);
            } else {
                $pdo->prepare("INSERT INTO autismo_config ({$campo}) VALUES (?)")
                    ->execute([$upload['path']]);
            }
            $_SESSION['flash_message'] = 'Imagen actualizada correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = 'Error al guardar imagen.';
            $_SESSION['flash_type']    = 'danger';
        }
        header('Location: autismo'); exit;
    }

    // ── Eliminar imagen ───────────────────────────────────────────────────────
    if ($action === 'delete_image') {
        $campo = $_POST['campo'] ?? '';
        $campos_validos = ['logo_path', 'imagen_centro_path', 'imagen_inferior_path'];
        if (!in_array($campo, $campos_validos)) {
            header('Location: autismo'); exit;
        }
        $stmt = $pdo->query('SELECT * FROM autismo_config LIMIT 1');
        $current = $stmt->fetch();
        if ($current && !empty($current[$campo])) {
            $old = BASE_PATH . '/' . $current[$campo];
            if (file_exists($old)) unlink($old);
            $pdo->prepare("UPDATE autismo_config SET {$campo}=NULL WHERE id=?")->execute([$current['id']]);
        }
        $_SESSION['flash_message'] = 'Imagen eliminada. Se usará la imagen por defecto.';
        $_SESSION['flash_type']    = 'success';
        header('Location: autismo'); exit;
    }
}

// ── Consultar config ──────────────────────────────────────────────────────────
$stmt   = $pdo->query('SELECT * FROM autismo_config LIMIT 1');
$config = $stmt->fetch();

$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType    = $_SESSION['flash_type']    ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);
$token = csrf_token();

// Helpers
function aut_img_card(string $campo, string $label, string $default, ?array $config): void {
    $path  = !empty($config[$campo]) ? $config[$campo] : null;
    $src   = $path ? '../' . htmlspecialchars($path) : htmlspecialchars($default);
    $token = csrf_token();
    $form_id = 'form_' . $campo;
    ?>
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-image me-1"></i> <?= htmlspecialchars($label) ?></div>
        <div class="card-body">
            <img src="<?= $src ?>" class="img-fluid rounded mb-3" style="max-height:200px;object-fit:contain;background:#f5f5f5;width:100%;" alt="<?= htmlspecialchars($label) ?>">
            <form method="POST" enctype="multipart/form-data" action="autismo" id="<?= $form_id ?>">
                <input type="hidden" name="action" value="upload_image">
                <input type="hidden" name="campo" value="<?= htmlspecialchars($campo) ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                <div class="input-group">
                    <input type="file" class="form-control form-control-sm" name="imagen" accept=".jpg,.jpeg,.png,.webp"
                           required onchange="validarTamanoImagen(this, '<?= $form_id ?>')">
                    <button type="submit" class="btn btn-sm btn-action-key"><i class="bi bi-upload"></i> Subir</button>
                </div>
                <div class="invalid-feedback d-block mt-1 text-danger small" id="err_<?= $campo ?>" style="display:none!important;"></div>
            </form>
            <?php if ($path): ?>
            <form method="POST" action="autismo" class="mt-2" onsubmit="return confirm('¿Eliminar imagen?')">
                <input type="hidden" name="action" value="delete_image">
                <input type="hidden" name="campo" value="<?= htmlspecialchars($campo) ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                <button type="submit" class="btn btn-sm btn-action-pdf-delete w-100"><i class="bi bi-image-x me-1"></i> Eliminar imagen</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unidad Municipal de Autismo — Panel Admin DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css?v=7">
</head>
<body>
<div class="d-flex">
    <?php
    require_once __DIR__ . '/sidebar_sections.php';
    require_once __DIR__ . '/page_help.php';
    render_admin_sidebar($sidebar_groups, $current_admin_file);
    ?>
    <div class="main-content">
        <nav class="navbar navbar-light bg-white shadow-sm px-3">
            <button class="btn btn-outline-secondary me-2" id="toggleSidebar"><i class="bi bi-list"></i></button>
            <span class="navbar-brand mb-0 h6">Unidad Municipal de Autismo</span>
            <a href="logout" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-box-arrow-right"></i> Salir</a>
        </nav>

        <div class="container-fluid p-4">
            <?php page_help('autismo'); ?>

            <?php if ($flashMessage): ?>
            <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($flashMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Columna izquierda: imágenes -->
                <div class="col-lg-4">
                    <?php aut_img_card('logo_path',            'Logo / Imagen principal (izquierda)', '../img/UMA_SMA.png',                                    $config); ?>
                    <?php aut_img_card('imagen_centro_path',   'Imagen central (derecha)',            '../img/front-view-boy-playing-memory-game.jpg',         $config); ?>
                    <?php aut_img_card('imagen_inferior_path', 'Imagen inferior (izquierda)',         '../img/top-view-kid-playing-with-colorful-game.jpg',    $config); ?>
                </div>

                <!-- Columna derecha: textos -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white"><i class="bi bi-pencil-square me-1"></i> Textos de contenido</div>
                        <div class="card-body">
                            <form method="POST" action="autismo">
                                <input type="hidden" name="action" value="save_config">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Texto derecha (junto al logo)</label>
                                    <small class="text-muted d-block mb-1">Aparece a la derecha del logo principal</small>
                                    <textarea class="form-control tinymce-editor" id="texto_derecha" name="texto_derecha" rows="5"><?= htmlspecialchars($config['texto_derecha'] ?? '') ?></textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Texto central (izquierda, junto a foto central)</label>
                                    <small class="text-muted d-block mb-1">Aparece a la izquierda de la imagen central</small>
                                    <textarea class="form-control tinymce-editor" id="texto_centro" name="texto_centro" rows="5"><?= htmlspecialchars($config['texto_centro'] ?? '') ?></textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Texto inferior (derecha, junto a foto inferior)</label>
                                    <small class="text-muted d-block mb-1">Aparece a la derecha de la imagen inferior</small>
                                    <textarea class="form-control tinymce-editor" id="texto_inferior" name="texto_inferior" rows="5"><?= htmlspecialchars($config['texto_inferior'] ?? '') ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-warning w-100"><i class="bi bi-save me-1"></i> Guardar textos</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../lib/tinymce/tinymce.min.js"></script>
<script src="../js/upload-progress.js?v=13"></script>
<script>
    tinymce.init({
        selector: '.tinymce-editor',
        plugins: 'lists link image table code fullscreen preview wordcount charmap hr pagebreak emoticons align',
        toolbar1: 'undo redo | cut copy paste | selectall | searchreplace | fullscreen preview',
        toolbar2: 'fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | removeformat',
        toolbar3: 'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | blockquote hr',
        toolbar4: 'link image table charmap emoticons | code | h1 h2 h3 h4 p',
        menubar: 'file edit view insert format tools table',
        height: 300,
        branding: false,
        promotion: false,
        language: 'es',
        font_family_formats:
            'Montserrat=Montserrat,sans-serif;' +
            'Arial=arial,helvetica,sans-serif;' +
            'Georgia=georgia,palatino;' +
            'Tahoma=tahoma,arial,helvetica,sans-serif;' +
            'Times New Roman=times new roman,times;' +
            'Verdana=verdana,geneva;' +
            'Courier New=courier new,courier,monospace;',
        font_size_formats: '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 24pt 28pt 32pt 36pt 48pt',
        content_style: 'body { font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1.6; color: #333; padding: 12px; } p { margin: 0 0 8px 0; }',
        content_css: false,
        resize: true,
        statusbar: true,
        setup: function(ed) { ed.on('change input keyup', function() { ed.save(); }); }
    });
    document.querySelectorAll('form').forEach(function(f) { f.addEventListener('submit', function() { tinymce.triggerSave(); }); });
    const sidebar = document.getElementById('sidebar');
    if (window.innerWidth <= 768) sidebar.classList.add('collapsed');
    document.getElementById('toggleSidebar').addEventListener('click', () => sidebar.classList.toggle('collapsed'));
    const cb = document.getElementById('closeSidebar');
    if (cb) cb.addEventListener('click', () => sidebar.classList.add('collapsed'));

    const MAX_MB = 20;
    const MAX_BYTES = MAX_MB * 1024 * 1024;

    function validarTamanoImagen(input, formId) {
        var campo = input.closest('form').querySelector('[name="campo"]').value;
        var errEl = document.getElementById('err_' + campo);
        var btn   = input.closest('form').querySelector('button[type="submit"]');

        if (input.files && input.files[0]) {
            if (input.files[0].size > MAX_BYTES) {
                var mb = (input.files[0].size / 1024 / 1024).toFixed(1);
                errEl.textContent = 'La imagen pesa ' + mb + ' MB. El máximo permitido es ' + MAX_MB + ' MB.';
                errEl.style.display = 'block';
                btn.disabled = true;
                input.value = '';
            } else {
                errEl.style.display = 'none';
                errEl.textContent = '';
                btn.disabled = false;
            }
        }
    }
</script>
</body>
</html>
