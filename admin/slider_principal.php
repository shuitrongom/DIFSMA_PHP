<?php
/**
 * admin/slider_principal.php — CRUD para imágenes del Slider Principal
 *
 * Requisitos: 2.1, 2.2, 2.3, 2.4
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
        header('Location: slider_principal.php');
        exit;
    }

    // ── ADD: nueva imagen ──────────────────────────────────────────────────────
    if ($action === 'add') {
        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_message'] = 'Debe seleccionar una imagen.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: slider_principal.php');
            exit;
        }

        $upload = handle_upload($_FILES['imagen'], 'image');

        if (!$upload['success']) {
            $_SESSION['flash_message'] = $upload['error'];
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_principal.php');
            exit;
        }

        try {
            $stmt = $pdo->prepare('SELECT COALESCE(MAX(orden), 0) + 1 AS next_orden FROM slider_principal');
            $stmt->execute();
            $nextOrden = (int) $stmt->fetchColumn();

            $stmt = $pdo->prepare(
                'INSERT INTO slider_principal (imagen_path, orden, activo) VALUES (?, ?, 1)'
            );
            $stmt->execute([$upload['path'], $nextOrden]);

            $_SESSION['flash_message'] = 'Imagen agregada correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al guardar en la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: slider_principal.php');
        exit;
    }

    // ── EDIT: reemplazar imagen ────────────────────────────────────────────────
    if ($action === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID de imagen inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_principal.php');
            exit;
        }

        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_message'] = 'Debe seleccionar una imagen para reemplazar.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: slider_principal.php');
            exit;
        }

        // Obtener ruta del archivo anterior
        $stmt = $pdo->prepare('SELECT imagen_path FROM slider_principal WHERE id = ?');
        $stmt->execute([$id]);
        $old = $stmt->fetch();

        if (!$old) {
            $_SESSION['flash_message'] = 'Registro no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_principal.php');
            exit;
        }

        $upload = handle_upload($_FILES['imagen'], 'image');

        if (!$upload['success']) {
            $_SESSION['flash_message'] = $upload['error'];
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_principal.php');
            exit;
        }

        try {
            $stmt = $pdo->prepare('UPDATE slider_principal SET imagen_path = ? WHERE id = ?');
            $stmt->execute([$upload['path'], $id]);

            // Eliminar archivo anterior
            $oldFile = BASE_PATH . '/' . $old['imagen_path'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }

            $_SESSION['flash_message'] = 'Imagen reemplazada correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al actualizar en la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: slider_principal.php');
        exit;
    }

    // ── DELETE: eliminar imagen ────────────────────────────────────────────────
    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID de imagen inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_principal.php');
            exit;
        }

        $stmt = $pdo->prepare('SELECT imagen_path FROM slider_principal WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            $_SESSION['flash_message'] = 'Registro no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_principal.php');
            exit;
        }

        try {
            $stmt = $pdo->prepare('DELETE FROM slider_principal WHERE id = ?');
            $stmt->execute([$id]);

            // Eliminar archivo del servidor
            $filePath = BASE_PATH . '/' . $row['imagen_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $_SESSION['flash_message'] = 'Imagen eliminada correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al eliminar de la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: slider_principal.php');
        exit;
    }
}

// ── Consultar imágenes actuales ────────────────────────────────────────────────
$stmt = $pdo->query('SELECT * FROM slider_principal ORDER BY orden ASC');
$slides = $stmt->fetchAll();

// ── Flash messages ─────────────────────────────────────────────────────────────
$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType    = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);

// Generar token CSRF para los formularios
$token = csrf_token();

// Secciones del sidebar (misma lista que dashboard)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slider Principal — Panel de Administración DIF</title>
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
                <span class="navbar-brand mb-0 h6">Slider Principal</span>
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
                    <!-- Formulario de alta -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-plus-circle me-1"></i> Agregar imagen
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data" action="slider_principal.php">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                    <div class="mb-3">
                                        <label for="imagen" class="form-label">Imagen (JPG, PNG, WEBP — máx. 20 MB)</label>
                                        <input type="file" class="form-control" id="imagen" name="imagen" accept=".jpg,.jpeg,.png,.webp" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-upload me-1"></i> Subir imagen
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Listado de imágenes -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-images me-1"></i> Imágenes del Slider Principal
                                <span class="badge bg-secondary ms-1"><?= count($slides) ?></span>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($slides)): ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-image" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">No hay imágenes registradas. Use el formulario para agregar una.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 60px;">Orden</th>
                                                    <th style="width: 100px;">Vista previa</th>
                                                    <th>Ruta</th>
                                                    <th style="width: 60px;">Activo</th>
                                                    <th style="width: 180px;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($slides as $slide): ?>
                                                    <tr>
                                                        <td class="text-center"><?= (int) $slide['orden'] ?></td>
                                                        <td>
                                                            <img src="../<?= htmlspecialchars($slide['imagen_path']) ?>"
                                                                 alt="Slide <?= (int) $slide['orden'] ?>"
                                                                 class="thumb-preview">
                                                        </td>
                                                        <td class="text-truncate" style="max-width: 200px;">
                                                            <small class="text-muted"><?= htmlspecialchars($slide['imagen_path']) ?></small>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if ($slide['activo']): ?>
                                                                <span class="badge bg-success">Sí</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">No</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <!-- Botón Editar (abre modal) -->
                                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editModal<?= (int) $slide['id'] ?>"
                                                                    title="Editar">
                                                                <i class="bi bi-pencil"></i> Editar
                                                            </button>
                                                            <!-- Botón Eliminar (abre modal de confirmación) -->
                                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#deleteModal<?= (int) $slide['id'] ?>"
                                                                    title="Eliminar">
                                                                <i class="bi bi-trash"></i> Eliminar
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <!-- Modal Editar -->
                                                    <div class="modal fade" id="editModal<?= (int) $slide['id'] ?>" tabindex="-1" aria-labelledby="editLabel<?= (int) $slide['id'] ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form method="POST" enctype="multipart/form-data" action="slider_principal.php">
                                                                    <input type="hidden" name="action" value="edit">
                                                                    <input type="hidden" name="id" value="<?= (int) $slide['id'] ?>">
                                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="editLabel<?= (int) $slide['id'] ?>">
                                                                            Reemplazar imagen #<?= (int) $slide['id'] ?>
                                                                        </h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>Imagen actual:</p>
                                                                        <img src="../<?= htmlspecialchars($slide['imagen_path']) ?>"
                                                                             alt="Slide actual"
                                                                             class="img-fluid rounded mb-3" style="max-height: 200px;">
                                                                        <div class="mb-3">
                                                                            <label for="editImagen<?= (int) $slide['id'] ?>" class="form-label">Nueva imagen</label>
                                                                            <input type="file" class="form-control" id="editImagen<?= (int) $slide['id'] ?>" name="imagen" accept=".jpg,.jpeg,.png,.webp" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                        <button type="submit" class="btn btn-warning">
                                                                            <i class="bi bi-pencil me-1"></i> Reemplazar
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Modal Eliminar -->
                                                    <div class="modal fade" id="deleteModal<?= (int) $slide['id'] ?>" tabindex="-1" aria-labelledby="deleteLabel<?= (int) $slide['id'] ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form method="POST" action="slider_principal.php">
                                                                    <input type="hidden" name="action" value="delete">
                                                                    <input type="hidden" name="id" value="<?= (int) $slide['id'] ?>">
                                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title text-danger" id="deleteLabel<?= (int) $slide['id'] ?>">
                                                                            <i class="bi bi-exclamation-triangle me-1"></i> Confirmar eliminación
                                                                        </h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>¿Está seguro de eliminar esta imagen del slider?</p>
                                                                        <img src="../<?= htmlspecialchars($slide['imagen_path']) ?>"
                                                                             alt="Slide a eliminar"
                                                                             class="img-fluid rounded" style="max-height: 150px;">
                                                                        <p class="text-muted small mt-2">Esta acción no se puede deshacer. El archivo será eliminado del servidor.</p>
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
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/upload-progress.js?v=9"></script>
    <script>
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
