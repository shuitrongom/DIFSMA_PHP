<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload_handler.php';
require_once __DIR__ . '/../includes/db.php';
$pdo = get_db();
$secId = (int) ($_GET['id'] ?? 0);
$bloqueId = (int) ($_GET['bloque_id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM trans_secciones WHERE id = ?');
$stmt->execute([$secId]);
$seccion = $stmt->fetch();
if (!$seccion) { header('Location: transparencia_dinamica.php'); exit; }
$plantilla = $seccion['plantilla'];
$baseUrl = "transparencia_seccion.php?id={$secId}";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';
    if (!csrf_validate($token)) { $_SESSION['flash_message'] = 'Token CSRF invalido.'; $_SESSION['flash_type'] = 'danger'; header("Location: {$baseUrl}"); exit; }
    if ($action === 'create_block') {
        $anio = trim($_POST['anio'] ?? '');
        $s = $pdo->prepare('SELECT id FROM trans_bloques WHERE seccion_id=? AND anio=?'); $s->execute([$secId, $anio]);
        if ($s->fetch()) { $_SESSION['flash_message'] = "Ya existe {$anio}."; $_SESSION['flash_type'] = 'warning'; header("Location: {$baseUrl}"); exit; }
        $s = $pdo->prepare('SELECT COALESCE(MAX(orden),0)+1 FROM trans_bloques WHERE seccion_id=?'); $s->execute([$secId]);
        $pdo->prepare('INSERT INTO trans_bloques (seccion_id,anio,orden) VALUES (?,?,?)')->execute([$secId, $anio, (int)$s->fetchColumn()]);
        $_SESSION['flash_message'] = "Bloque {$anio} creado."; $_SESSION['flash_type'] = 'success'; header("Location: {$baseUrl}"); exit;
    }
    if ($action === 'delete_block') {
        $id = (int)($_POST['block_id'] ?? 0);
        $pdo->prepare('DELETE FROM trans_bloques WHERE id=? AND seccion_id=?')->execute([$id, $secId]);
        $_SESSION['flash_message'] = 'Bloque eliminado.'; $_SESSION['flash_type'] = 'success'; header("Location: {$baseUrl}"); exit;
    }
    if ($action === 'add_titulo') {
        $nombre = trim($_POST['nombre'] ?? ''); $bId = (int)($_POST['bloque_id'] ?? 0);
        $pdo->prepare('INSERT INTO trans_titulos (seccion_id,bloque_id,nombre,orden) VALUES (?,?,?,1)')->execute([$secId, $bId > 0 ? $bId : null, $nombre]);
        $_SESSION['flash_message'] = 'Titulo agregado.'; $_SESSION['flash_type'] = 'success'; header("Location: {$baseUrl}" . ($bId > 0 ? "&bloque_id={$bId}" : '')); exit;
    }
    if ($action === 'delete_titulo') {
        $pdo->prepare('DELETE FROM trans_titulos WHERE id=? AND seccion_id=?')->execute([(int)($_POST['titulo_id'] ?? 0), $secId]);
        $_SESSION['flash_message'] = 'Titulo eliminado.'; $_SESSION['flash_type'] = 'success'; header("Location: {$baseUrl}"); exit;
    }
    if ($action === 'add_concepto') {
        $nombre = trim($_POST['nombre'] ?? ''); $bId = (int)($_POST['bloque_id'] ?? 0); $pdfPath = null;
        if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] !== UPLOAD_ERR_NO_FILE) { $u = handle_upload($_FILES['pdf'], 'pdf'); if ($u['success']) $pdfPath = $u['path']; }
        $pdo->prepare('INSERT INTO trans_conceptos (seccion_id,bloque_id,nombre,pdf_path,orden) VALUES (?,?,?,?,1)')->execute([$secId, $bId > 0 ? $bId : null, $nombre, $pdfPath]);
        $_SESSION['flash_message'] = 'Concepto agregado.'; $_SESSION['flash_type'] = 'success'; header("Location: {$baseUrl}&bloque_id={$bId}"); exit;
    }
    if ($action === 'delete_concepto') {
        $cId = (int)($_POST['concepto_id'] ?? 0);
        $pdo->prepare('DELETE FROM trans_pdfs WHERE concepto_id=?')->execute([$cId]);
        $pdo->prepare('DELETE FROM trans_conceptos WHERE id=? AND seccion_id=?')->execute([$cId, $secId]);
        $_SESSION['flash_message'] = 'Concepto eliminado.'; $_SESSION['flash_type'] = 'success'; header("Location: {$baseUrl}&bloque_id={$bloqueId}"); exit;
    }
    if ($action === 'upload_pdf') {
        $cId = (int)($_POST['concepto_id'] ?? 0); $tId = (int)($_POST['titulo_id'] ?? 0); $anio = $_POST['anio'] ?? null; $trim = $_POST['trimestre'] ?? null;
        if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] === UPLOAD_ERR_NO_FILE) { $_SESSION['flash_message'] = 'Seleccione PDF.'; $_SESSION['flash_type'] = 'warning'; header("Location: {$baseUrl}&bloque_id={$bloqueId}"); exit; }
        $u = handle_upload($_FILES['pdf'], 'pdf'); if (!$u['success']) { $_SESSION['flash_message'] = $u['error']; $_SESSION['flash_type'] = 'danger'; header("Location: {$baseUrl}&bloque_id={$bloqueId}"); exit; }
        $w = 'seccion_id=?'; $p = [$secId];
        if ($cId > 0) { $w .= ' AND concepto_id=?'; $p[] = $cId; }
        if ($tId > 0) { $w .= ' AND titulo_id=?'; $p[] = $tId; }
        if ($trim) { $w .= ' AND trimestre=?'; $p[] = (int)$trim; }
        if ($anio) { $w .= ' AND anio=?'; $p[] = $anio; }
        $st = $pdo->prepare("SELECT id FROM trans_pdfs WHERE {$w}"); $st->execute($p); $ex = $st->fetch();
        if ($ex) { $pdo->prepare('UPDATE trans_pdfs SET pdf_path=? WHERE id=?')->execute([$u['path'], $ex['id']]); }
        else { $pdo->prepare('INSERT INTO trans_pdfs (seccion_id,concepto_id,titulo_id,anio,trimestre,pdf_path) VALUES (?,?,?,?,?,?)')->execute([$secId, $cId > 0 ? $cId : null, $tId > 0 ? $tId : null, $anio, $trim ? (int)$trim : null, $u['path']]); }
        $_SESSION['flash_message'] = 'PDF subido.'; $_SESSION['flash_type'] = 'success'; header("Location: {$baseUrl}&bloque_id={$bloqueId}"); exit;
    }
    if ($action === 'delete_pdf') {
        $pdo->prepare('DELETE FROM trans_pdfs WHERE id=? AND seccion_id=?')->execute([(int)($_POST['pdf_id'] ?? 0), $secId]);
        $_SESSION['flash_message'] = 'PDF eliminado.'; $_SESSION['flash_type'] = 'success'; header("Location: {$baseUrl}&bloque_id={$bloqueId}"); exit;
    }
}
$bloques = $pdo->prepare('SELECT * FROM trans_bloques WHERE seccion_id=? ORDER BY anio DESC'); $bloques->execute([$secId]); $bloques = $bloques->fetchAll();
$currentBloque = null;
if ($bloqueId > 0) { $s = $pdo->prepare('SELECT * FROM trans_bloques WHERE id=? AND seccion_id=?'); $s->execute([$bloqueId, $secId]); $currentBloque = $s->fetch(); }
$flashMessage = $_SESSION['flash_message'] ?? ''; $flashType = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);
$token = csrf_token();
$plN = ['seac'=>'SEAC','cuenta_publica'=>'Cuenta Publica','presupuesto_anual'=>'Presupuesto Anual','pae'=>'PAE','matrices'=>'Matrices','conac'=>'CONAC','financiero'=>'Financiero'];
?><!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= htmlspecialchars($seccion['nombre']) ?></title>
<link rel="icon" href="../img/favicon-32x32.png" sizes="35x35"><link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../css/admin.css?v=7"></head><body><div class="d-flex">
<?php require_once __DIR__.'/sidebar_sections.php'; render_admin_sidebar($sidebar_groups, $current_admin_file); ?>
<div class="main-content">
<nav class="navbar navbar-light bg-white shadow-sm px-3">
<button class="btn btn-outline-secondary me-2" id="toggleSidebar"><i class="bi bi-list"></i></button>
<span class="navbar-brand mb-0 h6">
<a href="transparencia_dinamica.php" class="text-decoration-none text-muted">Secciones</a> <i class="bi bi-chevron-right mx-2 small text-muted"></i>
<?= htmlspecialchars($seccion['nombre']) ?>
<small class="text-muted ms-2">(<?= $plN[$plantilla] ?? $plantilla ?>)</small>
<?php if ($currentBloque): ?> <i class="bi bi-chevron-right mx-2 small text-muted"></i> <?= (int)$currentBloque['anio'] ?><?php endif; ?>
</span>
<a href="logout.php" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-box-arrow-right"></i> Salir</a>
</nav>
<div class="container-fluid p-4">
<?php if ($flashMessage): ?><div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show"><?= htmlspecialchars($flashMessage) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<?php if ($plantilla === 'matrices'):
    $mP = $pdo->prepare('SELECT * FROM trans_pdfs WHERE seccion_id=? ORDER BY anio DESC'); $mP->execute([$secId]); $mP = $mP->fetchAll(); ?>
<div class="row g-4">
<div class="col-lg-4"><div class="card"><div class="card-header bg-danger text-white">Agregar</div><div class="card-body">
<form method="POST" enctype="multipart/form-data" action="<?= $baseUrl ?>"><input type="hidden" name="action" value="upload_pdf"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
<div class="mb-3"><label class="form-label">Anio</label><input type="number" class="form-control" name="anio" min="2000" max="<?= date('Y') ?>" placeholder="Ej: 2026" required></div>
<div class="mb-3"><label class="form-label">PDF</label><input type="file" class="form-control" name="pdf" accept=".pdf" required></div>
<button type="submit" class="btn btn-danger w-100"><i class="bi bi-upload me-1"></i> Subir</button></form></div></div></div>
<div class="col-lg-8"><div class="card"><div class="card-header">Registros <span class="badge bg-secondary"><?= count($mP) ?></span></div><div class="card-body p-0">
<?php if (empty($mP)): ?><div class="text-center text-muted py-4">Sin registros.</div>
<?php else: ?><table class="table table-hover mb-0"><thead class="table-light"><tr><th>Anio</th><th>PDF</th><th></th></tr></thead><tbody>
<?php foreach ($mP as $p): ?><tr><td><span class="badge bg-danger"><?= $p['anio'] ?></span></td>
<td><?php if (!empty($p['pdf_path'])): ?><a href="../<?= htmlspecialchars($p['pdf_path']) ?>" target="_blank" class="btn btn-sm btn-outline-success"><i class="bi bi-file-pdf"></i></a><?php endif; ?></td>
<td><form method="POST" action="<?= $baseUrl ?>" class="d-inline" onsubmit="return confirm('Eliminar?')"><input type="hidden" name="action" value="delete_pdf"><input type="hidden" name="pdf_id" value="<?= (int)$p['id'] ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>"><button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr>
<?php endforeach; ?></tbody></table><?php endif; ?></div></div></div></div>

<?php elseif ($plantilla === 'pae'):
    $pT = $pdo->prepare('SELECT * FROM trans_titulos WHERE seccion_id=? AND bloque_id IS NULL ORDER BY orden ASC'); $pT->execute([$secId]); $pT = $pT->fetchAll();
    $pPT = []; $s = $pdo->prepare('SELECT * FROM trans_pdfs WHERE seccion_id=? AND titulo_id IS NOT NULL ORDER BY anio DESC'); $s->execute([$secId]); foreach ($s->fetchAll() as $p) { $pPT[(int)$p['titulo_id']][] = $p; } ?>
<div class="card mb-3"><div class="card-header bg-success text-white">Agregar titulo</div><div class="card-body">
<form method="POST" action="<?= $baseUrl ?>" class="row g-2 align-items-end"><input type="hidden" name="action" value="add_titulo"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
<div class="col-md-9"><input type="text" class="form-control" name="nombre" required placeholder="Nombre"></div>
<div class="col-md-3"><button type="submit" class="btn btn-success w-100">Agregar</button></div></form></div></div>
<?php foreach ($pT as $tit): $tPs = $pPT[(int)$tit['id']] ?? []; ?>
<div class="card mb-3"><div class="card-header d-flex justify-content-between"><span><?= htmlspecialchars($tit['nombre']) ?></span>
<form method="POST" action="<?= $baseUrl ?>" class="d-inline" onsubmit="return confirm('Eliminar?')"><input type="hidden" name="action" value="delete_titulo"><input type="hidden" name="titulo_id" value="<?= (int)$tit['id'] ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>"><button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></div>
<div class="card-body">
<form method="POST" enctype="multipart/form-data" action="<?= $baseUrl ?>" class="row g-2 mb-2"><input type="hidden" name="action" value="upload_pdf"><input type="hidden" name="titulo_id" value="<?= (int)$tit['id'] ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
<div class="col-3"><input type="number" class="form-control form-control-sm" name="anio" min="2000" max="<?= date('Y') ?>" placeholder="Ej: 2026" required></div>
<div class="col-6"><input type="file" class="form-control form-control-sm" name="pdf" accept=".pdf" required></div>
<div class="col-3"><button type="submit" class="btn btn-sm btn-danger w-100">Subir</button></div></form>
<?php foreach ($tPs as $p): ?><div class="d-flex gap-2 mb-1"><span class="badge bg-danger"><?= $p['anio'] ?></span><a href="../<?= htmlspecialchars($p['pdf_path']) ?>" target="_blank"><i class="bi bi-file-pdf text-success"></i></a>
<form method="POST" action="<?= $baseUrl ?>" class="d-inline" onsubmit="return confirm('Eliminar?')"><input type="hidden" name="action" value="delete_pdf"><input type="hidden" name="pdf_id" value="<?= (int)$p['id'] ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>"><button type="submit" class="btn btn-sm btn-outline-danger py-0"><i class="bi bi-trash"></i></button></form></div><?php endforeach; ?>
</div></div><?php endforeach; ?>

<?php elseif (in_array($plantilla, ['seac','conac','cuenta_publica','presupuesto_anual','financiero'])):
    if ($bloqueId > 0 && $currentBloque):
        $bC = $pdo->prepare('SELECT * FROM trans_conceptos WHERE seccion_id=? AND bloque_id=? ORDER BY orden ASC'); $bC->execute([$secId, $bloqueId]); $bC = $bC->fetchAll();
        $bPM = [];
        if (in_array($plantilla, ['seac','conac'])) { $s = $pdo->prepare('SELECT * FROM trans_pdfs WHERE seccion_id=? AND concepto_id IN (SELECT id FROM trans_conceptos WHERE bloque_id=?)'); $s->execute([$secId, $bloqueId]); foreach ($s->fetchAll() as $r) { $bPM[(int)$r['concepto_id']][(int)$r['trimestre']] = $r; } }
        $bPC = [];
        if ($plantilla === 'presupuesto_anual') { $s = $pdo->prepare('SELECT * FROM trans_pdfs WHERE seccion_id=? AND concepto_id IN (SELECT id FROM trans_conceptos WHERE bloque_id=?) ORDER BY anio DESC'); $s->execute([$secId, $bloqueId]); foreach ($s->fetchAll() as $r) { $bPC[(int)$r['concepto_id']][] = $r; } }
?>
<div class="d-flex justify-content-between mb-3"><h5>Bloque <?= (int)$currentBloque['anio'] ?></h5>
<div><a href="<?= $baseUrl ?>" class="btn btn-outline-secondary btn-sm">Volver</a>
<form method="POST" action="<?= $baseUrl ?>" class="d-inline" onsubmit="return confirm('Eliminar bloque?')"><input type="hidden" name="action" value="delete_block"><input type="hidden" name="block_id" value="<?= (int)$currentBloque['id'] ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>"><button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button></form></div></div>
<div class="card mb-3"><div class="card-header bg-success text-white">Agregar concepto</div><div class="card-body">
<form method="POST" <?= in_array($plantilla, ['financiero','cuenta_publica']) ? 'enctype="multipart/form-data"' : '' ?> action="<?= $baseUrl ?>&bloque_id=<?= $bloqueId ?>" class="row g-2 align-items-end">
<input type="hidden" name="action" value="add_concepto"><input type="hidden" name="bloque_id" value="<?= $bloqueId ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
<div class="<?= in_array($plantilla, ['financiero','cuenta_publica']) ? 'col-md-5' : 'col-md-9' ?>"><input type="text" class="form-control" name="nombre" required placeholder="Nombre"></div>
<?php if (in_array($plantilla, ['financiero','cuenta_publica'])): ?><div class="col-md-4"><input type="file" class="form-control" name="pdf" accept=".pdf"></div><?php endif; ?>
<div class="col-md-3"><button type="submit" class="btn btn-success w-100">Agregar</button></div></form></div></div>
<?php if (empty($bC)): ?><div class="alert alert-info">Sin conceptos.</div>
<?php else: ?><div class="card"><div class="card-header">Conceptos <span class="badge bg-secondary"><?= count($bC) ?></span></div><div class="card-body p-0"><div class="table-responsive">
<table class="table table-bordered table-hover align-middle mb-0"><thead class="table-light"><tr><th>Concepto</th>
<?php if (in_array($plantilla, ['seac','conac'])): ?><th class="text-center" style="min-width:140px">Q1</th><th class="text-center" style="min-width:140px">Q2</th><th class="text-center" style="min-width:140px">Q3</th><th class="text-center" style="min-width:140px">Q4</th>
<?php elseif ($plantilla === 'financiero' || $plantilla === 'cuenta_publica'): ?><th class="text-center">PDF</th>
<?php elseif ($plantilla === 'presupuesto_anual'): ?><th>Sub-anios</th><?php endif; ?>
<th style="width:80px" class="text-center">Accion</th></tr></thead><tbody>
<?php foreach ($bC as $c): ?><tr><td><?= htmlspecialchars($c['nombre']) ?></td>
<?php if (in_array($plantilla, ['seac','conac'])):
    for ($t = 1; $t <= 4; $t++): $cl = $bPM[(int)$c['id']][$t] ?? null; ?><td class="text-center">
<?php if ($cl && !empty($cl['pdf_path'])): ?><a href="../<?= htmlspecialchars($cl['pdf_path']) ?>" target="_blank" class="btn btn-sm btn-outline-danger mb-1"><i class="bi bi-file-pdf"></i></a>
<form method="POST" action="<?= $baseUrl ?>&bloque_id=<?= $bloqueId ?>" class="d-inline" onsubmit="return confirm('Eliminar?')"><input type="hidden" name="action" value="delete_pdf"><input type="hidden" name="pdf_id" value="<?= (int)$cl['id'] ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>"><button type="submit" class="btn btn-sm btn-outline-danger mb-1"><i class="bi bi-trash"></i></button></form><br><?php endif; ?>
<form method="POST" enctype="multipart/form-data" action="<?= $baseUrl ?>&bloque_id=<?= $bloqueId ?>"><input type="hidden" name="action" value="upload_pdf"><input type="hidden" name="concepto_id" value="<?= (int)$c['id'] ?>"><input type="hidden" name="trimestre" value="<?= $t ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
<div class="input-group input-group-sm"><input type="file" class="form-control form-control-sm" name="pdf" accept=".pdf" required><button type="submit" class="btn btn-sm btn-success"><i class="bi bi-upload"></i></button></div></form></td>
<?php endfor; ?>
<?php elseif ($plantilla === 'financiero' || $plantilla === 'cuenta_publica'): ?><td class="text-center"><?php if (!empty($c['pdf_path'])): ?><a href="../<?= htmlspecialchars($c['pdf_path']) ?>" target="_blank" class="btn btn-sm btn-outline-success"><i class="bi bi-file-pdf"></i></a><?php else: ?>-<?php endif; ?></td>
<?php elseif ($plantilla === 'presupuesto_anual'): ?><td>
<form method="POST" enctype="multipart/form-data" action="<?= $baseUrl ?>&bloque_id=<?= $bloqueId ?>" class="row g-1 mb-2"><input type="hidden" name="action" value="upload_pdf"><input type="hidden" name="concepto_id" value="<?= (int)$c['id'] ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
<div class="col-4"><input type="number" class="form-control form-control-sm" name="anio" min="2000" max="<?= date('Y') ?>" placeholder="Ej: 2026" required></div><div class="col-5"><input type="file" class="form-control form-control-sm" name="pdf" accept=".pdf" required></div><div class="col-3"><button type="submit" class="btn btn-sm btn-success w-100"><i class="bi bi-upload"></i></button></div></form>
<?php foreach (($bPC[(int)$c['id']] ?? []) as $p): ?><div class="d-flex gap-2 mb-1"><span class="badge bg-danger"><?= $p['anio'] ?></span><a href="../<?= htmlspecialchars($p['pdf_path']) ?>" target="_blank"><i class="bi bi-file-pdf text-success"></i></a>
<form method="POST" action="<?= $baseUrl ?>&bloque_id=<?= $bloqueId ?>" class="d-inline" onsubmit="return confirm('Eliminar?')"><input type="hidden" name="action" value="delete_pdf"><input type="hidden" name="pdf_id" value="<?= (int)$p['id'] ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>"><button type="submit" class="btn btn-sm btn-outline-danger py-0"><i class="bi bi-trash"></i></button></form></div><?php endforeach; ?></td>
<?php endif; ?>
<td class="text-center"><form method="POST" action="<?= $baseUrl ?>&bloque_id=<?= $bloqueId ?>" class="d-inline" onsubmit="return confirm('Eliminar concepto?')"><input type="hidden" name="action" value="delete_concepto"><input type="hidden" name="concepto_id" value="<?= (int)$c['id'] ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>"><button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td>
</tr><?php endforeach; ?></tbody></table></div></div></div><?php endif; ?>
<?php else: ?>
<div class="row g-4"><div class="col-lg-4"><div class="card"><div class="card-header bg-danger text-white">Crear bloque</div><div class="card-body">
<form method="POST" action="<?= $baseUrl ?>"><input type="hidden" name="action" value="create_block"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
<div class="mb-3"><label class="form-label">Anio</label><input type="number" class="form-control" name="anio" min="2000" max="<?= date('Y') ?>" placeholder="Ej: 2026" required></div>
<button type="submit" class="btn btn-danger w-100">Crear</button></form></div></div></div>
<div class="col-lg-8"><div class="card"><div class="card-header">Bloques <span class="badge bg-secondary"><?= count($bloques) ?></span></div><div class="card-body p-0">
<?php if (empty($bloques)): ?><div class="text-center text-muted py-4">Sin bloques.</div>
<?php else: ?><table class="table table-hover mb-0"><thead class="table-light"><tr><th>Anio</th><th>Acciones</th></tr></thead><tbody>
<?php foreach ($bloques as $bl): ?><tr><td><span class="badge bg-danger fs-6"><?= (int)$bl['anio'] ?></span></td>
<td><a href="<?= $baseUrl ?>&bloque_id=<?= (int)$bl['id'] ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-eye"></i> Ver</a>
<form method="POST" action="<?= $baseUrl ?>" class="d-inline" onsubmit="return confirm('Eliminar?')"><input type="hidden" name="action" value="delete_block"><input type="hidden" name="block_id" value="<?= (int)$bl['id'] ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>"><button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td>
</tr><?php endforeach; ?></tbody></table><?php endif; ?></div></div></div></div>
<?php endif; ?>
<?php else: ?><div class="alert alert-warning">Plantilla no implementada.</div><?php endif; ?>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>const sb=document.getElementById('sidebar');if(window.innerWidth<=768)sb.classList.add('collapsed');document.getElementById('toggleSidebar').addEventListener('click',function(){sb.classList.toggle('collapsed');});const cb=document.getElementById('closeSidebar');if(cb)cb.addEventListener('click',function(){sb.classList.add('collapsed');});</script>
<style>
@media (max-width: 768px) {
    .navbar-brand { font-size: 12px !important; white-space: normal !important; word-break: break-word; line-height: 1.4; }
    .navbar-brand .bi-chevron-right { display: none; }
    .navbar-brand small { display: block; margin-left: 0 !important; margin-top: 4px; }
}
</style>
</body></html>
