<?php
/**
 * admin/slider_comunica.php — CRUD para imágenes del Slider DIF Comunica
 * Organizado por mes: permite subir imágenes al mes actual y al siguiente.
 * El front muestra solo las del mes en curso.
 */

require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload_handler.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();

$meses_nombre = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];

$mes_actual  = (int) date('n');
$anio_actual = (int) date('Y');
$mes_sig     = $mes_actual === 12 ? 1 : $mes_actual + 1;
$anio_sig    = $mes_actual === 12 ? $anio_actual + 1 : $anio_actual;

// ── Procesamiento POST ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';

    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF inválido. Intente de nuevo.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: slider_comunica.php');
        exit;
    }

    // ── ADD ────────────────────────────────────────────────────────────────────
    if ($action === 'add') {
        $target_mes  = (int) ($_POST['mes'] ?? $mes_actual);
        $target_anio = (int) ($_POST['anio'] ?? $anio_actual);

        // Validar que sea mes actual o siguiente
        $valid_month = ($target_mes === $mes_actual && $target_anio === $anio_actual)
                    || ($target_mes === $mes_sig && $target_anio === $anio_sig);
        if (!$valid_month) {
            $_SESSION['flash_message'] = 'Solo puede subir imágenes al mes actual o al siguiente.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: slider_comunica.php');
            exit;
        }

        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_message'] = 'Debe seleccionar una imagen.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: slider_comunica.php');
            exit;
        }

        $upload = handle_upload($_FILES['imagen'], 'image');
        if (!$upload['success']) {
            $_SESSION['flash_message'] = $upload['error'];
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_comunica.php');
            exit;
        }

        try {
            $stmt = $pdo->prepare('SELECT COALESCE(MAX(orden), 0) + 1 FROM slider_comunica WHERE mes = ? AND anio = ?');
            $stmt->execute([$target_mes, $target_anio]);
            $nextOrden = (int) $stmt->fetchColumn();

            $stmt = $pdo->prepare(
                'INSERT INTO slider_comunica (imagen_path, orden, activo, mes, anio) VALUES (?, ?, 1, ?, ?)'
            );
            $stmt->execute([$upload['path'], $nextOrden, $target_mes, $target_anio]);

            $_SESSION['flash_message'] = 'Imagen agregada a ' . $meses_nombre[$target_mes] . ' ' . $target_anio . '.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al guardar.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: slider_comunica.php');
        exit;
    }

    // ── EDIT ───────────────────────────────────────────────────────────────────
    if ($action === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_comunica.php');
            exit;
        }

        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_message'] = 'Debe seleccionar una imagen para reemplazar.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: slider_comunica.php');
            exit;
        }

        $stmt = $pdo->prepare('SELECT imagen_path FROM slider_comunica WHERE id = ?');
        $stmt->execute([$id]);
        $old = $stmt->fetch();
        if (!$old) {
            $_SESSION['flash_message'] = 'Registro no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_comunica.php');
            exit;
        }

        $upload = handle_upload($_FILES['imagen'], 'image');
        if (!$upload['success']) {
            $_SESSION['flash_message'] = $upload['error'];
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_comunica.php');
            exit;
        }

        try {
            $stmt = $pdo->prepare('UPDATE slider_comunica SET imagen_path = ? WHERE id = ?');
            $stmt->execute([$upload['path'], $id]);
            $oldFile = BASE_PATH . '/' . $old['imagen_path'];
            if (file_exists($oldFile)) unlink($oldFile);
            $_SESSION['flash_message'] = 'Imagen reemplazada correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al actualizar.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: slider_comunica.php');
        exit;
    }

    // ── DELETE ──────────────────────────────────────────────────────────────────
    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_comunica.php');
            exit;
        }

        $stmt = $pdo->prepare('SELECT imagen_path FROM slider_comunica WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            $_SESSION['flash_message'] = 'Registro no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_comunica.php');
            exit;
        }

        try {
            $stmt = $pdo->prepare('DELETE FROM slider_comunica WHERE id = ?');
            $stmt->execute([$id]);
            $filePath = BASE_PATH . '/' . $row['imagen_path'];
            if (file_exists($filePath)) unlink($filePath);
            $_SESSION['flash_message'] = 'Imagen eliminada correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al eliminar.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: slider_comunica.php');
        exit;
    }
}

// ── Consultar imágenes organizadas por mes ─────────────────────────────────────
$stmt = $pdo->prepare(
    'SELECT * FROM slider_comunica WHERE (mes = ? AND anio = ?) OR (mes = ? AND anio = ?) ORDER BY anio DESC, mes DESC, orden ASC'
);
$stmt->execute([$mes_actual, $anio_actual, $mes_sig, $anio_sig]);
$all_slides = $stmt->fetchAll();

// Agrupar por mes/anio
$grouped = [];
foreach ($all_slides as $s) {
    $key = $s['anio'] . '-' . str_pad($s['mes'], 2, '0', STR_PAD_LEFT);
    $grouped[$key][] = $s;
}

// Asegurar que ambos meses aparezcan aunque estén vacíos
$key_actual = $anio_actual . '-' . str_pad($mes_actual, 2, '0', STR_PAD_LEFT);
$key_sig    = $anio_sig . '-' . str_pad($mes_sig, 2, '0', STR_PAD_LEFT);
if (!isset($grouped[$key_sig])) $grouped[$key_sig] = [];
if (!isset($grouped[$key_actual])) $grouped[$key_actual] = [];
krsort($grouped); // Mes siguiente primero, luego actual

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
    <title>Slider DIF Comunica — Panel de Administración DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css?v=5">
</head>
<body>
    <div class="d-flex">
        <?php require_once __DIR__ . '/sidebar_sections.php'; render_admin_sidebar($sidebar_groups, $current_admin_file); ?>

        <div class="main-content">
            <nav class="navbar navbar-light bg-white shadow-sm px-3">
                <button class="btn btn-outline-secondary me-2" id="toggleSidebar" aria-label="Abrir/cerrar menú">
                    <i class="bi bi-list"></i>
                </button>
                <span class="navbar-brand mb-0 h6">Slider DIF Comunica</span>
                <a href="logout.php" class="btn btn-sm btn-outline-danger ms-auto">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </nav>

            <div class="container-fluid p-4">
                <?php if ($flashMessage): ?>
                    <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($flashMessage) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                <?php endif; ?>

                <!-- Formulario de alta -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-plus-circle me-1"></i> Agregar imagen
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" action="slider_comunica.php" class="row g-3 align-items-end">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                            <div class="col-md-4">
                                <label for="mes_target" class="form-label">Mes destino</label>
                                <select class="form-select" id="mes_target" name="mes" required>
                                    <option value="<?= $mes_actual ?>" data-anio="<?= $anio_actual ?>" selected>
                                        <?= $meses_nombre[$mes_actual] ?> <?= $anio_actual ?> (actual)
                                    </option>
                                    <option value="<?= $mes_sig ?>" data-anio="<?= $anio_sig ?>">
                                        <?= $meses_nombre[$mes_sig] ?> <?= $anio_sig ?> (siguiente)
                                    </option>
                                </select>
                            </div>
                            <input type="hidden" name="anio" id="anio_hidden" value="<?= $anio_actual ?>">
                            <div class="col-md-5">
                                <label for="imagen" class="form-label">Imagen (JPG, PNG, WEBP — máx. 20 MB)</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" accept=".jpg,.jpeg,.png,.webp" required>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-upload me-1"></i> Subir imagen
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Imágenes por mes -->
                <?php foreach ($grouped as $key => $slides):
                    $parts = explode('-', $key);
                    $g_anio = (int) $parts[0];
                    $g_mes  = (int) $parts[1];
                    $is_current = ($g_mes === $mes_actual && $g_anio === $anio_actual);
                    $badge_class = $is_current ? 'bg-success' : 'bg-info';
                    $badge_text  = $is_current ? 'Mes actual — visible en el sitio' : 'Mes siguiente — se mostrará automáticamente';
                ?>
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <i class="bi bi-calendar-month me-1"></i>
                            <strong><?= $meses_nombre[$g_mes] ?> <?= $g_anio ?></strong>
                            <span class="badge <?= $badge_class ?> ms-2"><?= $badge_text ?></span>
                        </div>
                        <span class="badge bg-secondary"><?= count($slides) ?> imágenes</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($slides)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-image" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">No hay imágenes para este mes.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:60px;">Orden</th>
                                            <th style="width:100px;">Vista previa</th>
                                            <th>Ruta</th>
                                            <th style="width:60px;">Activo</th>
                                            <th style="width:180px;">Acciones</th>
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
                                            <td class="text-truncate" style="max-width:200px;">
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
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                        data-bs-toggle="modal" data-bs-target="#editModal<?= (int) $slide['id'] ?>">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal<?= (int) $slide['id'] ?>">
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Modal Editar -->
                                        <div class="modal fade" id="editModal<?= (int) $slide['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" enctype="multipart/form-data" action="slider_comunica.php">
                                                        <input type="hidden" name="action" value="edit">
                                                        <input type="hidden" name="id" value="<?= (int) $slide['id'] ?>">
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Reemplazar imagen #<?= (int) $slide['id'] ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Imagen actual:</p>
                                                            <img src="../<?= htmlspecialchars($slide['imagen_path']) ?>" class="img-fluid rounded mb-3" style="max-height:200px;">
                                                            <div class="mb-3">
                                                                <label class="form-label">Nueva imagen</label>
                                                                <input type="file" class="form-control" name="imagen" accept=".jpg,.jpeg,.png,.webp" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-warning"><i class="bi bi-pencil me-1"></i> Reemplazar</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Eliminar -->
                                        <div class="modal fade" id="deleteModal<?= (int) $slide['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" action="slider_comunica.php">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?= (int) $slide['id'] ?>">
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Confirmar eliminación</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>¿Eliminar esta imagen del slider?</p>
                                                            <img src="../<?= htmlspecialchars($slide['imagen_path']) ?>" class="img-fluid rounded" style="max-height:150px;">
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
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
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
        if (closeBtn) closeBtn.addEventListener('click', function () { sidebar.classList.add('collapsed'); });

        // Sincronizar año oculto con el select de mes
        document.getElementById('mes_target').addEventListener('change', function() {
            var opt = this.options[this.selectedIndex];
            document.getElementById('anio_hidden').value = opt.getAttribute('data-anio');
        });
    </script>
</body>
</html>
