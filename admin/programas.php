<?php
/**
 * admin/programas.php — CRUD para "Nuestros Programas"
 * Secciones: solo título + slug (generan página dinámica propia)
 */

require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload_handler.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';

    // REORDER (AJAX)
    if ($action === 'reorder') {
        header('Content-Type: application/json');
        $ids = array_map('intval', explode(',', $_POST['order'] ?? ''));
        try {
            $stmt = $pdo->prepare('UPDATE programas SET orden = ? WHERE id = ?');
            foreach ($ids as $pos => $id) { if ($id > 0) $stmt->execute([$pos + 1, $id]); }
            echo json_encode(['success' => true]);
        } catch (PDOException $e) { echo json_encode(['success' => false]); }
        exit;
    }

    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF inválido.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: programas'); exit;
    }

    // ── CREATE ────────────────────────────────────────────────────────────────
    if ($action === 'create') {
        $nombre = trim($_POST['nombre'] ?? '');
        if (empty($nombre)) {
            $_SESSION['flash_message'] = 'El nombre es obligatorio.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: programas'); exit;
        }
        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_message'] = 'Debe seleccionar una imagen.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: programas'); exit;
        }
        $upload = handle_upload($_FILES['imagen'], 'image');
        if (!$upload['success']) {
            $_SESSION['flash_message'] = $upload['error'];
            $_SESSION['flash_type']    = 'danger';
            header('Location: programas'); exit;
        }

        $secTitulos = array_filter(array_map('trim', $_POST['sec_titulo'] ?? []));

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->query('SELECT COALESCE(MAX(orden),0)+1 FROM programas');
            $nextOrden = (int)$stmt->fetchColumn();
            $pdo->prepare('INSERT INTO programas (nombre, imagen_path, orden, activo) VALUES (?,?,?,1)')
                ->execute([$nombre, $upload['path'], $nextOrden]);
            $programaId = (int)$pdo->lastInsertId();

            $stmtSec = $pdo->prepare('INSERT INTO programas_secciones (programa_id, titulo, slug, orden) VALUES (?,?,?,?)');
            foreach (array_values($secTitulos) as $idx => $titulo) {
                $slug = _gen_slug($titulo, $programaId, $idx, $pdo);
                $stmtSec->execute([$programaId, $titulo, $slug, $idx]);
            }
            $pdo->commit();
            $_SESSION['flash_message'] = 'Programa creado correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al guardar.';
            $_SESSION['flash_type']    = 'danger';
        }
        header('Location: programas'); exit;
    }

    // ── EDIT ──────────────────────────────────────────────────────────────────
    if ($action === 'edit') {
        $id     = (int)($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        if ($id <= 0 || empty($nombre)) {
            $_SESSION['flash_message'] = 'Datos inválidos.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: programas'); exit;
        }
        $stmt = $pdo->prepare('SELECT * FROM programas WHERE id = ?');
        $stmt->execute([$id]);
        $old = $stmt->fetch();
        if (!$old) { header('Location: programas'); exit; }

        $newImagePath = $old['imagen_path'];
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = handle_upload($_FILES['imagen'], 'image');
            if (!$upload['success']) {
                $_SESSION['flash_message'] = $upload['error'];
                $_SESSION['flash_type']    = 'danger';
                header('Location: programas'); exit;
            }
            $newImagePath = $upload['path'];
        }

        $secTitulos = array_filter(array_map('trim', $_POST['sec_titulo'] ?? []));

        try {
            $pdo->beginTransaction();
            $pdo->prepare('UPDATE programas SET nombre=?, imagen_path=? WHERE id=?')
                ->execute([$nombre, $newImagePath, $id]);

            // Obtener slugs existentes para no regenerar
            $existingSlugs = [];
            $stmtEx = $pdo->prepare('SELECT titulo, slug FROM programas_secciones WHERE programa_id = ?');
            $stmtEx->execute([$id]);
            foreach ($stmtEx->fetchAll() as $row) {
                $existingSlugs[$row['titulo']] = $row['slug'];
            }

            $pdo->prepare('DELETE FROM programas_secciones WHERE programa_id = ?')->execute([$id]);

            $stmtSec = $pdo->prepare('INSERT INTO programas_secciones (programa_id, titulo, slug, orden) VALUES (?,?,?,?)');
            foreach (array_values($secTitulos) as $idx => $titulo) {
                $slug = $existingSlugs[$titulo] ?? _gen_slug($titulo, $id, $idx, $pdo);
                $stmtSec->execute([$id, $titulo, $slug, $idx]);
            }
            $pdo->commit();

            if ($newImagePath !== $old['imagen_path'] && !empty($old['imagen_path'])) {
                $oldFile = BASE_PATH . '/' . $old['imagen_path'];
                if (file_exists($oldFile)) unlink($oldFile);
            }
            $_SESSION['flash_message'] = 'Programa actualizado.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al actualizar.';
            $_SESSION['flash_type']    = 'danger';
        }
        header('Location: programas'); exit;
    }

    // ── DELETE ────────────────────────────────────────────────────────────────
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('SELECT imagen_path FROM programas WHERE id = ?');
        $stmt->execute([$id]);
        $prog = $stmt->fetch();
        if ($prog) {
            $pdo->prepare('DELETE FROM programas WHERE id = ?')->execute([$id]);
            if (!empty($prog['imagen_path'])) {
                $f = BASE_PATH . '/' . $prog['imagen_path'];
                if (file_exists($f)) unlink($f);
            }
            $_SESSION['flash_message'] = 'Programa eliminado.';
            $_SESSION['flash_type']    = 'success';
        }
        header('Location: programas'); exit;
    }

    // ── SAVE CONTACTO ─────────────────────────────────────────────────────────
    if ($action === 'save_contacto') {
        $data = [
            trim($_POST['titulo1']   ?? ''),
            trim($_POST['titulo2']   ?? ''),
            trim($_POST['direccion'] ?? ''),
            trim($_POST['telefono']  ?? ''),
            trim($_POST['horario']   ?? ''),
            trim($_POST['correo']    ?? ''),
        ];
        try {
            $exists = $pdo->query('SELECT id FROM contacto_config LIMIT 1')->fetch();
            if ($exists) {
                $pdo->prepare('UPDATE contacto_config SET titulo1=?,titulo2=?,direccion=?,telefono=?,horario=?,correo=?,updated_at=NOW() WHERE id=1')
                    ->execute($data);
            } else {
                $pdo->prepare('INSERT INTO contacto_config (titulo1,titulo2,direccion,telefono,horario,correo) VALUES (?,?,?,?,?,?)')
                    ->execute($data);
            }
            $_SESSION['flash_message'] = 'Información de contacto actualizada.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = 'Error al guardar contacto.';
            $_SESSION['flash_type']    = 'danger';
        }
        header('Location: programas'); exit;
    }

    // ── SAVE PAGE (contenido de sección) ──────────────────────────────────────
    if ($action === 'save_page') {
        $seccion_id = (int)($_POST['seccion_id'] ?? 0);
        $texto1     = trim($_POST['texto1'] ?? '');
        $texto2     = trim($_POST['texto2'] ?? '');

        // Imágenes opcionales
        $stmt = $pdo->prepare('SELECT * FROM programas_secciones_paginas WHERE seccion_id = ?');
        $stmt->execute([$seccion_id]);
        $current = $stmt->fetch();

        $img1 = $current['imagen1_path'] ?? null;
        $img2 = $current['imagen2_path'] ?? null;

        if (isset($_FILES['imagen1']) && $_FILES['imagen1']['error'] !== UPLOAD_ERR_NO_FILE) {
            $up = handle_upload($_FILES['imagen1'], 'image');
            if ($up['success']) {
                if ($img1) { $f = BASE_PATH.'/'.$img1; if (file_exists($f)) unlink($f); }
                $img1 = $up['path'];
            }
        }
        if (isset($_FILES['imagen2']) && $_FILES['imagen2']['error'] !== UPLOAD_ERR_NO_FILE) {
            $up = handle_upload($_FILES['imagen2'], 'image');
            if ($up['success']) {
                if ($img2) { $f = BASE_PATH.'/'.$img2; if (file_exists($f)) unlink($f); }
                $img2 = $up['path'];
            }
        }

        try {
            if ($current) {
                $pdo->prepare('UPDATE programas_secciones_paginas SET imagen1_path=?,texto1=?,imagen2_path=?,texto2=?,updated_at=NOW() WHERE seccion_id=?')
                    ->execute([$img1, $texto1, $img2, $texto2, $seccion_id]);
            } else {
                $pdo->prepare('INSERT INTO programas_secciones_paginas (seccion_id,imagen1_path,texto1,imagen2_path,texto2) VALUES (?,?,?,?,?)')
                    ->execute([$seccion_id, $img1, $texto1, $img2, $texto2]);
            }
            $_SESSION['flash_message'] = 'Contenido de sección guardado.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = 'Error al guardar contenido.';
            $_SESSION['flash_type']    = 'danger';
        }
        header('Location: programas'); exit;
    }
}

// Helper: generar slug único
function _gen_slug(string $titulo, int $prog_id, int $idx, PDO $pdo): string {
    $base = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $titulo), '-'));
    if (empty($base)) $base = 'seccion';
    $slug = $base . '-' . $prog_id;
    $counter = 1;
    while (true) {
        $s = $pdo->prepare('SELECT id FROM programas_secciones WHERE slug = ?');
        $s->execute([$slug]);
        if (!$s->fetch()) break;
        $slug = $base . '-' . $prog_id . '-' . $counter++;
    }
    return $slug;
}

// ── Consultar datos ────────────────────────────────────────────────────────────
$programas = $pdo->query(
    'SELECT p.*, COUNT(s.id) AS num_secciones
     FROM programas p
     LEFT JOIN programas_secciones s ON s.programa_id = p.id
     GROUP BY p.id ORDER BY p.orden ASC'
)->fetchAll();

$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType    = $_SESSION['flash_type']    ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);
$token        = csrf_token();
$token_delete = csrf_token();

$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType    = $_SESSION['flash_type']    ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);
$token        = csrf_token();
$token_delete = csrf_token();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programas — Panel Admin DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css?v=7">
</head>
<body>
<div class="d-flex">
    <?php require_once __DIR__ . '/sidebar_sections.php';
    require_once __DIR__ . '/page_help.php';
    render_admin_sidebar($sidebar_groups, $current_admin_file); ?>
    <div class="main-content">
        <nav class="navbar navbar-light bg-white shadow-sm px-3">
            <button class="btn btn-outline-secondary me-2" id="toggleSidebar"><i class="bi bi-list"></i></button>
            <span class="navbar-brand mb-0 h6">Nuestros Programas</span>
            <a href="logout" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-box-arrow-right"></i> Salir</a>
        </nav>
        <div class="container-fluid p-4">
            <?php page_help('programas'); ?>
            <?php if ($flashMessage): ?>
            <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show">
                <?= htmlspecialchars($flashMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Crear programa -->
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header bg-primary text-white"><i class="bi bi-plus-circle me-1"></i> Crear programa</div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data" action="programas">
                                <input type="hidden" name="action" value="create">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del programa</label>
                                    <input type="text" class="form-control" name="nombre" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Imagen (JPG, PNG, WEBP — máx. 20 MB)</label>
                                    <input type="file" class="form-control" name="imagen" accept=".jpg,.jpeg,.png,.webp" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Crear programa</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Listado -->
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header"><i class="bi bi-grid-3x3-gap me-1"></i> Programas <span class="badge bg-secondary ms-1"><?= count($programas) ?></span></div>
                        <div class="card-body p-0">
                            <?php if (empty($programas)): ?>
                            <div class="text-center text-muted py-4"><i class="bi bi-grid-3x3-gap" style="font-size:2rem;"></i><p class="mt-2">No hay programas.</p></div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr><th style="width:80px;">Imagen</th><th>Nombre</th><th style="width:90px;">Secciones</th><th style="width:180px;">Acciones</th></tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($programas as $prog): ?>
                                        <tr>
                                            <td><?php if (!empty($prog['imagen_path'])): ?><img src="../<?= htmlspecialchars($prog['imagen_path']) ?>" class="thumb-preview"><?php endif; ?></td>
                                            <td><?= htmlspecialchars($prog['nombre']) ?></td>
                                            <td class="text-center"><span class="badge bg-info text-dark"><?= (int)$prog['num_secciones'] ?></span></td>
                                            <td>
                                                <a href="programa_editar?id=<?= (int)$prog['id'] ?>" class="btn btn-sm btn-action-edit"><i class="bi bi-pencil"></i> Editar</a>
                                                <button class="btn btn-sm btn-action-delete" data-bs-toggle="modal" data-bs-target="#delM<?= $prog['id'] ?>"><i class="bi bi-trash3"></i></button>
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
    </div>
</div>

<!-- Modales eliminar -->
<?php foreach ($programas as $prog): ?>
<div class="modal fade" id="delM<?= $prog['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="programas">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$prog['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token_delete) ?>">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Eliminar programa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body"><p>¿Eliminar <strong><?= htmlspecialchars($prog['nombre']) ?></strong> y todas sus secciones?</p></div>
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
<script src="../js/upload-progress.js?v=13"></script>
<script>
const sidebar = document.getElementById('sidebar');
if (window.innerWidth <= 768) sidebar.classList.add('collapsed');
document.getElementById('toggleSidebar').addEventListener('click', () => sidebar.classList.toggle('collapsed'));
const cb = document.getElementById('closeSidebar');
if (cb) cb.addEventListener('click', () => sidebar.classList.add('collapsed'));
</script>
</body>
</html>
