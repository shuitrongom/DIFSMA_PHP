<?php
/**
 * admin/presidencia.php — Gestión de datos de Presidencia
 *
 * Requisitos: 5.1, 5.2
 */

require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload_handler.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();

// ── Procesamiento POST ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF inválido. Intente de nuevo.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: presidencia');
        exit;
    }

    $nombre      = trim($_POST['nombre'] ?? '');
    $apellidos   = trim($_POST['apellidos'] ?? '');
    $cargo       = trim($_POST['cargo'] ?? '');
    $descripcion = $_POST['descripcion'] ?? '';

    if (empty($nombre) || empty($apellidos) || empty($cargo)) {
        $_SESSION['flash_message'] = 'El nombre, apellidos y cargo son obligatorios.';
        $_SESSION['flash_type']    = 'warning';
        header('Location: presidencia');
        exit;
    }

    // Obtener registro actual
    $stmt = $pdo->query('SELECT * FROM presidencia ORDER BY id ASC LIMIT 1');
    $current = $stmt->fetch();

    $imagenPath = $current ? $current['imagen_path'] : null;

    // Si se envió un nuevo archivo, procesarlo
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = handle_upload($_FILES['imagen'], 'image');

        if (!$upload['success']) {
            $_SESSION['flash_message'] = $upload['error'];
            $_SESSION['flash_type']    = 'danger';
            header('Location: presidencia');
            exit;
        }

        // Eliminar imagen anterior si existe
        if ($current && !empty($current['imagen_path'])) {
            $oldFile = BASE_PATH . '/' . $current['imagen_path'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        $imagenPath = $upload['path'];
    }

    try {
        if ($current) {
            $stmt = $pdo->prepare(
                'UPDATE presidencia SET nombre = ?, apellidos = ?, cargo = ?, descripcion = ?, imagen_path = ? WHERE id = ?'
            );
            $stmt->execute([$nombre, $apellidos, $cargo, $descripcion, $imagenPath, $current['id']]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO presidencia (nombre, apellidos, cargo, descripcion, imagen_path) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$nombre, $apellidos, $cargo, $descripcion, $imagenPath]);
        }

        $_SESSION['flash_message'] = 'Datos de presidencia actualizados correctamente.';
        $_SESSION['flash_type']    = 'success';
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al guardar en la base de datos.';
        $_SESSION['flash_type']    = 'danger';
    }

    header('Location: presidencia');
    exit;
}

// ── Consultar registro actual ──────────────────────────────────────────────────
$stmt = $pdo->query('SELECT * FROM presidencia ORDER BY id ASC LIMIT 1');
$presidencia = $stmt->fetch();

// ── Flash messages ─────────────────────────────────────────────────────────────
$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType    = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);

// Generar token CSRF para el formulario
$token = csrf_token();

// Secciones del sidebar (misma lista que dashboard)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presidencia — Panel de Administración DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css?v=7">
    <style>
        .img-preview { max-height: 200px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/sidebar_sections.php';
require_once __DIR__ . '/page_help.php'; render_admin_sidebar($sidebar_groups, $current_admin_file); ?>

        <!-- Main content -->
        <div class="main-content">
            <!-- Top bar -->
            <nav class="navbar navbar-light bg-white shadow-sm px-3">
                <button class="btn btn-outline-secondary me-2" id="toggleSidebar" aria-label="Abrir/cerrar menú">
                    <i class="bi bi-list"></i>
                </button>
                <span class="navbar-brand mb-0 h6">Presidencia</span>
                <a href="logout" class="btn btn-sm btn-outline-danger ms-auto">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </nav>

            <div class="container-fluid p-4">
                <?php page_help('presidencia'); ?>
                <!-- Flash message -->
                <?php if ($flashMessage): ?>
                    <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($flashMessage) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                <?php endif; ?>

                <div class="row g-4">
                    <!-- Vista previa actual -->
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-person-badge me-1"></i> Datos actuales
                            </div>
                            <div class="card-body text-center">
                                <?php if ($presidencia): ?>
                                    <?php
                                        $imgSrc = !empty($presidencia['imagen_path'])
                                            ? '../' . htmlspecialchars($presidencia['imagen_path'])
                                            : '../img/Presidente.png';
                                    ?>
                                    <img src="<?= $imgSrc ?>"
                                         alt="Imagen de presidencia"
                                         class="img-preview img-fluid mb-3">
                                    <h5 class="mb-0"><?= htmlspecialchars($presidencia['nombre']) ?></h5>
                                    <h5 class="mb-1"><?= htmlspecialchars($presidencia['apellidos'] ?? '') ?></h5>
                                    <p class="text-muted"><?= htmlspecialchars($presidencia['cargo']) ?></p>
                                    <?php if (!empty($presidencia['descripcion'])): ?>
                                        <div class="text-muted small"><?= $presidencia['descripcion'] ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($presidencia['updated_at'])): ?>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            Última actualización: <?= htmlspecialchars($presidencia['updated_at']) ?>
                                        </small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="text-muted py-4">
                                        <i class="bi bi-person-badge" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">No hay datos de presidencia registrados. Use el formulario para agregar.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de edición -->
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-pencil-square me-1"></i>
                                <?= $presidencia ? 'Editar datos de presidencia' : 'Registrar datos de presidencia' ?>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data" action="presidencia">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre(s)</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre"
                                               value="<?= $presidencia ? htmlspecialchars($presidencia['nombre']) : '' ?>"
                                               required maxlength="200"
                                               placeholder="Nombre(s)">
                                    </div>
                                    <div class="mb-3">
                                        <label for="apellidos" class="form-label">Apellidos</label>
                                        <input type="text" class="form-control" id="apellidos" name="apellidos"
                                               value="<?= $presidencia ? htmlspecialchars($presidencia['apellidos'] ?? '') : '' ?>"
                                               required maxlength="200"
                                               placeholder="Apellido paterno y materno">
                                    </div>
                                    <div class="mb-3">
                                        <label for="cargo" class="form-label">Cargo</label>
                                        <input type="text" class="form-control" id="cargo" name="cargo"
                                               value="<?= $presidencia ? htmlspecialchars($presidencia['cargo']) : '' ?>"
                                               required maxlength="200"
                                               placeholder="Cargo o título">
                                    </div>
                                    <div class="mb-3">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <textarea class="form-control tinymce-editor" id="descripcion" name="descripcion"
                                                  rows="6"
                                                  placeholder="Descripción o semblanza (opcional)"><?= $presidencia && !empty($presidencia['descripcion']) ? htmlspecialchars($presidencia['descripcion']) : '' ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="imagen" class="form-label">
                                            Imagen (JPG, PNG, WEBP — máx. 20 MB)
                                            <?php if ($presidencia): ?>
                                                <small class="text-muted">— dejar vacío para conservar la actual</small>
                                            <?php endif; ?>
                                        </label>
                                        <input type="file" class="form-control" id="imagen" name="imagen"
                                               accept=".jpg,.jpeg,.png,.webp"
                                               <?= !$presidencia ? 'required' : '' ?>>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-save me-1"></i>
                                        <?= $presidencia ? 'Guardar cambios' : 'Registrar' ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
    <script src="../js/upload-progress.js?v=13"></script>
    <script>
        tinymce.init({
            selector: '#descripcion',
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
        document.querySelector('form').addEventListener('submit', function() { tinymce.triggerSave(); });
        const sidebar = document.getElementById('sidebar');
        if (window.innerWidth <= 768) sidebar.classList.add('collapsed');
        document.getElementById('toggleSidebar').addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
        });
        const closeBtn = document.getElementById('closeSidebar');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                sidebar.classList.add('collapsed');
            });
        }
    </script>
</body>
</html>

