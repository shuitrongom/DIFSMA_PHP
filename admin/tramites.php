<?php
/**
 * admin/tramites.php — Gestión de Trámites y Servicios
 * Permite crear, editar y eliminar trámites dinámicamente.
 */

require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload_handler.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'edit';
    $token  = $_POST['csrf_token'] ?? '';

    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF inválido.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: tramites.php');
        exit;
    }

    // ── CREATE ──────────────────────────────────────────────────────────────
    if ($action === 'create') {
        $titulo = trim($_POST['titulo'] ?? '');
        $slug   = trim($_POST['slug'] ?? '');

        if (empty($titulo) || empty($slug)) {
            $_SESSION['flash_message'] = 'El título y slug son obligatorios.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: tramites.php');
            exit;
        }

        // Sanitizar slug
        $slug = preg_replace('/[^a-zA-Z0-9_-]/', '', $slug);

        // Verificar que no exista
        $s = $pdo->prepare('SELECT id FROM tramites WHERE slug = ?');
        $s->execute([$slug]);
        if ($s->fetch()) {
            $_SESSION['flash_message'] = "Ya existe un trámite con el slug '{$slug}'.";
            $_SESSION['flash_type']    = 'warning';
            header('Location: tramites.php');
            exit;
        }

        $imagenPath = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = handle_upload($_FILES['imagen'], 'image');
            if (!$upload['success']) {
                $_SESSION['flash_message'] = $upload['error'];
                $_SESSION['flash_type']    = 'danger';
                header('Location: tramites.php');
                exit;
            }
            $imagenPath = $upload['path'];
        }

        $contenido = $_POST['contenido'] ?? '';

        try {
            $pdo->prepare('INSERT INTO tramites (slug, titulo, imagen_path, contenido) VALUES (?, ?, ?, ?)')->execute([$slug, $titulo, $imagenPath, $contenido]);

            // Crear archivo PHP para el trámite
            $phpFile = __DIR__ . '/../tramites/' . $slug . '.php';
            if (!file_exists($phpFile)) {
                $phpContent = "<?php\n\$tramite_slug  = '{$slug}';\n\$default_image = 'img/placeholder.jpg';\nrequire __DIR__ . '/_tramite_template.php';\n";
                file_put_contents($phpFile, $phpContent);
            }

            $_SESSION['flash_message'] = "Trámite '{$titulo}' creado correctamente.";
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al crear.';
            $_SESSION['flash_type']    = 'danger';
        }
        header('Location: tramites.php');
        exit;
    }

    // ── EDIT ────────────────────────────────────────────────────────────────
    if ($action === 'edit') {
        $id       = (int) ($_POST['id'] ?? 0);
        $titulo   = trim($_POST['titulo'] ?? '');
        $contenido = $_POST['contenido'] ?? '';

        if ($id <= 0 || empty($titulo)) {
            $_SESSION['flash_message'] = 'Datos inválidos.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: tramites.php');
            exit;
        }

        $stmt = $pdo->prepare('SELECT * FROM tramites WHERE id = ?');
        $stmt->execute([$id]);
        $current = $stmt->fetch();
        if (!$current) {
            $_SESSION['flash_message'] = 'Trámite no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: tramites.php');
            exit;
        }

        $imagenPath = $current['imagen_path'];
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = handle_upload($_FILES['imagen'], 'image');
            if (!$upload['success']) {
                $_SESSION['flash_message'] = $upload['error'];
                $_SESSION['flash_type']    = 'danger';
                header('Location: tramites.php');
                exit;
            }
            if (!empty($current['imagen_path'])) {
                $oldFile = BASE_PATH . '/' . $current['imagen_path'];
                if (file_exists($oldFile)) unlink($oldFile);
            }
            $imagenPath = $upload['path'];
        }

        try {
            $pdo->prepare('UPDATE tramites SET titulo = ?, imagen_path = ?, contenido = ?, updated_at = NOW() WHERE id = ?')
                ->execute([$titulo, $imagenPath, $contenido, $id]);
            $_SESSION['flash_message'] = "Trámite actualizado correctamente.";
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al guardar.';
            $_SESSION['flash_type']    = 'danger';
        }
        header('Location: tramites.php');
        exit;
    }

    // ── DELETE ──────────────────────────────────────────────────────────────
    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: tramites.php');
            exit;
        }

        $stmt = $pdo->prepare('SELECT * FROM tramites WHERE id = ?');
        $stmt->execute([$id]);
        $current = $stmt->fetch();
        if (!$current) {
            $_SESSION['flash_message'] = 'Trámite no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: tramites.php');
            exit;
        }

        try {
            // Eliminar imagen
            if (!empty($current['imagen_path'])) {
                $f = BASE_PATH . '/' . $current['imagen_path'];
                if (file_exists($f)) unlink($f);
            }
            $pdo->prepare('DELETE FROM tramites WHERE id = ?')->execute([$id]);

            // Eliminar archivo PHP si existe
            $phpFile = BASE_PATH . '/tramites/' . $current['slug'] . '.php';
            if (file_exists($phpFile)) unlink($phpFile);

            $_SESSION['flash_message'] = "Trámite '{$current['titulo']}' eliminado.";
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al eliminar.';
            $_SESSION['flash_type']    = 'danger';
        }
        header('Location: tramites.php');
        exit;
    }
}

// ── Consultar todos los trámites ───────────────────────────────────────────
$stmt = $pdo->query('SELECT * FROM tramites ORDER BY id ASC');
$tramites = $stmt->fetchAll();

$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType    = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);
$token = csrf_token();
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
    <link rel="stylesheet" href="../css/admin.css?v=7">
</head>
<body>
<div class="d-flex">
    <?php require_once __DIR__ . '/sidebar_sections.php'; render_admin_sidebar($sidebar_groups, $current_admin_file); ?>
    <div class="main-content">
        <nav class="navbar navbar-light bg-white shadow-sm px-3">
            <button class="btn btn-outline-secondary me-2" id="toggleSidebar" aria-label="Menú"><i class="bi bi-list"></i></button>
            <span class="navbar-brand mb-0 h6">Trámites y Servicios</span>
            <a href="logout.php" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-box-arrow-right"></i> Salir</a>
        </nav>
        <div class="container-fluid p-4">
            <?php if ($flashMessage): ?>
            <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($flashMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            <?php endif; ?>

            <!-- Formulario crear trámite -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white" style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#formNuevoTramite">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo trámite <i class="bi bi-chevron-down ms-2 small"></i>
                </div>
                <div class="collapse" id="formNuevoTramite">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" action="tramites.php">
                        <input type="hidden" name="action" value="create">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Título</label>
                                <input type="text" class="form-control" name="titulo" required placeholder="Ej: Dirección de Atención a la Mujer">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Slug (identificador único)</label>
                                <input type="text" class="form-control" name="slug" required placeholder="Ej: DAM" pattern="[a-zA-Z0-9_-]+">
                                <small class="text-muted">Solo letras, números, guiones</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Imagen (opcional)</label>
                                <input type="file" class="form-control" name="imagen" accept=".jpg,.jpeg,.png,.webp">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-circle me-1"></i> Crear</button>
                            </div>
                        </div>
                    </form>
                </div>
                </div>
            </div>

            <!-- Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-file-earmark-text me-1"></i> Trámites y Servicios
                    <span class="badge bg-secondary ms-1"><?= count($tramites) ?></span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($tramites)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-file-earmark-text" style="font-size:2rem;"></i>
                        <p class="mt-2 mb-0">No hay trámites registrados.</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:80px;">Imagen</th>
                                    <th style="width:100px;">Slug</th>
                                    <th>Título</th>
                                    <th style="width:160px;">Actualización</th>
                                    <th style="width:220px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($tramites as $tramite): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($tramite['imagen_path'])): ?>
                                        <img src="../<?= htmlspecialchars($tramite['imagen_path']) ?>" class="thumb-preview" alt="">
                                        <?php else: ?>
                                        <span class="badge bg-light text-muted">Sin imagen</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-primary"><?= htmlspecialchars($tramite['slug']) ?></span></td>
                                    <td class="text-start"><?= htmlspecialchars($tramite['titulo']) ?></td>
                                    <td><small class="text-muted"><?= htmlspecialchars($tramite['updated_at'] ?? '—') ?></small></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editM<?= (int)$tramite['id'] ?>"><i class="bi bi-pencil"></i> Editar</button>
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#delM<?= (int)$tramite['id'] ?>"><i class="bi bi-trash"></i></button>
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

<!-- Modales -->
<?php foreach ($tramites as $tramite): ?>
<!-- Modal Editar -->
<div class="modal fade" id="editM<?= (int)$tramite['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" action="tramites.php">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?= (int)$tramite['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-1"></i> Editar: <?= htmlspecialchars($tramite['titulo']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Título</label>
                                <input type="text" class="form-control" name="titulo" value="<?= htmlspecialchars($tramite['titulo']) ?>" required>
                            </div>
                            <?php if (!empty($tramite['imagen_path'])): ?>
                            <div class="mb-2">
                                <img src="../<?= htmlspecialchars($tramite['imagen_path']) ?>" class="img-fluid rounded" style="max-height:200px;">
                            </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label class="form-label">Nueva imagen (opcional)</label>
                                <input type="file" class="form-control" name="imagen" accept=".jpg,.jpeg,.png,.webp">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Contenido HTML</label>
                            <textarea class="form-control tinymce-editor" id="contenido<?= (int)$tramite['id'] ?>" name="contenido" rows="15"><?= htmlspecialchars($tramite['contenido'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Eliminar -->
<div class="modal fade" id="delM<?= (int)$tramite['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="tramites.php">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$tramite['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Eliminar trámite</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Eliminar <strong><?= htmlspecialchars($tramite['titulo']) ?></strong>?</p>
                    <p class="text-muted small">Se eliminará el registro, la imagen y el archivo PHP del trámite.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i> Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/upload-progress.js?v=13"></script>
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    if (window.innerWidth <= 768) sidebar.classList.add('collapsed');
    document.getElementById('toggleSidebar').addEventListener('click', function () { sidebar.classList.toggle('collapsed'); });
    const cb = document.getElementById('closeSidebar');
    if (cb) cb.addEventListener('click', function () { sidebar.classList.add('collapsed'); });

    // TinyMCE en modales
    document.querySelectorAll('.modal').forEach(function (modal) {
        modal.addEventListener('shown.bs.modal', function () {
            const ta = modal.querySelector('.tinymce-editor');
            if (ta && !tinymce.get(ta.id)) {
                tinymce.init({
                    selector: '#' + ta.id,
                    plugins: 'lists link image table code',
                    toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image table | code | removeformat',
                    menubar: false, height: 400, branding: false, promotion: false, language: 'es',
                    content_style: 'body { font-family: sans-serif; font-size: 14px; }',
                    setup: function (ed) { ed.on('change', function () { ed.save(); }); }
                });
            }
        });
        modal.addEventListener('hidden.bs.modal', function () {
            const ta = modal.querySelector('.tinymce-editor');
            if (ta && tinymce.get(ta.id)) tinymce.get(ta.id).remove();
        });
    });
    document.querySelectorAll('form').forEach(function (f) { f.addEventListener('submit', function () { tinymce.triggerSave(); }); });
</script>
</body>
</html>
