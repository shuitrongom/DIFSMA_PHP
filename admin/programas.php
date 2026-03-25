<?php
/**
 * admin/programas.php — CRUD para "Nuestros Programas" (imagen + acordeón dinámico)
 *
 * Requisitos: 12.1, 12.2, 12.3, 12.4, 12.5
 */

require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload_handler.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();

// ── Procesamiento POST ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';

    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF inválido. Intente de nuevo.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: programas.php');
        exit;
    }

    // ── CREATE PROGRAM ─────────────────────────────────────────────────────────
    if ($action === 'create') {
        $nombre = trim($_POST['nombre'] ?? '');

        if (empty($nombre)) {
            $_SESSION['flash_message'] = 'El nombre del programa es obligatorio.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: programas.php');
            exit;
        }

        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_message'] = 'Debe seleccionar una imagen para el programa.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: programas.php');
            exit;
        }

        $upload = handle_upload($_FILES['imagen'], 'image');

        if (!$upload['success']) {
            $_SESSION['flash_message'] = $upload['error'];
            $_SESSION['flash_type']    = 'danger';
            header('Location: programas.php');
            exit;
        }

        // Validate accordion sections
        $secTitulos    = $_POST['sec_titulo'] ?? [];
        $secContenidos = $_POST['sec_contenido'] ?? [];

        $secciones = [];
        for ($i = 0, $len = count($secTitulos); $i < $len; $i++) {
            $t = trim($secTitulos[$i] ?? '');
            $c = trim($secContenidos[$i] ?? '');
            if ($t !== '' && $c !== '') {
                $secciones[] = ['titulo' => $t, 'contenido' => $c];
            }
        }

        if (empty($secciones)) {
            $_SESSION['flash_message'] = 'Debe agregar al menos una sección de acordeón con título y contenido.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: programas.php');
            exit;
        }

        try {
            $pdo->beginTransaction();

            // Get next orden
            $stmt = $pdo->query('SELECT COALESCE(MAX(orden), 0) + 1 FROM programas');
            $nextOrden = (int) $stmt->fetchColumn();

            $stmt = $pdo->prepare(
                'INSERT INTO programas (nombre, imagen_path, orden, activo) VALUES (?, ?, ?, 1)'
            );
            $stmt->execute([$nombre, $upload['path'], $nextOrden]);
            $programaId = (int) $pdo->lastInsertId();

            $stmtSec = $pdo->prepare(
                'INSERT INTO programas_secciones (programa_id, titulo, contenido, orden) VALUES (?, ?, ?, ?)'
            );
            foreach ($secciones as $idx => $sec) {
                $stmtSec->execute([$programaId, $sec['titulo'], $sec['contenido'], $idx]);
            }

            $pdo->commit();

            $_SESSION['flash_message'] = 'Programa creado correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al guardar en la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: programas.php');
        exit;
    }

    // ── EDIT PROGRAM ───────────────────────────────────────────────────────────
    if ($action === 'edit') {
        $id     = (int) ($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');

        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID de programa inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: programas.php');
            exit;
        }

        if (empty($nombre)) {
            $_SESSION['flash_message'] = 'El nombre del programa es obligatorio.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: programas.php');
            exit;
        }

        // Fetch current program
        $stmt = $pdo->prepare('SELECT * FROM programas WHERE id = ?');
        $stmt->execute([$id]);
        $old = $stmt->fetch();

        if (!$old) {
            $_SESSION['flash_message'] = 'Programa no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: programas.php');
            exit;
        }

        $newImagePath = $old['imagen_path'];

        // Handle optional image replacement
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = handle_upload($_FILES['imagen'], 'image');

            if (!$upload['success']) {
                $_SESSION['flash_message'] = $upload['error'];
                $_SESSION['flash_type']    = 'danger';
                header('Location: programas.php');
                exit;
            }

            $newImagePath = $upload['path'];
        }

        // Validate accordion sections
        $secTitulos    = $_POST['sec_titulo'] ?? [];
        $secContenidos = $_POST['sec_contenido'] ?? [];

        $secciones = [];
        for ($i = 0, $len = count($secTitulos); $i < $len; $i++) {
            $t = trim($secTitulos[$i] ?? '');
            $c = trim($secContenidos[$i] ?? '');
            if ($t !== '' && $c !== '') {
                $secciones[] = ['titulo' => $t, 'contenido' => $c];
            }
        }

        if (empty($secciones)) {
            $_SESSION['flash_message'] = 'Debe agregar al menos una sección de acordeón con título y contenido.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: programas.php');
            exit;
        }

        try {
            $pdo->beginTransaction();

            // Update program
            $stmt = $pdo->prepare('UPDATE programas SET nombre = ?, imagen_path = ? WHERE id = ?');
            $stmt->execute([$nombre, $newImagePath, $id]);

            // Replace sections: delete old, insert new
            $pdo->prepare('DELETE FROM programas_secciones WHERE programa_id = ?')->execute([$id]);

            $stmtSec = $pdo->prepare(
                'INSERT INTO programas_secciones (programa_id, titulo, contenido, orden) VALUES (?, ?, ?, ?)'
            );
            foreach ($secciones as $idx => $sec) {
                $stmtSec->execute([$id, $sec['titulo'], $sec['contenido'], $idx]);
            }

            $pdo->commit();

            // Delete old image if replaced
            if ($newImagePath !== $old['imagen_path'] && !empty($old['imagen_path'])) {
                $oldFile = BASE_PATH . '/' . $old['imagen_path'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $_SESSION['flash_message'] = 'Programa actualizado correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al actualizar en la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: programas.php');
        exit;
    }

    // ── DELETE PROGRAM ─────────────────────────────────────────────────────────
    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID de programa inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: programas.php');
            exit;
        }

        $stmt = $pdo->prepare('SELECT imagen_path FROM programas WHERE id = ?');
        $stmt->execute([$id]);
        $programa = $stmt->fetch();

        if (!$programa) {
            $_SESSION['flash_message'] = 'Programa no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: programas.php');
            exit;
        }

        try {
            // CASCADE will delete programas_secciones rows
            $stmt = $pdo->prepare('DELETE FROM programas WHERE id = ?');
            $stmt->execute([$id]);

            // Delete image file
            if (!empty($programa['imagen_path'])) {
                $filePath = BASE_PATH . '/' . $programa['imagen_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $_SESSION['flash_message'] = 'Programa y sus secciones eliminados correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al eliminar de la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: programas.php');
        exit;
    }
}

// ── Consultar datos ────────────────────────────────────────────────────────────
$programas = [];
$stmt = $pdo->query(
    'SELECT p.*, COUNT(s.id) AS num_secciones
     FROM programas p
     LEFT JOIN programas_secciones s ON s.programa_id = p.id
     GROUP BY p.id
     ORDER BY p.orden ASC, p.id ASC'
);
$programas = $stmt->fetchAll();

// ── Flash messages ─────────────────────────────────────────────────────────────
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
    <title>Programas — Panel de Administración DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css?v=7">
    <style>
        .section-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            background: #f8f9fa;
            position: relative;
        }
        .section-item .remove-section {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
        }
    </style>
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
                <span class="navbar-brand mb-0 h6">Nuestros Programas</span>
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

                <div class="row g-4">
                    <!-- Formulario crear programa -->
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-plus-circle me-1"></i> Crear programa
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data" action="programas.php" id="formCreate">
                                    <input type="hidden" name="action" value="create">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre del programa</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required
                                               placeholder="Ej: Programa de Asistencia Social">
                                    </div>
                                    <div class="mb-3">
                                        <label for="imagen" class="form-label">Imagen (JPG, PNG, WEBP — máx. 20 MB)</label>
                                        <input type="file" class="form-control" id="imagen" name="imagen" accept=".jpg,.jpeg,.png,.webp" required>
                                    </div>

                                    <hr>
                                    <h6><i class="bi bi-list-ul me-1"></i> Secciones de acordeón</h6>
                                    <p class="text-muted small">Agregue al menos una sección con título y contenido.</p>

                                    <div id="createSections">
                                        <div class="section-item">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-section" title="Eliminar sección" style="display:none;">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título de la sección</label>
                                                <input type="text" class="form-control form-control-sm" name="sec_titulo[]" required
                                                       placeholder="Ej: Objetivo">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Contenido</label>
                                                <textarea class="form-control form-control-sm" name="sec_contenido[]" rows="3" required
                                                          placeholder="Descripción de la sección..."></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-sm btn-outline-secondary mb-3" id="addCreateSection">
                                        <i class="bi bi-plus-circle me-1"></i> Agregar sección
                                    </button>

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-save me-1"></i> Crear programa
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Listado de programas -->
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-grid-3x3-gap me-1"></i> Programas registrados
                                <span class="badge bg-secondary ms-1"><?= count($programas) ?></span>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($programas)): ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-grid-3x3-gap" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">No hay programas registrados. Use el formulario para crear uno.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 80px;">Imagen</th>
                                                    <th>Nombre</th>
                                                    <th style="width: 90px;">Secciones</th>
                                                    <th style="width: 60px;">Activo</th>
                                                    <th style="width: 200px;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($programas as $prog): ?>
                                                    <tr>
                                                        <td>
                                                            <?php if (!empty($prog['imagen_path'])): ?>
                                                                <img src="../<?= htmlspecialchars($prog['imagen_path']) ?>"
                                                                     alt="<?= htmlspecialchars($prog['nombre']) ?>"
                                                                     class="thumb-preview">
                                                            <?php else: ?>
                                                                <span class="text-muted"><i class="bi bi-image"></i></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= htmlspecialchars($prog['nombre']) ?></td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info text-dark"><?= (int) $prog['num_secciones'] ?></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if ($prog['activo']): ?>
                                                                <span class="badge bg-success">Sí</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">No</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editModal<?= (int) $prog['id'] ?>"
                                                                    title="Editar">
                                                                <i class="bi bi-pencil"></i> Editar
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#deleteModal<?= (int) $prog['id'] ?>"
                                                                    title="Eliminar">
                                                                <i class="bi bi-trash"></i>
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

                <!-- ═══════════════════════════════════════════════════════════ -->
                <!-- MODALES DE EDICIÓN Y ELIMINACIÓN                           -->
                <!-- ═══════════════════════════════════════════════════════════ -->
                <?php foreach ($programas as $prog):
                    // Fetch sections for this program
                    $stmtSec = $pdo->prepare(
                        'SELECT * FROM programas_secciones WHERE programa_id = ? ORDER BY orden ASC'
                    );
                    $stmtSec->execute([$prog['id']]);
                    $progSecciones = $stmtSec->fetchAll();
                ?>

                <!-- Modal editar programa -->
                <div class="modal fade" id="editModal<?= (int) $prog['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form method="POST" enctype="multipart/form-data" action="programas.php">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" value="<?= (int) $prog['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title">
                                        <i class="bi bi-pencil-square me-1"></i> Editar programa
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Nombre del programa</label>
                                                <input type="text" class="form-control" name="nombre"
                                                       value="<?= htmlspecialchars($prog['nombre']) ?>" required>
                                            </div>
                                            <?php if (!empty($prog['imagen_path'])): ?>
                                                <div class="mb-3 text-center">
                                                    <img src="../<?= htmlspecialchars($prog['imagen_path']) ?>"
                                                         alt="Imagen actual" class="img-fluid rounded" style="max-height: 150px;">
                                                </div>
                                            <?php endif; ?>
                                            <div class="mb-3">
                                                <label class="form-label">Nueva imagen (opcional)</label>
                                                <input type="file" class="form-control" name="imagen" accept=".jpg,.jpeg,.png,.webp">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6><i class="bi bi-list-ul me-1"></i> Secciones de acordeón</h6>
                                            <div class="edit-sections" data-prog-id="<?= (int) $prog['id'] ?>">
                                                <?php foreach ($progSecciones as $sec): ?>
                                                    <div class="section-item">
                                                        <button type="button" class="btn btn-sm btn-outline-danger remove-section" title="Eliminar sección">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                        <div class="mb-2">
                                                            <label class="form-label small fw-bold">Título</label>
                                                            <input type="text" class="form-control form-control-sm" name="sec_titulo[]"
                                                                   value="<?= htmlspecialchars($sec['titulo']) ?>" required>
                                                        </div>
                                                        <div>
                                                            <label class="form-label small fw-bold">Contenido</label>
                                                            <textarea class="form-control form-control-sm" name="sec_contenido[]" rows="3" required><?= htmlspecialchars($sec['contenido']) ?></textarea>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary add-edit-section"
                                                    data-target="<?= (int) $prog['id'] ?>">
                                                <i class="bi bi-plus-circle me-1"></i> Agregar sección
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-pencil me-1"></i> Guardar cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal eliminar programa -->
                <div class="modal fade" id="deleteModal<?= (int) $prog['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="programas.php">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $prog['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title text-danger">
                                        <i class="bi bi-exclamation-triangle me-1"></i> Eliminar programa
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <p>¿Está seguro de eliminar el programa <strong><?= htmlspecialchars($prog['nombre']) ?></strong>?</p>
                                    <p class="text-danger small">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        Se eliminarán la imagen y todas las secciones (<?= (int) $prog['num_secciones'] ?>) asociadas. Esta acción no se puede deshacer.
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash me-1"></i> Eliminar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php endforeach; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/upload-progress.js?v=13"></script>
    <script>
        // ── Sidebar toggle ─────────────────────────────────────────────────────
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

        // ── Section template ───────────────────────────────────────────────────
        function createSectionHTML() {
            return `
                <div class="section-item">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-section" title="Eliminar sección">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Título de la sección</label>
                        <input type="text" class="form-control form-control-sm" name="sec_titulo[]" required
                               placeholder="Ej: Objetivo">
                    </div>
                    <div>
                        <label class="form-label small fw-bold">Contenido</label>
                        <textarea class="form-control form-control-sm" name="sec_contenido[]" rows="3" required
                                  placeholder="Descripción de la sección..."></textarea>
                    </div>
                </div>`;
        }

        // ── Add section to create form ─────────────────────────────────────────
        document.getElementById('addCreateSection').addEventListener('click', function () {
            const container = document.getElementById('createSections');
            container.insertAdjacentHTML('beforeend', createSectionHTML());
            updateRemoveButtons(container);
        });

        // ── Add section to edit modals ─────────────────────────────────────────
        document.querySelectorAll('.add-edit-section').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const progId = this.getAttribute('data-target');
                const container = document.querySelector('.edit-sections[data-prog-id="' + progId + '"]');
                container.insertAdjacentHTML('beforeend', createSectionHTML());
                updateRemoveButtons(container);
            });
        });

        // ── Remove section (delegated) ─────────────────────────────────────────
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-section');
            if (!btn) return;
            const item = btn.closest('.section-item');
            const container = item.parentElement;
            // Don't remove if it's the last section
            if (container.querySelectorAll('.section-item').length > 1) {
                item.remove();
                updateRemoveButtons(container);
            }
        });

        // ── Show/hide remove buttons (hide if only 1 section) ──────────────────
        function updateRemoveButtons(container) {
            const items = container.querySelectorAll('.section-item');
            items.forEach(function (item) {
                const btn = item.querySelector('.remove-section');
                if (btn) {
                    btn.style.display = items.length > 1 ? '' : 'none';
                }
            });
        }

        // Initialize remove button visibility
        document.querySelectorAll('#createSections, .edit-sections').forEach(function (container) {
            updateRemoveButtons(container);
        });
    </script>
</body>
</html>
