<?php
/**
 * admin/transparencia.php — CRUD para entradas de Transparencia del index
 *
 * Requisitos: 13.1, 13.2, 13.3, 13.4
 */

require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload_handler.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();

// ── Páginas internas de transparencia disponibles ──────────────────────────────
$paginas_transparencia = [
    'transparencia/SEAC.php'                 => 'SEAC',
    'transparencia/cuenta_publica.php'       => 'Cuenta Pública',
    'transparencia/presupuesto_anual.php'    => 'Presupuesto Anual',
    'transparencia/pae.php'                  => 'PAE',
    'transparencia/matrices_indicadores.php' => 'Matrices de Indicadores',
    'transparencia/conac.php'                => 'CONAC',
    'transparencia/financiero.php'           => 'Financiero',
    'transparencia/avisos_privacidad.php'    => 'Avisos de Privacidad',
    'acerca-del-dif/organigrama.php'         => 'Organigrama',
];

// ── Procesamiento POST ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';

    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF inválido. Intente de nuevo.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: transparencia.php');
        exit;
    }

    // ── CREATE: nueva entrada de transparencia ─────────────────────────────────
    if ($action === 'create') {
        $titulo    = trim($_POST['titulo'] ?? '');
        $pagina    = trim($_POST['pagina'] ?? '');
        $url_ext   = trim($_POST['url_externa'] ?? '');
        $orden     = (int) ($_POST['orden'] ?? 0);

        // Determinar URL final
        if ($pagina === '__ninguno__') {
            $url = '#';
        } elseif ($pagina !== '' && $pagina !== '__externa__') {
            $url = $pagina; // ruta relativa interna
        } elseif ($url_ext !== '') {
            $url = $url_ext;
        } else {
            $url = '';
        }

        if (empty($titulo)) {
            $_SESSION['flash_message'] = 'El título es obligatorio.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: transparencia.php');
            exit;
        }

        if (empty($url) && $pagina !== '__ninguno__') {
            $_SESSION['flash_message'] = 'Debe seleccionar una página de destino o ingresar una URL externa.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: transparencia.php');
            exit;
        }

        $imagenPath = null;

        // Handle optional image upload
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = handle_upload($_FILES['imagen'], 'image');

            if (!$upload['success']) {
                $_SESSION['flash_message'] = $upload['error'];
                $_SESSION['flash_type']    = 'danger';
                header('Location: transparencia.php');
                exit;
            }

            $imagenPath = $upload['path'];
        }

        try {
            if ($orden <= 0) {
                $stmt = $pdo->query('SELECT COALESCE(MAX(orden), 0) + 1 FROM transparencia_items');
                $orden = (int) $stmt->fetchColumn();
            }

            $stmt = $pdo->prepare(
                'INSERT INTO transparencia_items (titulo, url, imagen_path, orden, activo) VALUES (?, ?, ?, ?, 1)'
            );
            $stmt->execute([$titulo, $url, $imagenPath, $orden]);

            $_SESSION['flash_message'] = 'Entrada de transparencia creada correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al guardar en la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: transparencia.php');
        exit;
    }

    // ── EDIT: modificar entrada de transparencia ───────────────────────────────
    if ($action === 'edit') {
        $id        = (int) ($_POST['id'] ?? 0);
        $titulo    = trim($_POST['titulo'] ?? '');
        $pagina    = trim($_POST['pagina'] ?? '');
        $url_ext   = trim($_POST['url_externa'] ?? '');
        $orden     = (int) ($_POST['orden'] ?? 0);

        // Determinar URL final
        if ($pagina === '__ninguno__') {
            $url = '#';
        } elseif ($pagina !== '' && $pagina !== '__externa__') {
            $url = $pagina;
        } elseif ($url_ext !== '') {
            $url = $url_ext;
        } else {
            $url = '';
        }

        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID de entrada inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: transparencia.php');
            exit;
        }

        if (empty($titulo)) {
            $_SESSION['flash_message'] = 'El título es obligatorio.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: transparencia.php');
            exit;
        }

        if (empty($url) && $pagina !== '__ninguno__') {
            $_SESSION['flash_message'] = 'Debe seleccionar una página de destino o ingresar una URL externa.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: transparencia.php');
            exit;
        }

        // Fetch current record
        $stmt = $pdo->prepare('SELECT * FROM transparencia_items WHERE id = ?');
        $stmt->execute([$id]);
        $old = $stmt->fetch();

        if (!$old) {
            $_SESSION['flash_message'] = 'Registro no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: transparencia.php');
            exit;
        }

        $newImagePath = $old['imagen_path'];

        // Handle optional image replacement
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = handle_upload($_FILES['imagen'], 'image');

            if (!$upload['success']) {
                $_SESSION['flash_message'] = $upload['error'];
                $_SESSION['flash_type']    = 'danger';
                header('Location: transparencia.php');
                exit;
            }

            $newImagePath = $upload['path'];
        }

        try {
            $stmt = $pdo->prepare(
                'UPDATE transparencia_items SET titulo = ?, url = ?, imagen_path = ?, orden = ? WHERE id = ?'
            );
            $stmt->execute([$titulo, $url, $newImagePath, $orden, $id]);

            // Delete old image if replaced
            if ($newImagePath !== $old['imagen_path'] && !empty($old['imagen_path'])) {
                $oldFile = BASE_PATH . '/' . $old['imagen_path'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $_SESSION['flash_message'] = 'Entrada de transparencia actualizada correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al actualizar en la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: transparencia.php');
        exit;
    }

    // ── DELETE: eliminar entrada de transparencia ──────────────────────────────
    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID de entrada inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: transparencia.php');
            exit;
        }

        $stmt = $pdo->prepare('SELECT * FROM transparencia_items WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            $_SESSION['flash_message'] = 'Registro no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: transparencia.php');
            exit;
        }

        try {
            $stmt = $pdo->prepare('DELETE FROM transparencia_items WHERE id = ?');
            $stmt->execute([$id]);

            // Delete image file if exists
            if (!empty($row['imagen_path'])) {
                $filePath = BASE_PATH . '/' . $row['imagen_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $_SESSION['flash_message'] = 'Entrada de transparencia eliminada correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al eliminar de la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: transparencia.php');
        exit;
    }
}

// ── Consultar entradas actuales ────────────────────────────────────────────────
$stmt = $pdo->query('SELECT * FROM transparencia_items ORDER BY orden ASC, id ASC');
$items = $stmt->fetchAll();

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
    <title>Transparencia — Panel de Administración DIF</title>
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
                <span class="navbar-brand mb-0 h6">Transparencia</span>
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
                                <i class="bi bi-plus-circle me-1"></i> Agregar entrada de transparencia
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data" action="transparencia.php">
                                    <input type="hidden" name="action" value="create">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                    <div class="mb-3">
                                        <label for="titulo" class="form-label">Título</label>
                                        <input type="text" class="form-control" id="titulo" name="titulo" required
                                               placeholder="Ej: Cuenta Pública">
                                    </div>
                                    <div class="mb-3">
                                        <label for="pagina" class="form-label">Página de destino</label>
                                        <select class="form-select" id="pagina" name="pagina" required onchange="toggleUrlExterna(this, 'urlExternaCreate')">
                                            <option value="" disabled selected>— Seleccionar página —</option>
                                            <?php foreach ($paginas_transparencia as $ruta => $nombre): ?>
                                            <option value="<?= htmlspecialchars($ruta) ?>"><?= htmlspecialchars($nombre) ?></option>
                                            <?php endforeach; ?>
                                            <option value="__ninguno__">🚫 Sin enlace</option>
                                            <option value="__externa__">🔗 URL externa...</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 d-none" id="urlExternaCreate">
                                        <label for="url_externa" class="form-label">URL externa</label>
                                        <input type="url" class="form-control" name="url_externa"
                                               placeholder="https://ejemplo.com/transparencia">
                                    </div>
                                    <div class="mb-3">
                                        <label for="imagen" class="form-label">Imagen (opcional — JPG, PNG, WEBP — máx. 20 MB)</label>
                                        <input type="file" class="form-control" id="imagen" name="imagen" accept=".jpg,.jpeg,.png,.webp">
                                    </div>
                                    <div class="mb-3">
                                        <label for="orden" class="form-label">Orden</label>
                                        <input type="number" class="form-control" id="orden" name="orden" value="0" min="0"
                                               placeholder="0 = automático">
                                        <div class="form-text">Dejar en 0 para asignar automáticamente al final.</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-save me-1"></i> Crear entrada
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Listado de entradas -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-shield-check me-1"></i> Entradas de Transparencia
                                <span class="badge bg-secondary ms-1"><?= count($items) ?></span>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($items)): ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-shield-check" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">No hay entradas de transparencia registradas. Use el formulario para agregar una.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 60px;">Orden</th>
                                                    <th>Título</th>
                                                    <th>Destino</th>
                                                    <th style="width: 80px;">Imagen</th>
                                                    <th style="width: 60px;">Activo</th>
                                                    <th style="width: 180px;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($items as $item): ?>
                                                    <tr>
                                                        <td class="text-center"><?= (int) $item['orden'] ?></td>
                                                        <td><?= htmlspecialchars($item['titulo']) ?></td>
                                                        <td>
                                                            <?php
                                                            $destino_label = $paginas_transparencia[$item['url']] ?? null;
                                                            if ($item['url'] === '#'): ?>
                                                                <span class="badge bg-secondary">Sin enlace</span>
                                                            <?php elseif ($destino_label): ?>
                                                                <span class="badge bg-primary"><?= htmlspecialchars($destino_label) ?></span>
                                                            <?php else: ?>
                                                                <a href="<?= htmlspecialchars($item['url']) ?>" target="_blank" rel="noopener" class="text-decoration-none">
                                                                    <small><?= htmlspecialchars($item['url']) ?></small>
                                                                    <i class="bi bi-box-arrow-up-right ms-1"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if (!empty($item['imagen_path'])): ?>
                                                                <img src="../<?= htmlspecialchars($item['imagen_path']) ?>"
                                                                     alt="<?= htmlspecialchars($item['titulo']) ?>"
                                                                     class="thumb-preview">
                                                            <?php else: ?>
                                                                <span class="text-muted"><i class="bi bi-image"></i></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if ($item['activo']): ?>
                                                                <span class="badge bg-success">Sí</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">No</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editModal<?= (int) $item['id'] ?>"
                                                                    title="Editar">
                                                                <i class="bi bi-pencil"></i> Editar
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#deleteModal<?= (int) $item['id'] ?>"
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
                <?php foreach ($items as $item): ?>

                <!-- Modal Editar -->
                <div class="modal fade" id="editModal<?= (int) $item['id'] ?>" tabindex="-1" aria-labelledby="editLabel<?= (int) $item['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" enctype="multipart/form-data" action="transparencia.php">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title" id="editLabel<?= (int) $item['id'] ?>">
                                        <i class="bi bi-pencil-square me-1"></i> Editar entrada #<?= (int) $item['id'] ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="editTitulo<?= (int) $item['id'] ?>" class="form-label">Título</label>
                                        <input type="text" class="form-control" id="editTitulo<?= (int) $item['id'] ?>" name="titulo"
                                               value="<?= htmlspecialchars($item['titulo']) ?>" required>
                                    </div>
                                    <?php
                                    $es_interna = array_key_exists($item['url'], $paginas_transparencia);
                                    $es_ninguno = ($item['url'] === '#');
                                    ?>
                                    <div class="mb-3">
                                        <label for="editPagina<?= (int) $item['id'] ?>" class="form-label">Página de destino</label>
                                        <select class="form-select" id="editPagina<?= (int) $item['id'] ?>" name="pagina" required
                                                onchange="toggleUrlExterna(this, 'urlExternaEdit<?= (int) $item['id'] ?>')">
                                            <option value="" disabled>— Seleccionar página —</option>
                                            <?php foreach ($paginas_transparencia as $ruta => $nombre): ?>
                                            <option value="<?= htmlspecialchars($ruta) ?>"<?= ($es_interna && $item['url'] === $ruta) ? ' selected' : '' ?>><?= htmlspecialchars($nombre) ?></option>
                                            <?php endforeach; ?>
                                            <option value="__ninguno__"<?= $es_ninguno ? ' selected' : '' ?>>🚫 Sin enlace</option>
                                            <option value="__externa__"<?= (!$es_interna && !$es_ninguno) ? ' selected' : '' ?>>🔗 URL externa...</option>
                                        </select>
                                    </div>
                                    <div class="mb-3<?= ($es_interna || $es_ninguno) ? ' d-none' : '' ?>" id="urlExternaEdit<?= (int) $item['id'] ?>">
                                        <label for="editUrlExt<?= (int) $item['id'] ?>" class="form-label">URL externa</label>
                                        <input type="url" class="form-control" id="editUrlExt<?= (int) $item['id'] ?>" name="url_externa"
                                               value="<?= (!$es_interna && !$es_ninguno) ? htmlspecialchars($item['url']) : '' ?>"
                                               placeholder="https://ejemplo.com">
                                    </div>
                                    <?php if (!empty($item['imagen_path'])): ?>
                                        <div class="mb-3 text-center">
                                            <p class="text-muted small mb-1">Imagen actual:</p>
                                            <img src="../<?= htmlspecialchars($item['imagen_path']) ?>"
                                                 alt="Imagen actual"
                                                 class="img-fluid rounded" style="max-height: 150px;">
                                        </div>
                                    <?php endif; ?>
                                    <div class="mb-3">
                                        <label for="editImagen<?= (int) $item['id'] ?>" class="form-label">Nueva imagen (opcional — dejar vacío para conservar la actual)</label>
                                        <input type="file" class="form-control" id="editImagen<?= (int) $item['id'] ?>" name="imagen" accept=".jpg,.jpeg,.png,.webp">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editOrden<?= (int) $item['id'] ?>" class="form-label">Orden</label>
                                        <input type="number" class="form-control" id="editOrden<?= (int) $item['id'] ?>" name="orden"
                                               value="<?= (int) $item['orden'] ?>" min="0">
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
                <div class="modal fade" id="deleteModal<?= (int) $item['id'] ?>" tabindex="-1" aria-labelledby="deleteLabel<?= (int) $item['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="transparencia.php">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title text-danger" id="deleteLabel<?= (int) $item['id'] ?>">
                                        <i class="bi bi-exclamation-triangle me-1"></i> Confirmar eliminación
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <p>¿Está seguro de eliminar la entrada <strong><?= htmlspecialchars($item['titulo']) ?></strong>?</p>
                                    <p class="text-muted small">
                                        URL: <?= htmlspecialchars($item['url']) ?><br>
                                        <?php if (!empty($item['imagen_path'])): ?>
                                            La imagen asociada también será eliminada del servidor.<br>
                                        <?php endif; ?>
                                        Esta acción no se puede deshacer.
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
    <script src="../js/upload-progress.js?v=12"></script>
    <script>
        // Toggle URL externa
        function toggleUrlExterna(sel, targetId) {
            var wrap = document.getElementById(targetId);
            if (!wrap) return;
            if (sel.value === '__externa__') {
                wrap.classList.remove('d-none');
            } else {
                wrap.classList.add('d-none');
            }
        }

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
