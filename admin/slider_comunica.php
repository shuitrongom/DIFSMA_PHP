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

    // ── REORDER (AJAX) — antes de CSRF para evitar redirect ────────────────
    if ($action === 'reorder') {
        header('Content-Type: application/json');
        $order = $_POST['order'] ?? '';
        if (empty($order)) {
            echo json_encode(['success' => false, 'error' => 'Sin datos']);
            exit;
        }
        $ids = array_map('intval', explode(',', $order));
        try {
            $stmt = $pdo->prepare('UPDATE slider_comunica SET orden = ? WHERE id = ?');
            foreach ($ids as $pos => $id) {
                if ($id > 0) $stmt->execute([$pos + 1, $id]);
            }
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Error BD']);
        }
        exit;
    }

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

        if (!isset($_FILES['imagenes']) || !is_array($_FILES['imagenes']['name'])) {
            $_SESSION['flash_message'] = 'Debe seleccionar al menos una imagen.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: slider_comunica.php');
            exit;
        }

        $fileCount = count($_FILES['imagenes']['name']);
        $uploaded = 0;
        $errors = [];

        // Obtener siguiente orden
        $stmt = $pdo->prepare('SELECT COALESCE(MAX(orden), 0) FROM slider_comunica WHERE mes = ? AND anio = ?');
        $stmt->execute([$target_mes, $target_anio]);
        $nextOrden = (int) $stmt->fetchColumn() + 1;

        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['imagenes']['error'][$i] === UPLOAD_ERR_NO_FILE) continue;

            // Construir array individual para handle_upload
            $singleFile = [
                'name'     => $_FILES['imagenes']['name'][$i],
                'type'     => $_FILES['imagenes']['type'][$i],
                'tmp_name' => $_FILES['imagenes']['tmp_name'][$i],
                'error'    => $_FILES['imagenes']['error'][$i],
                'size'     => $_FILES['imagenes']['size'][$i],
            ];

            $upload = handle_upload($singleFile, 'image');
            if (!$upload['success']) {
                $errors[] = $_FILES['imagenes']['name'][$i] . ': ' . $upload['error'];
                continue;
            }

            try {
                $stmt = $pdo->prepare(
                    'INSERT INTO slider_comunica (imagen_path, orden, activo, mes, anio) VALUES (?, ?, 1, ?, ?)'
                );
                $stmt->execute([$upload['path'], $nextOrden, $target_mes, $target_anio]);
                $nextOrden++;
                $uploaded++;
            } catch (PDOException $e) {
                $errors[] = $_FILES['imagenes']['name'][$i] . ': Error BD';
            }
        }

        if ($uploaded > 0) {
            $_SESSION['flash_message'] = $uploaded . ' imagen(es) agregada(s) a ' . $meses_nombre[$target_mes] . ' ' . $target_anio . '.';
            $_SESSION['flash_type']    = 'success';
        }
        if (!empty($errors)) {
            $_SESSION['flash_message'] = ($uploaded > 0 ? $_SESSION['flash_message'] . ' ' : '') . 'Errores: ' . implode(', ', $errors);
            $_SESSION['flash_type']    = $uploaded > 0 ? 'warning' : 'danger';
        }
        if ($uploaded === 0 && empty($errors)) {
            $_SESSION['flash_message'] = 'No se seleccionaron imágenes.';
            $_SESSION['flash_type']    = 'warning';
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

    // ── DELETE ALL (por mes) ────────────────────────────────────────────────────
    if ($action === 'delete_all') {
        $del_mes  = (int) ($_POST['mes'] ?? 0);
        $del_anio = (int) ($_POST['anio'] ?? 0);

        if ($del_mes <= 0 || $del_anio <= 0) {
            $_SESSION['flash_message'] = 'Datos de mes/año inválidos.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_comunica.php');
            exit;
        }

        try {
            // Obtener rutas para eliminar archivos
            $stmt = $pdo->prepare('SELECT imagen_path FROM slider_comunica WHERE mes = ? AND anio = ?');
            $stmt->execute([$del_mes, $del_anio]);
            $paths = $stmt->fetchAll();

            // Eliminar registros
            $stmt = $pdo->prepare('DELETE FROM slider_comunica WHERE mes = ? AND anio = ?');
            $stmt->execute([$del_mes, $del_anio]);

            // Eliminar archivos del servidor
            foreach ($paths as $p) {
                $filePath = BASE_PATH . '/' . $p['imagen_path'];
                if (file_exists($filePath)) unlink($filePath);
            }

            $_SESSION['flash_message'] = count($paths) . ' imagen(es) eliminada(s) de ' . ($meses_nombre[$del_mes] ?? $del_mes) . ' ' . $del_anio . '.';
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
    <link rel="stylesheet" href="../css/admin.css?v=7">
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
                                <label for="imagenes" class="form-label">Imágenes (JPG, PNG, WEBP — máx. 20 MB c/u)</label>
                                <input type="file" class="form-control" id="imagenes" name="imagenes[]" accept=".jpg,.jpeg,.png,.webp" multiple required>
                                <small class="text-muted">Puede seleccionar varias imágenes a la vez</small>
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
                        <div>
                            <span class="badge bg-secondary"><?= count($slides) ?> imágenes</span>
                            <?php if (!empty($slides)): ?>
                            <button type="button" class="btn btn-sm btn-action-delete ms-2" data-bs-toggle="modal" data-bs-target="#deleteAllModal<?= $g_mes ?>_<?= $g_anio ?>">
                                <i class="bi bi-trash3 me-1"></i> Eliminar todas
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body p-2" style="overflow:visible;">
                        <?php if (empty($slides)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-image" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">No hay imágenes para este mes.</p>
                            </div>
                        <?php else: ?>
                            <p class="text-muted small px-2 pt-2 mb-2"><i class="bi bi-arrows-move me-1"></i> Arrastra las imágenes para cambiar el orden</p>
                            <div class="sortable-grid row g-2 px-2 pb-2" data-mes="<?= $g_mes ?>" data-anio="<?= $g_anio ?>">
                                <?php foreach ($slides as $slide): ?>
                                <div class="col-6 col-md-3 sortable-item" data-id="<?= (int) $slide['id'] ?>">
                                    <div class="card h-100 shadow-sm" style="cursor:grab;">
                                        <div class="position-relative">
                                            <img src="../<?= htmlspecialchars($slide['imagen_path']) ?>"
                                                 alt="Slide <?= (int) $slide['orden'] ?>"
                                                 class="card-img-top" style="height:100px;object-fit:contain;background:#f5f5f5;">
                                            <span class="position-absolute top-0 start-0 badge bg-dark m-1 orden-badge"><?= (int) $slide['orden'] ?></span>
                                            <?php if ($slide['activo']): ?>
                                                <span class="position-absolute top-0 end-0 badge bg-success m-1">Activo</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-body p-1 text-center">
                                            <div class="btn-group btn-group-sm w-100">
                                                <button type="button" class="btn btn-sm btn-action-edit"
                                                        data-bs-toggle="modal" data-bs-target="#editModal<?= (int) $slide['id'] ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-action-delete"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal<?= (int) $slide['id'] ?>">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
                                                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash3 me-1"></i> Eliminar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Modal Eliminar Todas -->
                <?php if (!empty($slides)): ?>
                <div class="modal fade" id="deleteAllModal<?= $g_mes ?>_<?= $g_anio ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="slider_comunica.php">
                                <input type="hidden" name="action" value="delete_all">
                                <input type="hidden" name="mes" value="<?= $g_mes ?>">
                                <input type="hidden" name="anio" value="<?= $g_anio ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Eliminar todas las imágenes</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <p>¿Está seguro de eliminar <strong>todas las <?= count($slides) ?> imágenes</strong> de <strong><?= $meses_nombre[$g_mes] ?> <?= $g_anio ?></strong>?</p>
                                    <p class="text-danger small">Esta acción no se puede deshacer. Todos los archivos serán eliminados del servidor.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash3 me-1"></i> Eliminar todas</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="../js/upload-progress.js?v=13"></script>
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

        // Drag & drop reorder
        document.querySelectorAll('.sortable-grid').forEach(function(grid) {
            new Sortable(grid, {
                animation: 200,
                ghostClass: 'sortable-ghost',
                handle: '.sortable-item',
                draggable: '.sortable-item',
                onEnd: function() {
                    var items = grid.querySelectorAll('.sortable-item');
                    var ids = [];
                    items.forEach(function(item, idx) {
                        ids.push(item.getAttribute('data-id'));
                        item.querySelector('.orden-badge').textContent = idx + 1;
                    });

                    // Guardar orden vía AJAX
                    var formData = new FormData();
                    formData.append('action', 'reorder');
                    formData.append('csrf_token', '<?= htmlspecialchars($token) ?>');
                    formData.append('order', ids.join(','));

                    fetch('slider_comunica.php', { method: 'POST', body: formData })
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            if (!data.success) alert('Error al guardar orden');
                        })
                        .catch(function() { alert('Error de conexión'); });
                }
            });
        });
    </script>
    <style>
        .sortable-ghost { opacity: 0.4; }
        .sortable-item { transition: transform 0.15s; }
    </style>
</body>
</html>


