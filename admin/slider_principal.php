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
require_once __DIR__ . '/slider_config_helper.php';

$pdo = get_db();
$autoplay_delay = get_slider_delay('slider_principal', 3200);

// Verificar si la columna link_url existe (para compatibilidad antes de migración)
$_cols = $pdo->query("SHOW COLUMNS FROM slider_principal LIKE 'link_url'")->fetchAll();
$has_link_url = !empty($_cols);

// Verificar si la columna tipo existe
$_cols_tipo = $pdo->query("SHOW COLUMNS FROM slider_principal LIKE 'tipo'")->fetchAll();
$has_tipo = !empty($_cols_tipo);

// ── Procesamiento POST ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';

    // ── REORDER (AJAX) — antes de CSRF ────────────────────────────────────────
    if ($action === 'reorder') {
        header('Content-Type: application/json');
        $order = $_POST['order'] ?? '';
        if (empty($order)) { echo json_encode(['success' => false, 'error' => 'Sin datos']); exit; }
        $ids = array_map('intval', explode(',', $order));
        try {
            $stmt = $pdo->prepare('UPDATE slider_principal SET orden = ? WHERE id = ?');
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
        header('Location: slider_principal');
        exit;
    }

    // ── SAVE CONFIG ───────────────────────────────────────────────────────────
    if ($action === 'save_config') {
        $delay = max(500, min(30000, (int)($_POST['autoplay_delay'] ?? 3200)));
        save_slider_delay('slider_principal', $delay);
        $_SESSION['flash_message'] = 'Configuración guardada.';
        $_SESSION['flash_type']    = 'success';
        header('Location: slider_principal'); exit;
    }

    // ── ADD: nueva imagen o video ──────────────────────────────────────────────
    if ($action === 'add') {
        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_message'] = 'Debe seleccionar un archivo.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: slider_principal');
            exit;
        }

        // Detectar si es video o imagen por extensión
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $es_video = in_array($ext, ['mp4', 'webm', 'ogv', 'ogg']);
        $upload_type = $es_video ? 'video' : 'image';
        $tipo_valor  = $es_video ? 'video' : 'imagen';

        $upload = handle_upload($_FILES['imagen'], $upload_type);

        if (!$upload['success']) {
            $_SESSION['flash_message'] = $upload['error'];
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_principal');
            exit;
        }

        try {
            $stmt = $pdo->prepare('SELECT COALESCE(MAX(orden), 0) + 1 AS next_orden FROM slider_principal');
            $stmt->execute();
            $nextOrden = (int) $stmt->fetchColumn();

            $link_url = trim($_POST['link_url'] ?? '') ?: null;

            if ($has_tipo && $has_link_url) {
                $stmt = $pdo->prepare('INSERT INTO slider_principal (imagen_path, tipo, orden, activo, link_url) VALUES (?, ?, ?, 1, ?)');
                $stmt->execute([$upload['path'], $tipo_valor, $nextOrden, $link_url]);
            } elseif ($has_link_url) {
                $stmt = $pdo->prepare('INSERT INTO slider_principal (imagen_path, orden, activo, link_url) VALUES (?, ?, 1, ?)');
                $stmt->execute([$upload['path'], $nextOrden, $link_url]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO slider_principal (imagen_path, orden, activo) VALUES (?, ?, 1)');
                $stmt->execute([$upload['path'], $nextOrden]);
            }

            $_SESSION['flash_message'] = $es_video ? 'Video agregado correctamente.' : 'Imagen agregada correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al guardar en la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: slider_principal');
        exit;
    }

    // ── EDIT: reemplazar imagen/video y/o link ────────────────────────────────
    if ($action === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_principal'); exit;
        }

        $stmt = $pdo->prepare($has_tipo
            ? 'SELECT imagen_path, tipo FROM slider_principal WHERE id = ?'
            : 'SELECT imagen_path FROM slider_principal WHERE id = ?');
        $stmt->execute([$id]);
        $old = $stmt->fetch();
        if (!isset($old['tipo'])) $old['tipo'] = 'imagen';

        if (!$old) {
            $_SESSION['flash_message'] = 'Registro no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_principal'); exit;
        }

        $link_url   = trim($_POST['link_url'] ?? '') ?: null;
        $new_imagen = null;
        $nuevo_tipo = null;

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            $ext_edit   = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $es_vid_edit = in_array($ext_edit, ['mp4', 'webm', 'ogv', 'ogg']);
            $upload_type_edit = $es_vid_edit ? 'video' : 'image';
            $nuevo_tipo = $es_vid_edit ? 'video' : 'imagen';

            $upload = handle_upload($_FILES['imagen'], $upload_type_edit);
            if (!$upload['success']) {
                $_SESSION['flash_message'] = $upload['error'];
                $_SESSION['flash_type']    = 'danger';
                header('Location: slider_principal'); exit;
            }
            $new_imagen = $upload['path'];
        }

        try {
            if ($new_imagen) {
                if ($has_tipo && $has_link_url) {
                    $pdo->prepare('UPDATE slider_principal SET imagen_path=?, tipo=?, link_url=? WHERE id=?')
                        ->execute([$new_imagen, $nuevo_tipo, $link_url, $id]);
                } elseif ($has_link_url) {
                    $pdo->prepare('UPDATE slider_principal SET imagen_path=?, link_url=? WHERE id=?')
                        ->execute([$new_imagen, $link_url, $id]);
                } else {
                    $pdo->prepare('UPDATE slider_principal SET imagen_path=? WHERE id=?')
                        ->execute([$new_imagen, $id]);
                }
                $oldFile = BASE_PATH . '/' . $old['imagen_path'];
                if (file_exists($oldFile)) unlink($oldFile);
                $_SESSION['flash_message'] = 'Archivo y configuración actualizados.';
            } else {
                if ($has_link_url) {
                    $pdo->prepare('UPDATE slider_principal SET link_url=? WHERE id=?')
                        ->execute([$link_url, $id]);
                }
                $_SESSION['flash_message'] = 'Configuración actualizada.';
            }
            $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al actualizar.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: slider_principal'); exit;
    }

    // ── DELETE: eliminar imagen ────────────────────────────────────────────────
    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID de imagen inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_principal');
            exit;
        }

        $stmt = $pdo->prepare('SELECT imagen_path FROM slider_principal WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            $_SESSION['flash_message'] = 'Registro no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: slider_principal');
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

        header('Location: slider_principal');
        exit;
    }
}

// ── Consultar slides actuales ──────────────────────────────────────────────────
if ($has_link_url && $has_tipo) {
    $select_slides = 'SELECT * FROM slider_principal ORDER BY orden ASC';
} elseif ($has_link_url) {
    $select_slides = "SELECT *, 'imagen' AS tipo FROM slider_principal ORDER BY orden ASC";
} elseif ($has_tipo) {
    $select_slides = 'SELECT *, NULL AS link_url FROM slider_principal ORDER BY orden ASC';
} else {
    $select_slides = "SELECT *, NULL AS link_url, 'imagen' AS tipo FROM slider_principal ORDER BY orden ASC";
}
$stmt = $pdo->query($select_slides);
$slides = $stmt->fetchAll();

// ── Páginas del menú disponibles para redirección ──────────────────────────────
$menu_pages = [
    ''                                          => '— Sin redirección —',
    'index'                                     => 'Inicio',
    'acerca-del-dif/presidencia'                => 'Acerca del DIF — Presidencia',
    'acerca-del-dif/direcciones'                => 'Acerca del DIF — Direcciones',
    'acerca-del-dif/organigrama'                => 'Acerca del DIF — Organigrama',
    'comunicacion-social/noticias'              => 'Comunicación Social — Noticias',
    'comunicacion-social/galeria'               => 'Comunicación Social — Galerías',
    'transparencia/SEAC'                        => 'Transparencia — SEAC',
    'transparencia/cuenta_publica'              => 'Transparencia — Cuenta Pública',
    'transparencia/presupuesto_anual'           => 'Transparencia — Presupuesto Anual',
    'transparencia/pae'                         => 'Transparencia — PAE',
    'transparencia/matrices_indicadores'        => 'Transparencia — Matrices de Indicadores',
    'transparencia/conac'                       => 'Transparencia — CONAC',
    'transparencia/financiero'                  => 'Transparencia — Financiero',
    'transparencia/avisos_privacidad'           => 'Transparencia — Avisos de Privacidad',
    'voluntariado'                              => 'Voluntariado',
    'autismo'                                   => 'Servicios — Unidad Municipal de Autismo',
    'mantenimiento'                             => 'Página de Mantenimiento',
];
// Agregar trámites dinámicamente
try {
    $tramites_nav = $pdo->query('SELECT slug, titulo FROM tramites ORDER BY id ASC')->fetchAll();
    foreach ($tramites_nav as $tn) {
        $menu_pages['tramites/' . $tn['slug']] = 'Servicios — ' . $tn['titulo'];
    }
} catch (Exception $e) {}


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
    <link rel="stylesheet" href="../css/admin.css?v=7">
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
                <span class="navbar-brand mb-0 h6">Slider Principal</span>
                <a href="logout" class="btn btn-sm btn-outline-danger ms-auto">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </nav>

            <div class="container-fluid p-4">
                <?php page_help('slider_principal'); ?>
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
                        <div class="card mb-3">
                            <div class="card-header bg-secondary text-white">
                                <i class="bi bi-stopwatch me-1"></i> Velocidad del Slider
                            </div>
                            <div class="card-body">
                                <form method="POST" action="slider_principal">
                                    <input type="hidden" name="action" value="save_config">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                    <label class="form-label fw-semibold">Tiempo entre imágenes</label>
                                    <div class="input-group mb-2">
                                        <input type="number" class="form-control" name="autoplay_delay"
                                               value="<?= $autoplay_delay ?>" min="500" max="30000" step="100" required>
                                        <span class="input-group-text">ms</span>
                                    </div>
                                    <small class="text-muted d-block mb-3">Ej: 3000 = 3 seg, 5000 = 5 seg</small>
                                    <button type="submit" class="btn btn-secondary w-100">
                                        <i class="bi bi-save me-1"></i> Guardar
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-plus-circle me-1"></i> Agregar imagen / video
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data" action="slider_principal">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                    <div class="mb-3">
                                        <label for="imagen" class="form-label">Imagen o Video</label>
                                        <input type="file" class="form-control" id="imagen" name="imagen"
                                               accept=".jpg,.jpeg,.png,.webp,.mp4,.webm,.ogv" required>
                                        <small class="text-muted">Imágenes: JPG, PNG, WEBP (máx. 20 MB)<br>Videos: MP4, WEBM (máx. 200 MB)</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="link_url_add" class="form-label">Redirección al hacer clic <small class="text-muted">(opcional)</small></label>
                                        <select class="form-select" id="link_url_add" name="link_url">
                                            <?php foreach ($menu_pages as $val => $label): ?>
                                            <option value="<?= htmlspecialchars($val) ?>"><?= htmlspecialchars($label) ?></option>
                                            <?php endforeach; ?>
                                        </select>
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
                            <div class="card-body" style="overflow:visible;">
                                <?php if (empty($slides)): ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-image" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">No hay imágenes registradas. Use el formulario para agregar una.</p>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted small mb-2"><i class="bi bi-arrows-move me-1"></i> Arrastra las imágenes para cambiar el orden</p>
                                    <div class="sortable-grid row g-2" id="sortableSlider">
                                        <?php foreach ($slides as $slide): ?>
                                        <div class="col-6 col-md-4 sortable-item" data-id="<?= (int)$slide['id'] ?>">
                                            <div class="card h-100 shadow-sm" style="cursor:grab;">
                                                <div class="position-relative">
                                                    <?php $es_vid = ($slide['tipo'] ?? 'imagen') === 'video'; ?>
                                                    <?php if ($es_vid): ?>
                                                    <video src="../<?= htmlspecialchars($slide['imagen_path']) ?>"
                                                           class="card-img-top" style="height:100px;object-fit:cover;background:#000;"
                                                           muted preload="metadata"></video>
                                                    <span class="position-absolute top-50 start-50 translate-middle" style="pointer-events:none;">
                                                        <i class="bi bi-play-circle-fill" style="font-size:1.8rem;color:rgba(255,255,255,.85);"></i>
                                                    </span>
                                                    <?php else: ?>
                                                    <img src="../<?= htmlspecialchars($slide['imagen_path']) ?>"
                                                         alt="Slide <?= (int)$slide['orden'] ?>"
                                                         class="card-img-top" style="height:100px;object-fit:contain;background:#f5f5f5;">
                                                    <?php endif; ?>
                                                    <span class="position-absolute top-0 start-0 badge bg-dark m-1 orden-badge"><?= (int)$slide['orden'] ?></span>
                                                    <?php if ($slide['activo']): ?>
                                                    <span class="position-absolute top-0 end-0 badge bg-success m-1">Activo</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="card-body p-1 text-center">
                                                    <div class="btn-group btn-group-sm w-100">
                                                        <button type="button" class="btn btn-sm btn-action-edit"
                                                                data-bs-toggle="modal" data-bs-target="#editModal<?= (int)$slide['id'] ?>">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-action-delete"
                                                                data-bs-toggle="modal" data-bs-target="#deleteModal<?= (int)$slide['id'] ?>">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales -->
    <?php foreach ($slides as $slide): ?>
    <!-- Modal Editar -->
    <div class="modal fade" id="editModal<?= (int)$slide['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data" action="slider_principal">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?= (int)$slide['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar imagen #<?= (int)$slide['id'] ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <?php if (($slide['tipo'] ?? 'imagen') === 'video'): ?>
                        <video src="../<?= htmlspecialchars($slide['imagen_path']) ?>" class="img-fluid rounded mb-3" style="max-height:200px;width:100%;" controls muted></video>
                        <?php else: ?>
                        <img src="../<?= htmlspecialchars($slide['imagen_path']) ?>" class="img-fluid rounded mb-3" style="max-height:200px;">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label">Nueva imagen o video <small class="text-muted">(opcional — dejar vacío para mantener el actual)</small></label>
                            <input type="file" class="form-control" name="imagen" accept=".jpg,.jpeg,.png,.webp,.mp4,.webm,.ogv">
                            <small class="text-muted">Imágenes: JPG, PNG, WEBP (máx. 20 MB) · Videos: MP4, WEBM (máx. 200 MB)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Redirección al hacer clic <small class="text-muted">(opcional)</small></label>
                            <select class="form-select" name="link_url">
                                <?php foreach ($menu_pages as $val => $label): ?>
                                <option value="<?= htmlspecialchars($val) ?>"<?= ($slide['link_url'] ?? '') === $val ? ' selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
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
    <div class="modal fade" id="deleteModal<?= (int)$slide['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="slider_principal">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int)$slide['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Confirmar eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de eliminar este elemento del slider?</p>
                        <?php if (($slide['tipo'] ?? 'imagen') === 'video'): ?>
                        <video src="../<?= htmlspecialchars($slide['imagen_path']) ?>" class="img-fluid rounded" style="max-height:150px;width:100%;" muted preload="metadata"></video>
                        <?php else: ?>
                        <img src="../<?= htmlspecialchars($slide['imagen_path']) ?>" class="img-fluid rounded" style="max-height:150px;">
                        <?php endif; ?>
                        <p class="text-muted small mt-2">Esta acción no se puede deshacer.</p>
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
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                sidebar.classList.add('collapsed');
            });
        }

        // Drag & drop reorder
        var grid = document.getElementById('sortableSlider');
        if (grid) {
            new Sortable(grid, {
                animation: 200,
                ghostClass: 'sortable-ghost',
                draggable: '.sortable-item',
                onEnd: function () {
                    var items = grid.querySelectorAll('.sortable-item');
                    var ids = [];
                    items.forEach(function (item, idx) {
                        ids.push(item.getAttribute('data-id'));
                        item.querySelector('.orden-badge').textContent = idx + 1;
                    });
                    var formData = new FormData();
                    formData.append('action', 'reorder');
                    formData.append('csrf_token', '<?= htmlspecialchars($token) ?>');
                    formData.append('order', ids.join(','));
                    fetch('slider_principal', { method: 'POST', body: formData })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            if (!data.success) console.error('Error al guardar orden');
                        });
                }
            });
        }
    </script>
</body>
</html>

