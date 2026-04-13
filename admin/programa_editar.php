<?php
/**
 * admin/programa_editar.php — Edición completa de un programa
 */
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload_handler.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();
$id  = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) { header('Location: programas'); exit; }

// ── POST ──────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';

    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF inválido.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: programa_editar?id=' . $id); exit;
    }

    // Guardar nombre + imagen + secciones
    if ($action === 'edit') {
        $nombre = trim($_POST['nombre'] ?? '');
        $stmt = $pdo->prepare('SELECT * FROM programas WHERE id = ?');
        $stmt->execute([$id]);
        $old = $stmt->fetch();
        if (!$old) { header('Location: programas'); exit; }

        $newImagePath = $old['imagen_path'];
        $imagenLink   = trim($_POST['imagen_link'] ?? $old['imagen_link'] ?? '');
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = handle_upload($_FILES['imagen'], 'image');
            if ($upload['success']) {
                if (!empty($old['imagen_path'])) { $f = BASE_PATH.'/'.$old['imagen_path']; if (file_exists($f)) unlink($f); }
                $newImagePath = $upload['path'];
            } else {
                $_SESSION['flash_message'] = $upload['error'];
                $_SESSION['flash_type']    = 'danger';
                header('Location: programa_editar?id=' . $id); exit;
            }
        }

        $secTitulos = array_values(array_filter(array_map('trim', $_POST['sec_titulo'] ?? [])));
        // sec_ids viene del formulario para saber qué secciones existentes conservar
        $secIds     = array_values($_POST['sec_id'] ?? []);

        try {
            $pdo->beginTransaction();
            $pdo->prepare('UPDATE programas SET nombre=?, imagen_path=?, imagen_link=? WHERE id=?')->execute([$nombre, $newImagePath, $imagenLink, $id]);

            // Obtener secciones actuales en BD
            $stmtEx = $pdo->prepare('SELECT id, titulo, slug FROM programas_secciones WHERE programa_id = ? ORDER BY orden ASC');
            $stmtEx->execute([$id]);
            $existentes = $stmtEx->fetchAll(PDO::FETCH_ASSOC);
            $existentesPorId = [];
            foreach ($existentes as $e) { $existentesPorId[$e['id']] = $e; }

            // IDs que el formulario quiere conservar
            $idsConservar = array_map('intval', array_filter($secIds, fn($v) => (int)$v > 0));

            // Eliminar solo las que NO están en el formulario
            foreach ($existentes as $e) {
                if (!in_array((int)$e['id'], $idsConservar)) {
                    $pdo->prepare('DELETE FROM programas_secciones WHERE id = ?')->execute([$e['id']]);
                }
            }

            // Actualizar orden y título de las existentes, insertar las nuevas
            $stmtUpd = $pdo->prepare('UPDATE programas_secciones SET titulo=?, orden=? WHERE id=? AND programa_id=?');
            $stmtIns = $pdo->prepare('INSERT INTO programas_secciones (programa_id, titulo, slug, orden) VALUES (?,?,?,?)');

            foreach ($secTitulos as $idx => $titulo) {
                $secId = isset($secIds[$idx]) ? (int)$secIds[$idx] : 0;
                if ($secId > 0 && isset($existentesPorId[$secId])) {
                    // Actualizar existente
                    $stmtUpd->execute([$titulo, $idx, $secId, $id]);
                } else {
                    // Nueva sección
                    $slug = _gen_slug_edit($titulo, $id, $idx, $pdo);
                    $stmtIns->execute([$id, $titulo, $slug, $idx]);
                }
            }

            $pdo->commit();
            $_SESSION['flash_message'] = 'Programa actualizado.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['flash_message'] = 'Error al guardar.';
            $_SESSION['flash_type']    = 'danger';
        }
        header('Location: programa_editar?id=' . $id); exit;
    }

    // Guardar contenido de página de sección
    if ($action === 'save_page') {
        $seccion_id = (int)($_POST['seccion_id'] ?? 0);
        $texto1 = $_POST['texto1'] ?? '';
        $texto2 = $_POST['texto2'] ?? '';
        $c_titulo1   = trim($_POST['c_titulo1']   ?? '');
        $c_titulo2   = trim($_POST['c_titulo2']   ?? '');
        $c_direccion = trim($_POST['c_direccion'] ?? '');
        $c_telefono  = trim($_POST['c_telefono']  ?? '');
        $c_horario   = trim($_POST['c_horario']   ?? '');
        $c_correo    = trim($_POST['c_correo']    ?? '');
        $c_contacto  = $_POST['c_contacto'] ?? '';

        $stmt = $pdo->prepare('SELECT * FROM programas_secciones_paginas WHERE seccion_id = ?');
        $stmt->execute([$seccion_id]);
        $current = $stmt->fetch();

        $img1 = $current['imagen1_path'] ?? null;
        $img2 = $current['imagen2_path'] ?? null;

        if (isset($_FILES['imagen1']) && $_FILES['imagen1']['error'] !== UPLOAD_ERR_NO_FILE) {
            $up = handle_upload($_FILES['imagen1'], 'image');
            if ($up['success']) { if ($img1) { $f=BASE_PATH.'/'.$img1; if(file_exists($f)) unlink($f); } $img1 = $up['path']; }
        }
        if (isset($_FILES['imagen2']) && $_FILES['imagen2']['error'] !== UPLOAD_ERR_NO_FILE) {
            $up = handle_upload($_FILES['imagen2'], 'image');
            if ($up['success']) { if ($img2) { $f=BASE_PATH.'/'.$img2; if(file_exists($f)) unlink($f); } $img2 = $up['path']; }
        }

        try {
            if ($current) {
                $pdo->prepare('UPDATE programas_secciones_paginas SET imagen1_path=?,texto1=?,imagen2_path=?,texto2=?,c_titulo1=?,c_titulo2=?,c_direccion=?,c_telefono=?,c_horario=?,c_correo=?,c_contacto=?,updated_at=NOW() WHERE seccion_id=?')
                    ->execute([$img1, $texto1, $img2, $texto2, $c_titulo1, $c_titulo2, $c_direccion, $c_telefono, $c_horario, $c_correo, $c_contacto, $seccion_id]);
            } else {
                $pdo->prepare('INSERT INTO programas_secciones_paginas (seccion_id,imagen1_path,texto1,imagen2_path,texto2,c_titulo1,c_titulo2,c_direccion,c_telefono,c_horario,c_correo,c_contacto) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
                    ->execute([$seccion_id, $img1, $texto1, $img2, $texto2, $c_titulo1, $c_titulo2, $c_direccion, $c_telefono, $c_horario, $c_correo, $c_contacto]);
            }
            $_SESSION['flash_message'] = 'Contenido guardado.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = 'Error al guardar.';
            $_SESSION['flash_type']    = 'danger';
        }
        header('Location: programa_editar?id=' . $id); exit;
    }

    // Eliminar sección (AJAX)
    if ($action === 'delete_seccion') {
        header('Content-Type: application/json');
        $seccion_id = (int)($_POST['seccion_id'] ?? 0);
        if ($seccion_id <= 0) { echo json_encode(['success' => false]); exit; }
        try {
            $pdo->prepare('DELETE FROM programas_secciones WHERE id = ? AND programa_id = ?')->execute([$seccion_id, $id]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    // Guardar contacto
    if ($action === 'save_contacto') {
        $data = [trim($_POST['titulo1']??''), trim($_POST['titulo2']??''), trim($_POST['direccion']??''), trim($_POST['telefono']??''), trim($_POST['horario']??''), trim($_POST['correo']??'')];
        try {
            $exists = $pdo->query('SELECT id FROM contacto_config LIMIT 1')->fetch();
            if ($exists) {
                $pdo->prepare('UPDATE contacto_config SET titulo1=?,titulo2=?,direccion=?,telefono=?,horario=?,correo=?,updated_at=NOW() WHERE id=1')->execute($data);
            } else {
                $pdo->prepare('INSERT INTO contacto_config (titulo1,titulo2,direccion,telefono,horario,correo) VALUES (?,?,?,?,?,?)')->execute($data);
            }
            $_SESSION['flash_message'] = 'Contacto actualizado.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = 'Error al guardar contacto.';
            $_SESSION['flash_type']    = 'danger';
        }
        header('Location: programa_editar?id=' . $id); exit;
    }
}

function _gen_slug_edit(string $titulo, int $prog_id, int $idx, PDO $pdo): string {
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

// ── Consultar ─────────────────────────────────────────────────────────────────
$stmt = $pdo->prepare('SELECT * FROM programas WHERE id = ?');
$stmt->execute([$id]);
$prog = $stmt->fetch();
if (!$prog) { header('Location: programas'); exit; }

$secciones = $pdo->prepare(
    'SELECT s.*, p.imagen1_path, p.texto1, p.imagen2_path, p.texto2,
            p.c_titulo1, p.c_titulo2, p.c_direccion, p.c_telefono, p.c_horario, p.c_correo, p.c_contacto
     FROM programas_secciones s
     LEFT JOIN programas_secciones_paginas p ON p.seccion_id = s.id
     WHERE s.programa_id = ? ORDER BY s.orden ASC'
);
$secciones->execute([$id]);
$secciones = $secciones->fetchAll();

try { $contacto = $pdo->query('SELECT * FROM contacto_config LIMIT 1')->fetch(); }
catch (PDOException $e) { $contacto = null; }

$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType    = $_SESSION['flash_type']    ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);

// Cargar trámites para el selector de enlace
$tramites_sel = [];
try { $tramites_sel = $pdo->query('SELECT slug, titulo FROM tramites ORDER BY id ASC')->fetchAll(); } catch (PDOException $e) {}

$t_edit     = csrf_token();
$t_pages    = csrf_token();
$t_contacto = csrf_token();
$t_delete   = csrf_token();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Programa — Panel Admin DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css?v=7">
    <style>
        .section-item { border:1px solid #dee2e6;border-radius:8px;padding:1rem;margin-bottom:.75rem;background:#f8f9fa;position:relative; }
        .section-item .remove-section { position:absolute;top:.5rem;right:.5rem; }
        .seccion-card { border:1px solid #dee2e6;border-radius:8px;margin-bottom:1.5rem; }
        .seccion-card .card-header { background:#f8f9fa;font-weight:600; }
    </style>
</head>
<body>
<div class="d-flex">
    <?php require_once __DIR__ . '/sidebar_sections.php';
    require_once __DIR__ . '/page_help.php';
    render_admin_sidebar($sidebar_groups, $current_admin_file); ?>
    <div class="main-content">
        <nav class="navbar navbar-light bg-white shadow-sm px-3">
            <button class="btn btn-outline-secondary me-2" id="toggleSidebar"><i class="bi bi-list"></i></button>
            <span class="navbar-brand mb-0 h6">
                <a href="programas" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-1"></i>Programas</a>
                <span class="text-muted mx-1">/</span>
                <?= htmlspecialchars($prog['nombre']) ?>
            </span>
            <a href="logout" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-box-arrow-right"></i> Salir</a>
        </nav>

        <div class="container-fluid p-4">
            <?php if ($flashMessage): ?>
            <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show">
                <?= htmlspecialchars($flashMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- ── 1. Datos del programa ── -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white"><i class="bi bi-pencil-square me-1"></i> Datos del programa</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" action="programa_editar?id=<?= $id ?>">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($t_edit) ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre del programa</label>
                                <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($prog['nombre']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Imagen del programa</label>
                                <?php if (!empty($prog['imagen_path'])): ?>
                                <img src="../<?= htmlspecialchars($prog['imagen_path']) ?>" class="img-fluid rounded d-block mb-2" style="max-height:100px;">
                                <?php endif; ?>
                                <input type="file" class="form-control" name="imagen" accept=".jpg,.jpeg,.png,.webp">
                                <small class="text-muted">Dejar vacío para mantener la imagen actual</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Enlace de la imagen (opcional)</label>
                                <select class="form-select" name="imagen_link">
                                    <option value="">— Sin enlace —</option>
                                    <option value="autismo" <?= ($prog['imagen_link'] ?? '') === 'autismo' ? 'selected' : '' ?>>Unidad Municipal de Autismo</option>
                                    <?php foreach ($tramites_sel as $tr): ?>
                                    <option value="tramites/<?= htmlspecialchars($tr['slug']) ?>" <?= ($prog['imagen_link'] ?? '') === 'tramites/' . $tr['slug'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tr['titulo']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Si se selecciona, al dar clic en la imagen llevará a ese servicio.</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Secciones <small class="text-muted">(solo título — cada una genera su propia página)</small></label>
                                <div id="editSections">
                                    <?php foreach ($secciones as $sec): ?>
                                    <div class="section-item" data-seccion-id="<?= (int)$sec['id'] ?>">
                                        <button type="button" class="btn btn-sm btn-action-delete remove-section"><i class="bi bi-x-lg"></i></button>
                                        <label class="form-label small fw-bold">Título</label>
                                        <input type="hidden" name="sec_id[]" value="<?= (int)$sec['id'] ?>">
                                        <input type="text" class="form-control form-control-sm" name="sec_titulo[]" value="<?= htmlspecialchars($sec['titulo']) ?>">
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="addSection">
                                    <i class="bi bi-plus-circle me-1"></i> Agregar sección
                                </button>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Guardar programa</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ── 2. Páginas de secciones ── -->
            <?php if (!empty($secciones)): ?>
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-layout-text-window me-1"></i> Contenido de secciones</div>
                <div class="card-body p-2">
                    <div class="accordion" id="accordionSecciones" data-delete-token="<?= htmlspecialchars($t_delete) ?>" data-prog-id="<?= $id ?>">
                    <?php foreach ($secciones as $idx => $sec): ?>
                    <div class="accordion-item" id="accordionItem<?= (int)$sec['id'] ?>">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed d-flex justify-content-between align-items-center" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#colSec<?= (int)$sec['id'] ?>" aria-expanded="false">
                                <span><?= htmlspecialchars($sec['titulo']) ?></span>
                            </button>
                        </h2>
                        <div id="colSec<?= (int)$sec['id'] ?>" class="accordion-collapse collapse" data-bs-parent="#accordionSecciones">
                            <div class="accordion-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <button type="button" class="btn btn-sm btn-action-delete btn-delete-seccion"
                                            data-seccion-id="<?= (int)$sec['id'] ?>"
                                            data-titulo="<?= htmlspecialchars($sec['titulo']) ?>">
                                        <i class="bi bi-trash3 me-1"></i> Eliminar sección
                                    </button>
                                    <a href="../programas/seccion?slug=<?= urlencode($sec['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye me-1"></i>Ver página</a>
                                </div>
                                <form method="POST" enctype="multipart/form-data" action="programa_editar?id=<?= $id ?>">
                                    <input type="hidden" name="action" value="save_page">
                                    <input type="hidden" name="seccion_id" value="<?= (int)$sec['id'] ?>">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($t_pages) ?>">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Imagen 1 (izquierda)</label>
                                            <?php if (!empty($sec['imagen1_path'])): ?>
                                            <img src="../<?= htmlspecialchars($sec['imagen1_path']) ?>" class="img-fluid rounded mb-2 d-block" style="max-height:120px;object-fit:cover;width:100%;">
                                            <?php endif; ?>
                                            <input type="file" class="form-control form-control-sm" name="imagen1" accept=".jpg,.jpeg,.png,.webp">
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label fw-semibold">Texto 1 (junto a imagen 1)</label>
                                            <textarea class="form-control tinymce-editor" id="texto1_<?= (int)$sec['id'] ?>" name="texto1" rows="6"><?= htmlspecialchars($sec['texto1'] ?? '') ?></textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Imagen 2 (derecha)</label>
                                            <?php if (!empty($sec['imagen2_path'])): ?>
                                            <img src="../<?= htmlspecialchars($sec['imagen2_path']) ?>" class="img-fluid rounded mb-2 d-block" style="max-height:120px;object-fit:cover;width:100%;">
                                            <?php endif; ?>
                                            <input type="file" class="form-control form-control-sm" name="imagen2" accept=".jpg,.jpeg,.png,.webp">
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label fw-semibold">Texto 2 (junto a imagen 2)</label>
                                            <textarea class="form-control tinymce-editor" id="texto2_<?= (int)$sec['id'] ?>" name="texto2" rows="6"><?= htmlspecialchars($sec['texto2'] ?? '') ?></textarea>
                                        </div>
                                        <!-- Contacto por sección -->
                                        <div class="col-12"><hr><p class="fw-semibold mb-2"><i class="bi bi-geo-alt me-1"></i> Información de contacto de esta sección</p></div>
                                        <div class="col-12">
                                            <textarea class="form-control tinymce-editor" id="c_contacto_<?= (int)$sec['id'] ?>" name="c_contacto" rows="6"><?= htmlspecialchars($sec['c_contacto'] ?? '') ?></textarea>
                                            <small class="text-muted">Puedes usar negritas, colores, listas, etc. Si se deja vacío no se mostrará el bloque de contacto.</small>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Guardar sección</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script src="../js/upload-progress.js?v=13"></script>
<script>
const sidebar = document.getElementById('sidebar');
if (window.innerWidth <= 768) sidebar.classList.add('collapsed');
document.getElementById('toggleSidebar').addEventListener('click', () => sidebar.classList.toggle('collapsed'));
const cb = document.getElementById('closeSidebar');
if (cb) cb.addEventListener('click', () => sidebar.classList.add('collapsed'));

// TinyMCE — inicializar al abrir cada acordeón
document.querySelectorAll('.accordion-collapse').forEach(function(collapse) {
    collapse.addEventListener('shown.bs.collapse', function() {
        this.querySelectorAll('.tinymce-editor').forEach(function(ta) {
            if (!tinymce.get(ta.id)) {
                tinymce.init({
                    selector: '#' + ta.id,
                    plugins: 'lists link image table code fullscreen preview wordcount charmap hr emoticons align',
                    toolbar1: 'undo redo | cut copy paste | selectall | fullscreen preview',
                    toolbar2: 'fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | removeformat',
                    toolbar3: 'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | blockquote hr',
                    toolbar4: 'link image table charmap emoticons | code | h1 h2 h3 h4 p',
                    menubar: 'file edit view insert format tools table',
                    height: 320,
                    branding: false, promotion: false, language: 'es',
                    font_family_formats: 'Montserrat=Montserrat,sans-serif;Arial=arial,helvetica,sans-serif;Georgia=georgia,palatino;Tahoma=tahoma,arial,helvetica,sans-serif;Verdana=verdana,geneva;',
                    font_size_formats: '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 24pt 28pt 32pt',
                    content_style: 'body { font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1.6; color: #333; padding: 12px; } p { margin: 0 0 8px 0; }',
                    content_css: false, resize: true,
                    setup: function(ed) { ed.on('change input keyup', function() { ed.save(); }); }
                });
            }
        });
    });
    collapse.addEventListener('hidden.bs.collapse', function() {
        this.querySelectorAll('.tinymce-editor').forEach(function(ta) {
            if (tinymce.get(ta.id)) tinymce.get(ta.id).remove();
        });
    });
});

document.querySelectorAll('form').forEach(f => f.addEventListener('submit', () => tinymce.triggerSave()));

// Eliminar sección vía AJAX
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-delete-seccion');
    if (!btn) return;
    const titulo = btn.getAttribute('data-titulo');
    if (!confirm('¿Eliminar la sección "' + titulo + '"?')) return;
    const seccionId = btn.getAttribute('data-seccion-id');
    const accordion = document.getElementById('accordionSecciones');
    const token     = accordion.getAttribute('data-delete-token');
    const progId    = accordion.getAttribute('data-prog-id');
    const formData  = new FormData();
    formData.append('action', 'delete_seccion');
    formData.append('seccion_id', seccionId);
    formData.append('id', progId);
    formData.append('csrf_token', token);
    fetch('programa_editar?id=' + progId, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const item = document.getElementById('accordionItem' + seccionId);
                if (item) item.remove();
            } else {
                alert('Error al eliminar la sección.');
            }
        })
        .catch(() => alert('Error de conexión.'));
});

// Secciones dinámicas
function createSectionHTML() {
    return `<div class="section-item">
        <button type="button" class="btn btn-sm btn-action-delete remove-section"><i class="bi bi-x-lg"></i></button>
        <label class="form-label small fw-bold">Título</label>
        <input type="hidden" name="sec_id[]" value="0">
        <input type="text" class="form-control form-control-sm" name="sec_titulo[]" placeholder="Ej: Objetivo">
    </div>`;
}
document.getElementById('addSection').addEventListener('click', () => {
    document.getElementById('editSections').insertAdjacentHTML('beforeend', createSectionHTML());
});
document.addEventListener('click', e => {
    const btn = e.target.closest('.remove-section');
    if (!btn) return;
    const item = btn.closest('.section-item');
    const seccionId = item.getAttribute('data-seccion-id');
    // Quitar del DOM de datos
    item.remove();
    // Si tiene seccion_id, eliminar también del acordeón y de la BD
    if (seccionId) {
        const accordionItem = document.getElementById('accordionItem' + seccionId);
        if (accordionItem) accordionItem.remove();
        // Eliminar en BD vía AJAX
        const accordion = document.getElementById('accordionSecciones');
        if (accordion) {
            const token  = accordion.getAttribute('data-delete-token');
            const progId = accordion.getAttribute('data-prog-id');
            const fd = new FormData();
            fd.append('action', 'delete_seccion');
            fd.append('seccion_id', seccionId);
            fd.append('id', progId);
            fd.append('csrf_token', token);
            fetch('programa_editar?id=' + progId, { method: 'POST', body: fd }).catch(() => {});
        }
    }
});
</script>
</body>
</html>
