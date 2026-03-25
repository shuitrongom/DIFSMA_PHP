<?php
/**
 * admin/tramites.php — Gestión de Trámites y Servicios
 *
 * Requisitos: 8.1, 8.2, 8.3
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
        header('Location: tramites.php');
        exit;
    }

    $slug = trim($_POST['slug'] ?? '');

    // Validar que el slug sea uno de los 6 permitidos
    $allowedSlugs = ['PMPNNA', 'DAAM', 'DANF', 'DAD', 'DPAF', 'DSJAIG'];
    if (!in_array($slug, $allowedSlugs, true)) {
        $_SESSION['flash_message'] = 'Trámite no válido.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: tramites.php');
        exit;
    }

    // Obtener registro actual
    $stmt = $pdo->prepare('SELECT * FROM tramites WHERE slug = ?');
    $stmt->execute([$slug]);
    $current = $stmt->fetch();

    if (!$current) {
        $_SESSION['flash_message'] = 'Trámite no encontrado en la base de datos.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: tramites.php');
        exit;
    }

    $imagenPath = $current['imagen_path'];
    $contenido  = $_POST['contenido'] ?? $current['contenido'];

    // Si se envió un nuevo archivo de imagen, procesarlo
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = handle_upload($_FILES['imagen'], 'image');

        if (!$upload['success']) {
            $_SESSION['flash_message'] = $upload['error'];
            $_SESSION['flash_type']    = 'danger';
            header('Location: tramites.php');
            exit;
        }

        // Eliminar imagen anterior si existe
        if (!empty($current['imagen_path'])) {
            $oldFile = BASE_PATH . '/' . $current['imagen_path'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        $imagenPath = $upload['path'];
    }

    try {
        $stmt = $pdo->prepare(
            'UPDATE tramites SET imagen_path = ?, contenido = ?, updated_at = NOW() WHERE slug = ?'
        );
        $stmt->execute([$imagenPath, $contenido, $slug]);

        $_SESSION['flash_message'] = 'Trámite "' . htmlspecialchars($current['titulo']) . '" actualizado correctamente.';
        $_SESSION['flash_type']    = 'success';
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al guardar en la base de datos.';
        $_SESSION['flash_type']    = 'danger';
    }

    header('Location: tramites.php');
    exit;
}

// ── Consultar todos los trámites ───────────────────────────────────────────────
$stmt = $pdo->query('SELECT * FROM tramites ORDER BY id ASC');
$tramites = $stmt->fetchAll();

// ── Flash messages ─────────────────────────────────────────────────────────────
$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType    = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);

// Generar token CSRF para los formularios
$token = csrf_token();

// Secciones del sidebar
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trámites y Servicios — Panel de Administración DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css?v=5">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/sidebar_sections.php'; render_admin_sidebar($sidebar_groups, $current_admin_file); ?>

        <!-- Main content -->
        <div class="main-content">
            <!-- Top bar -->
            <nav class="navbar navbar-light bg-white shadow-sm px-3">
                <button class="btn btn-outline-secondary me-2" id="toggleSidebar" aria-label="Abrir/cerrar menú">
                    <i class="bi bi-list"></i>
                </button>
                <span class="navbar-brand mb-0 h6">Trámites y Servicios</span>
                <a href="logout.php" class="btn btn-sm btn-outline-danger ms-auto">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </nav>

            <div class="container-fluid p-4">
                <!-- Flash message -->
                <?php if ($flashMessage): ?>
                    <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($flashMessage) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                <?php endif; ?>

                <!-- Listado de trámites -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-file-earmark-text me-1"></i> Trámites y Servicios
                        <span class="badge bg-secondary ms-1"><?= count($tramites) ?></span>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($tramites)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-file-earmark-text" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">No hay trámites registrados en la base de datos.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 80px;">Imagen</th>
                                            <th style="width: 100px;">Slug</th>
                                            <th>Título</th>
                                            <th style="width: 160px;">Última actualización</th>
                                            <th style="width: 120px;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tramites as $tramite): ?>
                                            <tr>
                                                <td>
                                                    <?php if (!empty($tramite['imagen_path'])): ?>
                                                        <img src="../<?= htmlspecialchars($tramite['imagen_path']) ?>"
                                                             alt="<?= htmlspecialchars($tramite['titulo']) ?>"
                                                             class="thumb-preview">
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-muted">Sin imagen</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary"><?= htmlspecialchars($tramite['slug']) ?></span>
                                                </td>
                                                <td><?= htmlspecialchars($tramite['titulo']) ?></td>
                                                <td>
                                                    <?php if (!empty($tramite['updated_at'])): ?>
                                                        <small class="text-muted">
                                                            <i class="bi bi-clock me-1"></i><?= htmlspecialchars($tramite['updated_at']) ?>
                                                        </small>
                                                    <?php else: ?>
                                                        <small class="text-muted">—</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-warning"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editModal<?= (int) $tramite['id'] ?>"
                                                            title="Editar">
                                                        <i class="bi bi-pencil"></i> Editar
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales de edición para cada trámite -->
    <?php foreach ($tramites as $tramite): ?>
        <div class="modal fade" id="editModal<?= (int) $tramite['id'] ?>" tabindex="-1" aria-labelledby="editLabel<?= (int) $tramite['id'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form method="POST" enctype="multipart/form-data" action="tramites.php">
                        <input type="hidden" name="slug" value="<?= htmlspecialchars($tramite['slug']) ?>">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editLabel<?= (int) $tramite['id'] ?>">
                                <i class="bi bi-pencil-square me-1"></i>
                                Editar: <?= htmlspecialchars($tramite['titulo']) ?>
                                <span class="badge bg-primary ms-2"><?= htmlspecialchars($tramite['slug']) ?></span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <!-- Imagen actual y campo de subida -->
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Imagen del trámite</label>
                                    <?php if (!empty($tramite['imagen_path'])): ?>
                                        <div class="mb-2">
                                            <img src="../<?= htmlspecialchars($tramite['imagen_path']) ?>"
                                                 alt="<?= htmlspecialchars($tramite['titulo']) ?>"
                                                 class="img-fluid rounded" style="max-height: 200px;">
                                            <small class="d-block text-muted mt-1"><?= htmlspecialchars($tramite['imagen_path']) ?></small>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted mb-2">
                                            <i class="bi bi-image" style="font-size: 3rem;"></i>
                                            <p class="small mb-0">Sin imagen — se usará la predeterminada</p>
                                        </div>
                                    <?php endif; ?>
                                    <label for="imagen<?= (int) $tramite['id'] ?>" class="form-label">
                                        Nueva imagen (JPG, PNG, WEBP — máx. 20 MB)
                                        <small class="text-muted d-block">Dejar vacío para conservar la actual</small>
                                    </label>
                                    <input type="file" class="form-control" id="imagen<?= (int) $tramite['id'] ?>"
                                           name="imagen" accept=".jpg,.jpeg,.png,.webp">
                                </div>
                                <!-- Editor de contenido HTML -->
                                <div class="col-md-8">
                                    <label for="contenido<?= (int) $tramite['id'] ?>" class="form-label fw-bold">
                                        Contenido HTML enriquecido
                                    </label>
                                    <textarea class="form-control tinymce-editor"
                                              id="contenido<?= (int) $tramite['id'] ?>"
                                              name="contenido"
                                              rows="15"><?= htmlspecialchars($tramite['contenido'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/upload-progress.js?v=10"></script>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        // Sidebar toggle
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

        // Initialize TinyMCE on all editors when their modal opens
        document.querySelectorAll('.modal').forEach(function (modal) {
            modal.addEventListener('shown.bs.modal', function () {
                const textarea = modal.querySelector('.tinymce-editor');
                if (textarea && !tinymce.get(textarea.id)) {
                    tinymce.init({
                        selector: '#' + textarea.id,
                        plugins: 'lists link image table code',
                        toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table | code | removeformat',
                        menubar: false,
                        height: 400,
                        branding: false,
                        promotion: false,
                        language: 'es',
                        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; }',
                        setup: function (editor) {
                            // Sync content back to textarea before form submit
                            editor.on('change', function () {
                                editor.save();
                            });
                        }
                    });
                }
            });

            // Destroy TinyMCE instance when modal closes to avoid stale editors
            modal.addEventListener('hidden.bs.modal', function () {
                const textarea = modal.querySelector('.tinymce-editor');
                if (textarea && tinymce.get(textarea.id)) {
                    tinymce.get(textarea.id).remove();
                }
            });
        });

        // Ensure TinyMCE content is saved before form submission
        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function () {
                tinymce.triggerSave();
            });
        });
    </script>
</body>
</html>
