<?php
/**
 * admin/voluntariado.php — Gestión de la página de Voluntariado
 */
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload_handler.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();

// ── POST ────────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';

    // REORDER (AJAX) — antes de CSRF
    if ($action === 'reorder_images') {
        header('Content-Type: application/json');
        $order = $_POST['order'] ?? '';
        if (empty($order)) { echo json_encode(['success'=>false]); exit; }
        $ids = array_map('intval', explode(',', $order));
        try {
            $stmt = $pdo->prepare('UPDATE voluntariado_imagenes SET orden = ? WHERE id = ?');
            foreach ($ids as $pos => $id) { if ($id > 0) $stmt->execute([$pos+1, $id]); }
            echo json_encode(['success'=>true]);
        } catch (PDOException $e) { echo json_encode(['success'=>false]); }
        exit;
    }

    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF inválido.';
        $_SESSION['flash_type'] = 'danger';
        header('Location: voluntariado'); exit;
    }

    // UPLOAD LOGO (dedicado)
    if ($action === 'upload_logo') {
        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_message'] = 'Debe seleccionar una imagen.';
            $_SESSION['flash_type'] = 'warning';
            header('Location: voluntariado'); exit;
        }
        $upload = handle_upload($_FILES['logo'], 'image');
        if (!$upload['success']) {
            $_SESSION['flash_message'] = $upload['error'];
            $_SESSION['flash_type'] = 'danger';
            header('Location: voluntariado'); exit;
        }
        $stmt = $pdo->query('SELECT * FROM voluntariado_config LIMIT 1');
        $current = $stmt->fetch();
        if ($current && !empty($current['logo_path'])) {
            $old = BASE_PATH . '/' . $current['logo_path'];
            if (file_exists($old)) unlink($old);
        }
        if ($current) {
            $pdo->prepare('UPDATE voluntariado_config SET logo_path = ? WHERE id = ?')->execute([$upload['path'], $current['id']]);
        } else {
            $pdo->prepare('INSERT INTO voluntariado_config (logo_path) VALUES (?)')->execute([$upload['path']]);
        }
        $_SESSION['flash_message'] = 'Logo actualizado correctamente.';
        $_SESSION['flash_type'] = 'success';
        header('Location: voluntariado'); exit;
    }

    // DELETE LOGO
    if ($action === 'delete_logo') {
        $stmt = $pdo->query('SELECT * FROM voluntariado_config LIMIT 1');
        $current = $stmt->fetch();
        if ($current && !empty($current['logo_path'])) {
            $old = BASE_PATH . '/' . $current['logo_path'];
            if (file_exists($old)) unlink($old);
            $pdo->prepare('UPDATE voluntariado_config SET logo_path = NULL WHERE id = ?')->execute([$current['id']]);
        }
        $_SESSION['flash_message'] = 'Logo eliminado. Se usará la imagen por defecto.';
        $_SESSION['flash_type'] = 'success';
        header('Location: voluntariado'); exit;
    }

    // SAVE CONFIG
    if ($action === 'save_config') {
        $lema = trim($_POST['lema'] ?? '');
        $mision_titulo = trim($_POST['mision_titulo'] ?? '');
        $mision_texto = trim($_POST['mision_texto'] ?? '');
        $mision_subtitulo = trim($_POST['mision_subtitulo'] ?? '');
        $mision_subtexto = trim($_POST['mision_subtexto'] ?? '');
        $vision_texto = trim($_POST['vision_texto'] ?? '');
        $valores_texto = trim($_POST['valores_texto'] ?? '');

        $stmt = $pdo->query('SELECT * FROM voluntariado_config LIMIT 1');
        $current = $stmt->fetch();
        $logoPath = $current ? $current['logo_path'] : null;

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = handle_upload($_FILES['logo'], 'image');
            if ($upload['success']) {
                if ($current && !empty($current['logo_path'])) {
                    $old = BASE_PATH . '/' . $current['logo_path'];
                    if (file_exists($old)) unlink($old);
                }
                $logoPath = $upload['path'];
            }
        }

        try {
            if ($current) {
                $stmt = $pdo->prepare('UPDATE voluntariado_config SET logo_path=?, lema=?, mision_titulo=?, mision_texto=?, mision_subtitulo=?, mision_subtexto=?, vision_texto=?, valores_texto=? WHERE id=?');
                $stmt->execute([$logoPath, $lema, $mision_titulo, $mision_texto, $mision_subtitulo, $mision_subtexto, $vision_texto, $valores_texto, $current['id']]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO voluntariado_config (logo_path, lema, mision_titulo, mision_texto, mision_subtitulo, mision_subtexto, vision_texto, valores_texto) VALUES (?,?,?,?,?,?,?,?)');
                $stmt->execute([$logoPath, $lema, $mision_titulo, $mision_texto, $mision_subtitulo, $mision_subtexto, $vision_texto, $valores_texto]);
            }
            $_SESSION['flash_message'] = 'Configuración guardada.';
            $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al guardar.';
            $_SESSION['flash_type'] = 'danger';
        }
        header('Location: voluntariado'); exit;
    }

    // ADD IMAGES (multi-upload)
    if ($action === 'add_images') {
        if (!isset($_FILES['imagenes']) || !is_array($_FILES['imagenes']['name'])) {
            $_SESSION['flash_message'] = 'Seleccione al menos una imagen.';
            $_SESSION['flash_type'] = 'warning';
            header('Location: voluntariado'); exit;
        }
        $count = count($_FILES['imagenes']['name']);
        $uploaded = 0;
        $stmt = $pdo->query('SELECT COALESCE(MAX(orden),0) FROM voluntariado_imagenes');
        $nextOrden = (int) $stmt->fetchColumn() + 1;

        for ($i = 0; $i < $count; $i++) {
            if ($_FILES['imagenes']['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
            $f = ['name'=>$_FILES['imagenes']['name'][$i],'type'=>$_FILES['imagenes']['type'][$i],'tmp_name'=>$_FILES['imagenes']['tmp_name'][$i],'error'=>$_FILES['imagenes']['error'][$i],'size'=>$_FILES['imagenes']['size'][$i]];
            $upload = handle_upload($f, 'image');
            if (!$upload['success']) continue;
            try {
                $pdo->prepare('INSERT INTO voluntariado_imagenes (imagen_path, orden, activo) VALUES (?,?,1)')->execute([$upload['path'], $nextOrden++]);
                $uploaded++;
            } catch (PDOException $e) {}
        }
        $_SESSION['flash_message'] = $uploaded . ' imagen(es) agregada(s).';
        $_SESSION['flash_type'] = $uploaded > 0 ? 'success' : 'warning';
        header('Location: voluntariado'); exit;
    }

    // DELETE IMAGE
    if ($action === 'delete_image') {
        $id = (int) ($_POST['image_id'] ?? 0);
        $stmt = $pdo->prepare('SELECT imagen_path FROM voluntariado_imagenes WHERE id=?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            $pdo->prepare('DELETE FROM voluntariado_imagenes WHERE id=?')->execute([$id]);
            $f = BASE_PATH . '/' . $row['imagen_path'];
            if (file_exists($f)) unlink($f);
            $_SESSION['flash_message'] = 'Imagen eliminada.';
            $_SESSION['flash_type'] = 'success';
        }
        header('Location: voluntariado'); exit;
    }

    // DELETE ALL IMAGES
    if ($action === 'delete_all_images') {
        $stmt = $pdo->query('SELECT imagen_path FROM voluntariado_imagenes');
        $paths = $stmt->fetchAll();
        $pdo->exec('DELETE FROM voluntariado_imagenes');
        foreach ($paths as $p) { $f = BASE_PATH . '/' . $p['imagen_path']; if (file_exists($f)) unlink($f); }
        $_SESSION['flash_message'] = count($paths) . ' imagen(es) eliminada(s).';
        $_SESSION['flash_type'] = 'success';
        header('Location: voluntariado'); exit;
    }
}

// ── Consultar datos ─────────────────────────────────────────────────────────────
$stmt = $pdo->query('SELECT * FROM voluntariado_config LIMIT 1');
$config = $stmt->fetch();
$stmt = $pdo->query('SELECT * FROM voluntariado_imagenes ORDER BY orden ASC');
$imagenes = $stmt->fetchAll();

$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);
$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voluntariado — Panel de Administración DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css?v=7">
</head>
<body>
    <div class="d-flex">
        <?php require_once __DIR__ . '/sidebar_sections.php';
require_once __DIR__ . '/page_help.php'; render_admin_sidebar($sidebar_groups, $current_admin_file); ?>
        <div class="main-content">
            <nav class="navbar navbar-light bg-white shadow-sm px-3">
                <button class="btn btn-outline-secondary me-2" id="toggleSidebar"><i class="bi bi-list"></i></button>
                <span class="navbar-brand mb-0 h6">Voluntariado</span>
                <a href="logout" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-box-arrow-right"></i> Salir</a>
            </nav>
            <div class="container-fluid p-4">
                <?php page_help('voluntariado'); ?>
                <?php if ($flashMessage): ?>
                <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show">
                    <?= htmlspecialchars($flashMessage) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Logo del voluntariado -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><i class="bi bi-image me-1"></i> Logo del voluntariado</div>
                    <div class="card-body">
                        <?php if (!empty($config['logo_path'])): ?>
                        <div class="d-flex align-items-center gap-3 mb-3 p-2 border rounded" style="background:#f8f9fa;">
                            <img src="../<?= htmlspecialchars($config['logo_path']) ?>" class="img-fluid" style="max-height:100px;">
                            <div>
                                <span class="badge bg-success mb-1">Logo actual</span><br>
                                <form method="POST" action="voluntariado" class="d-inline" onsubmit="return confirm('¿Eliminar el logo?')">
                                    <input type="hidden" name="action" value="delete_logo">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                    <button type="submit" class="btn btn-sm btn-action-delete mt-1"><i class="bi bi-trash3 me-1"></i> Eliminar logo</button>
                                </form>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="text-muted small mb-3"><i class="bi bi-image me-1"></i> Sin logo — se usa img/voluntariado.png por defecto</div>
                        <?php endif; ?>
                        <form method="POST" enctype="multipart/form-data" action="voluntariado" class="d-flex gap-2 align-items-end">
                            <input type="hidden" name="action" value="upload_logo">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                            <div class="flex-grow-1">
                                <label class="form-label">Subir nuevo logo (JPG, PNG, WEBP)</label>
                                <input type="file" class="form-control" name="logo" accept=".jpg,.jpeg,.png,.webp" required>
                            </div>
                            <button type="submit" class="btn btn-info text-white"><i class="bi bi-upload me-1"></i> Subir</button>
                        </form>
                    </div>
                </div>

                <!-- Configuración de contenido -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white"><i class="bi bi-gear me-1"></i> Contenido de la página</div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" action="voluntariado">
                            <input type="hidden" name="action" value="save_config">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Lema principal</label>
                                    <input type="text" class="form-control" name="lema" value="<?= htmlspecialchars($config['lema'] ?? '') ?>" maxlength="300">
                                </div>
                                <div class="col-12"><hr></div>
                                <div class="col-md-6">
                                    <label class="form-label">Misión — Título</label>
                                    <input type="text" class="form-control" name="mision_titulo" value="<?= htmlspecialchars($config['mision_titulo'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Misión — Subtítulo</label>
                                    <input type="text" class="form-control" name="mision_subtitulo" value="<?= htmlspecialchars($config['mision_subtitulo'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Misión — Texto</label>
                                    <textarea class="form-control" name="mision_texto" rows="4"><?= htmlspecialchars($config['mision_texto'] ?? '') ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Misión — Subtexto</label>
                                    <textarea class="form-control" name="mision_subtexto" rows="4"><?= htmlspecialchars($config['mision_subtexto'] ?? '') ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Visión — Texto</label>
                                    <textarea class="form-control" name="vision_texto" rows="3"><?= htmlspecialchars($config['vision_texto'] ?? '') ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Valores — Texto (uno por línea)</label>
                                    <textarea class="form-control" name="valores_texto" rows="3"><?= htmlspecialchars($config['valores_texto'] ?? '') ?></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Guardar configuración</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Galería de imágenes -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div><i class="bi bi-images me-1"></i> Galería de fotos <span class="badge bg-secondary ms-1"><?= count($imagenes) ?></span></div>
                        <?php if (!empty($imagenes)): ?>
                        <button type="button" class="btn btn-sm btn-action-delete" data-bs-toggle="modal" data-bs-target="#deleteAllModal"><i class="bi bi-trash3 me-1"></i> Eliminar todas</button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <!-- Upload -->
                        <form method="POST" enctype="multipart/form-data" action="voluntariado" class="row g-2 mb-3 align-items-end">
                            <input type="hidden" name="action" value="add_images">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                            <div class="col-md-8">
                                <label class="form-label">Imágenes (JPG, PNG, WEBP — máx. 20 MB c/u)</label>
                                <input type="file" class="form-control" name="imagenes[]" accept=".jpg,.jpeg,.png,.webp" multiple required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success w-100"><i class="bi bi-upload me-1"></i> Subir</button>
                            </div>
                        </form>

                        <?php if (empty($imagenes)): ?>
                        <div class="text-center text-muted py-3"><i class="bi bi-image" style="font-size:2rem;"></i><p class="mt-2 mb-0">No hay imágenes.</p></div>
                        <?php else: ?>
                        <p class="text-muted small mb-2"><i class="bi bi-arrows-move me-1"></i> Arrastra para reordenar</p>
                        <div class="row g-2 sortable-gallery">
                            <?php foreach ($imagenes as $img): ?>
                            <div class="col-6 col-md-3 sortable-item" data-id="<?= (int)$img['id'] ?>">
                                <div class="card h-100 shadow-sm" style="cursor:grab;">
                                    <div class="position-relative">
                                        <img src="../<?= htmlspecialchars($img['imagen_path']) ?>" class="card-img-top" style="height:100px;object-fit:contain;background:#f5f5f5;">
                                        <span class="position-absolute top-0 start-0 badge bg-dark m-1 orden-badge"><?= (int)$img['orden'] ?></span>
                                    </div>
                                    <div class="card-body p-1 text-center">
                                        <form method="POST" action="voluntariado" onsubmit="return confirm('¿Eliminar esta imagen?')">
                                            <input type="hidden" name="action" value="delete_image">
                                            <input type="hidden" name="image_id" value="<?= (int)$img['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                            <button type="submit" class="btn btn-sm btn-action-delete w-100"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Modal eliminar todas -->
                <?php if (!empty($imagenes)): ?>
                <div class="modal fade" id="deleteAllModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="voluntariado">
                                <input type="hidden" name="action" value="delete_all_images">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                <div class="modal-header"><h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Eliminar todas</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body"><p>¿Eliminar las <strong><?= count($imagenes) ?></strong> imágenes? Esta acción no se puede deshacer.</p></div>
                                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger"><i class="bi bi-trash3 me-1"></i> Eliminar todas</button></div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="../js/upload-progress.js?v=13"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        if (window.innerWidth <= 768) sidebar.classList.add('collapsed');
        document.getElementById('toggleSidebar').addEventListener('click', function() { sidebar.classList.toggle('collapsed'); });
        const closeBtn = document.getElementById('closeSidebar');
        if (closeBtn) closeBtn.addEventListener('click', function() { sidebar.classList.add('collapsed'); });

        document.querySelectorAll('.sortable-gallery').forEach(function(grid) {
            new Sortable(grid, {
                animation: 200, ghostClass: 'sortable-ghost', draggable: '.sortable-item',
                onEnd: function() {
                    var ids = [];
                    grid.querySelectorAll('.sortable-item').forEach(function(item, idx) {
                        ids.push(item.getAttribute('data-id'));
                        var b = item.querySelector('.orden-badge'); if (b) b.textContent = idx + 1;
                    });
                    var fd = new FormData();
                    fd.append('action', 'reorder_images');
                    fd.append('order', ids.join(','));
                    fetch('voluntariado.php', { method: 'POST', body: fd })
                        .then(function(r) { return r.json(); })
                        .then(function(d) { if (!d.success) alert('Error al guardar orden'); })
                        .catch(function() { alert('Error de conexión'); });
                }
            });
        });
    </script>
    <style>.sortable-ghost{opacity:.4;}.sortable-item{transition:transform .15s;}</style>
</body>
</html>



