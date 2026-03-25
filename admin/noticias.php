<?php
/**
 * admin/noticias.php — CRUD para imágenes de Noticias por Día
 *
 * Requisitos: 4.1, 4.2, 4.3, 4.4
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
        header('Location: noticias.php');
        exit;
    }

    // ── ADD: nueva imagen de noticia ───────────────────────────────────────────
    if ($action === 'add') {
        $fechaNoticia = $_POST['fecha_noticia'] ?? '';

        if (empty($fechaNoticia)) {
            $_SESSION['flash_message'] = 'Debe seleccionar una fecha para la noticia.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: noticias.php');
            exit;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaNoticia)) {
            $_SESSION['flash_message'] = 'Formato de fecha inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: noticias.php');
            exit;
        }

        if ($fechaNoticia > date('Y-m-d')) {
            $_SESSION['flash_message'] = 'La fecha no puede ser mayor al día de hoy.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: noticias.php');
            exit;
        }

        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_message'] = 'Debe seleccionar una imagen.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: noticias.php');
            exit;
        }

        $upload = handle_upload($_FILES['imagen'], 'image');

        if (!$upload['success']) {
            $_SESSION['flash_message'] = $upload['error'];
            $_SESSION['flash_type']    = 'danger';
            header('Location: noticias.php');
            exit;
        }

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO noticias_imagenes (imagen_path, fecha_noticia, activo) VALUES (?, ?, 1)'
            );
            $stmt->execute([$upload['path'], $fechaNoticia]);

            $_SESSION['flash_message'] = 'Imagen de noticia agregada correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al guardar en la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: noticias.php');
        exit;
    }

    // ── EDIT: modificar imagen y/o fecha ───────────────────────────────────────
    if ($action === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID de noticia inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: noticias.php');
            exit;
        }

        $fechaNoticia = $_POST['fecha_noticia'] ?? '';

        if (empty($fechaNoticia) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaNoticia)) {
            $_SESSION['flash_message'] = 'Formato de fecha inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: noticias.php');
            exit;
        }

        if ($fechaNoticia > date('Y-m-d')) {
            $_SESSION['flash_message'] = 'La fecha no puede ser mayor al día de hoy.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: noticias.php');
            exit;
        }

        // Obtener registro actual
        $stmt = $pdo->prepare('SELECT imagen_path FROM noticias_imagenes WHERE id = ?');
        $stmt->execute([$id]);
        $old = $stmt->fetch();

        if (!$old) {
            $_SESSION['flash_message'] = 'Registro no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: noticias.php');
            exit;
        }

        $newPath = $old['imagen_path'];

        // Si se envió un nuevo archivo, procesarlo
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = handle_upload($_FILES['imagen'], 'image');

            if (!$upload['success']) {
                $_SESSION['flash_message'] = $upload['error'];
                $_SESSION['flash_type']    = 'danger';
                header('Location: noticias.php');
                exit;
            }

            $newPath = $upload['path'];
        }

        try {
            $stmt = $pdo->prepare(
                'UPDATE noticias_imagenes SET imagen_path = ?, fecha_noticia = ? WHERE id = ?'
            );
            $stmt->execute([$newPath, $fechaNoticia, $id]);

            // Si se reemplazó la imagen, eliminar la anterior
            if ($newPath !== $old['imagen_path']) {
                $oldFile = BASE_PATH . '/' . $old['imagen_path'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $_SESSION['flash_message'] = 'Noticia actualizada correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al actualizar en la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: noticias.php');
        exit;
    }

    // ── DELETE: eliminar imagen de noticia ──────────────────────────────────────
    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID de noticia inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: noticias.php');
            exit;
        }

        $stmt = $pdo->prepare('SELECT imagen_path FROM noticias_imagenes WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            $_SESSION['flash_message'] = 'Registro no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: noticias.php');
            exit;
        }

        try {
            $stmt = $pdo->prepare('DELETE FROM noticias_imagenes WHERE id = ?');
            $stmt->execute([$id]);

            // Eliminar archivo del servidor
            $filePath = BASE_PATH . '/' . $row['imagen_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $_SESSION['flash_message'] = 'Imagen de noticia eliminada correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al eliminar de la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: noticias.php');
        exit;
    }
}

// ── Consultar noticias actuales ────────────────────────────────────────────────
$stmt = $pdo->query('SELECT * FROM noticias_imagenes ORDER BY fecha_noticia DESC, id DESC');
$noticias = $stmt->fetchAll();

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
    <title>Noticias por Día — Panel de Administración DIF</title>
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
                <span class="navbar-brand mb-0 h6">Noticias por Día</span>
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
                                <i class="bi bi-plus-circle me-1"></i> Agregar imagen de noticia
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data" action="noticias.php">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                    <div class="mb-3">
                                        <label for="fecha_noticia" class="form-label">Fecha de la noticia</label>
                                        <input type="date" class="form-control" id="fecha_noticia" name="fecha_noticia" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
                                    </div>
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

                    <!-- Listado de noticias -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-newspaper me-1"></i> Imágenes de Noticias
                                <span class="badge bg-secondary ms-1"><?= count($noticias) ?></span>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($noticias)): ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-newspaper" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">No hay imágenes de noticias registradas. Use el formulario para agregar una.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 100px;">Vista previa</th>
                                                    <th style="width: 130px;">Fecha</th>
                                                    <th>Ruta</th>
                                                    <th style="width: 60px;">Activo</th>
                                                    <th style="width: 180px;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($noticias as $noticia): ?>
                                                    <tr>
                                                        <td>
                                                            <img src="../<?= htmlspecialchars($noticia['imagen_path']) ?>"
                                                                 alt="Noticia <?= htmlspecialchars($noticia['fecha_noticia']) ?>"
                                                                 class="thumb-preview">
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info text-dark">
                                                                <i class="bi bi-calendar-event me-1"></i>
                                                                <?= htmlspecialchars($noticia['fecha_noticia']) ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-truncate" style="max-width: 200px;">
                                                            <small class="text-muted"><?= htmlspecialchars($noticia['imagen_path']) ?></small>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if ($noticia['activo']): ?>
                                                                <span class="badge bg-success">Sí</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">No</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editModal<?= (int) $noticia['id'] ?>"
                                                                    title="Editar">
                                                                <i class="bi bi-pencil"></i> Editar
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#deleteModal<?= (int) $noticia['id'] ?>"
                                                                    title="Eliminar">
                                                                <i class="bi bi-trash"></i> Eliminar
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <!-- Modal Editar -->
                                                    <div class="modal fade" id="editModal<?= (int) $noticia['id'] ?>" tabindex="-1" aria-labelledby="editLabel<?= (int) $noticia['id'] ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form method="POST" enctype="multipart/form-data" action="noticias.php">
                                                                    <input type="hidden" name="action" value="edit">
                                                                    <input type="hidden" name="id" value="<?= (int) $noticia['id'] ?>">
                                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="editLabel<?= (int) $noticia['id'] ?>">
                                                                            Editar noticia #<?= (int) $noticia['id'] ?>
                                                                        </h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>Imagen actual:</p>
                                                                        <img src="../<?= htmlspecialchars($noticia['imagen_path']) ?>"
                                                                             alt="Noticia actual"
                                                                             class="img-fluid rounded mb-3" style="max-height: 200px;">
                                                                        <div class="mb-3">
                                                                            <label for="editFecha<?= (int) $noticia['id'] ?>" class="form-label">Fecha de la noticia</label>
                                                                            <input type="date" class="form-control" id="editFecha<?= (int) $noticia['id'] ?>" name="fecha_noticia" value="<?= htmlspecialchars($noticia['fecha_noticia']) ?>" max="<?= date('Y-m-d') ?>" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="editImagen<?= (int) $noticia['id'] ?>" class="form-label">Nueva imagen (opcional — dejar vacío para conservar la actual)</label>
                                                                            <input type="file" class="form-control" id="editImagen<?= (int) $noticia['id'] ?>" name="imagen" accept=".jpg,.jpeg,.png,.webp">
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

                                                    <!-- Modal Eliminar -->
                                                    <div class="modal fade" id="deleteModal<?= (int) $noticia['id'] ?>" tabindex="-1" aria-labelledby="deleteLabel<?= (int) $noticia['id'] ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form method="POST" action="noticias.php">
                                                                    <input type="hidden" name="action" value="delete">
                                                                    <input type="hidden" name="id" value="<?= (int) $noticia['id'] ?>">
                                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title text-danger" id="deleteLabel<?= (int) $noticia['id'] ?>">
                                                                            <i class="bi bi-exclamation-triangle me-1"></i> Confirmar eliminación
                                                                        </h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>¿Está seguro de eliminar esta imagen de noticia?</p>
                                                                        <img src="../<?= htmlspecialchars($noticia['imagen_path']) ?>"
                                                                             alt="Noticia a eliminar"
                                                                             class="img-fluid rounded" style="max-height: 150px;">
                                                                        <p class="text-muted small mt-2">
                                                                            Fecha: <?= htmlspecialchars($noticia['fecha_noticia']) ?><br>
                                                                            Esta acción no se puede deshacer. El archivo será eliminado del servidor.
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
    <script src="../js/upload-progress.js?v=6"></script>
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
