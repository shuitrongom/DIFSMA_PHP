<?php
/**
 * admin/seac.php — CRUD para SEAC (Bloques por año, Conceptos por bloque, PDFs)
 */
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload_handler.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();
$bloqueId = isset($_GET['bloque_id']) ? (int) $_GET['bloque_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';
    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF inválido.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: seac.php' . ($bloqueId > 0 ? "?bloque_id={$bloqueId}" : ''));
        exit;
    }

    // CREATE BLOCK
    if ($action === 'create_block') {
        $anio = trim($_POST['anio'] ?? '');
        if (empty($anio) || !preg_match('/^\d{4}$/', $anio)) {
            $_SESSION['flash_message'] = 'Año inválido (4 dígitos).';
            $_SESSION['flash_type'] = 'warning';
            header('Location: seac.php'); exit;
        }
        if ((int)$anio > (int)date('Y')) {
            $_SESSION['flash_message'] = 'El año no puede ser mayor al año en curso (' . date('Y') . ').';
            $_SESSION['flash_type'] = 'warning';
            header('Location: seac.php'); exit;
        }
        try {
            $s = $pdo->prepare('SELECT id FROM seac_bloques WHERE anio = ?'); $s->execute([$anio]);
            if ($s->fetch()) { $_SESSION['flash_message'] = "Ya existe bloque {$anio}."; $_SESSION['flash_type'] = 'warning'; header('Location: seac.php'); exit; }
            $s = $pdo->prepare('SELECT COALESCE(MAX(orden),0)+1 FROM seac_bloques'); $s->execute();
            $ord = (int)$s->fetchColumn();
            $pdo->prepare('INSERT INTO seac_bloques (anio,orden) VALUES (?,?)')->execute([$anio,$ord]);
            $_SESSION['flash_message'] = "Bloque {$anio} creado. Agregue conceptos.";
            $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error al guardar.';
            $_SESSION['flash_type'] = 'danger';
        }
        header('Location: seac.php'); exit;
    }

    // ADD CONCEPTO
    if ($action === 'add_concepto') {
        $bId = (int)($_POST['bloque_id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        if ($bId <= 0 || $nombre === '') {
            $_SESSION['flash_message'] = 'Ingrese un nombre para el concepto.';
            $_SESSION['flash_type'] = 'warning';
            header("Location: seac.php?bloque_id={$bId}"); exit;
        }
        try {
            $s = $pdo->prepare('SELECT COALESCE(MAX(numero),0)+1 FROM seac_conceptos WHERE bloque_id=?'); $s->execute([$bId]);
            $num = (int)$s->fetchColumn();
            $pdo->prepare('INSERT INTO seac_conceptos (bloque_id,numero,nombre,orden) VALUES (?,?,?,?)')->execute([$bId,$num,$nombre,$num]);
            $_SESSION['flash_message'] = "Concepto \"{$nombre}\" agregado.";
            $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error al guardar.';
            $_SESSION['flash_type'] = 'danger';
        }
        header("Location: seac.php?bloque_id={$bId}"); exit;
    }

    // EDIT CONCEPTO
    if ($action === 'edit_concepto') {
        $bId = (int)($_POST['bloque_id'] ?? 0);
        $cId = (int)($_POST['concepto_id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        if ($cId <= 0 || $nombre === '') {
            $_SESSION['flash_message'] = 'Datos inválidos.';
            $_SESSION['flash_type'] = 'warning';
            header("Location: seac.php?bloque_id={$bId}"); exit;
        }
        try {
            $pdo->prepare('UPDATE seac_conceptos SET nombre=? WHERE id=? AND bloque_id=?')->execute([$nombre,$cId,$bId]);
            $_SESSION['flash_message'] = 'Concepto actualizado.';
            $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error al actualizar.';
            $_SESSION['flash_type'] = 'danger';
        }
        header("Location: seac.php?bloque_id={$bId}"); exit;
    }

    // DELETE CONCEPTO
    if ($action === 'delete_concepto') {
        $bId = (int)($_POST['bloque_id'] ?? 0);
        $cId = (int)($_POST['concepto_id'] ?? 0);
        if ($cId <= 0) {
            $_SESSION['flash_message'] = 'ID inválido.';
            $_SESSION['flash_type'] = 'danger';
            header("Location: seac.php?bloque_id={$bId}"); exit;
        }
        try {
            $s = $pdo->prepare('SELECT pdf_path FROM seac_pdfs WHERE concepto_id=? AND bloque_id=? AND pdf_path IS NOT NULL AND pdf_path!=""');
            $s->execute([$cId,$bId]); $pdfs = $s->fetchAll();
            $pdo->prepare('DELETE FROM seac_pdfs WHERE concepto_id=? AND bloque_id=?')->execute([$cId,$bId]);
            $pdo->prepare('DELETE FROM seac_conceptos WHERE id=? AND bloque_id=?')->execute([$cId,$bId]);
            foreach ($pdfs as $p) { $f = BASE_PATH.'/'.$p['pdf_path']; if (file_exists($f)) unlink($f); }
            // Renumber
            $s = $pdo->prepare('SELECT id FROM seac_conceptos WHERE bloque_id=? ORDER BY orden ASC'); $s->execute([$bId]);
            $n=1; $u=$pdo->prepare('UPDATE seac_conceptos SET numero=?,orden=? WHERE id=?');
            foreach ($s->fetchAll() as $r) { $u->execute([$n,$n,$r['id']]); $n++; }
            $_SESSION['flash_message'] = 'Concepto y PDFs eliminados.';
            $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error al eliminar.';
            $_SESSION['flash_type'] = 'danger';
        }
        header("Location: seac.php?bloque_id={$bId}"); exit;
    }

    // UPLOAD PDF
    if ($action === 'upload_pdf') {
        $bId = (int)($_POST['bloque_id'] ?? 0);
        $cId = (int)($_POST['concepto_id'] ?? 0);
        $trim = (int)($_POST['trimestre'] ?? 0);
        if ($bId<=0||$cId<=0||$trim<1||$trim>4) {
            $_SESSION['flash_message']='Parámetros inválidos.'; $_SESSION['flash_type']='danger';
            header("Location: seac.php?bloque_id={$bId}"); exit;
        }
        if (!isset($_FILES['pdf'])||$_FILES['pdf']['error']===UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_message']='Seleccione un PDF.'; $_SESSION['flash_type']='warning';
            header("Location: seac.php?bloque_id={$bId}"); exit;
        }
        $upload = handle_upload($_FILES['pdf'], 'pdf');
        if (!$upload['success']) {
            $_SESSION['flash_message']=$upload['error']; $_SESSION['flash_type']='danger';
            header("Location: seac.php?bloque_id={$bId}"); exit;
        }
        try {
            $s=$pdo->prepare('SELECT id,pdf_path FROM seac_pdfs WHERE bloque_id=? AND concepto_id=? AND trimestre=?');
            $s->execute([$bId,$cId,$trim]); $ex=$s->fetch();
            if ($ex) {
                if (!empty($ex['pdf_path'])) { $old=BASE_PATH.'/'.$ex['pdf_path']; if(file_exists($old)) unlink($old); }
                $pdo->prepare('UPDATE seac_pdfs SET pdf_path=? WHERE id=?')->execute([$upload['path'],$ex['id']]);
            } else {
                $pdo->prepare('INSERT INTO seac_pdfs (bloque_id,concepto_id,trimestre,pdf_path) VALUES (?,?,?,?)')->execute([$bId,$cId,$trim,$upload['path']]);
            }
            $_SESSION['flash_message']='PDF subido.'; $_SESSION['flash_type']='success';
        } catch (PDOException $e) {
            $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error al guardar.';
            $_SESSION['flash_type']='danger';
        }
        header("Location: seac.php?bloque_id={$bId}"); exit;
    }

    // DELETE PDF
    if ($action === 'delete_pdf') {
        $pdfId=(int)($_POST['pdf_id']??0); $bId=(int)($_POST['bloque_id']??0);
        if ($pdfId<=0) { $_SESSION['flash_message']='ID inválido.'; $_SESSION['flash_type']='danger'; header("Location: seac.php?bloque_id={$bId}"); exit; }
        $s=$pdo->prepare('SELECT pdf_path FROM seac_pdfs WHERE id=?'); $s->execute([$pdfId]); $row=$s->fetch();
        if (!$row) { $_SESSION['flash_message']='PDF no encontrado.'; $_SESSION['flash_type']='danger'; header("Location: seac.php?bloque_id={$bId}"); exit; }
        try {
            $pdo->prepare('DELETE FROM seac_pdfs WHERE id=?')->execute([$pdfId]);
            if (!empty($row['pdf_path'])) { $f=BASE_PATH.'/'.$row['pdf_path']; if(file_exists($f)) unlink($f); }
            $_SESSION['flash_message']='PDF eliminado.'; $_SESSION['flash_type']='success';
        } catch (PDOException $e) {
            $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error al eliminar.';
            $_SESSION['flash_type']='danger';
        }
        header("Location: seac.php?bloque_id={$bId}"); exit;
    }

    // DELETE BLOCK
    if ($action === 'delete_block') {
        $id=(int)($_POST['id']??0);
        if ($id<=0) { $_SESSION['flash_message']='ID inválido.'; $_SESSION['flash_type']='danger'; header('Location: seac.php'); exit; }
        $s=$pdo->prepare('SELECT id,anio FROM seac_bloques WHERE id=?'); $s->execute([$id]); $bl=$s->fetch();
        if (!$bl) { $_SESSION['flash_message']='Bloque no encontrado.'; $_SESSION['flash_type']='danger'; header('Location: seac.php'); exit; }
        try {
            $sp=$pdo->prepare('SELECT pdf_path FROM seac_pdfs WHERE bloque_id=? AND pdf_path IS NOT NULL AND pdf_path!=""');
            $sp->execute([$id]); $pdfs=$sp->fetchAll();
            $pdo->prepare('DELETE FROM seac_bloques WHERE id=?')->execute([$id]);
            foreach ($pdfs as $p) { $f=BASE_PATH.'/'.$p['pdf_path']; if(file_exists($f)) unlink($f); }
            $_SESSION['flash_message']="Bloque {$bl['anio']} eliminado."; $_SESSION['flash_type']='success';
        } catch (PDOException $e) {
            $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error al eliminar.';
            $_SESSION['flash_type']='danger';
        }
        header('Location: seac.php'); exit;
    }
}

// ── Consultar datos ────────────────────────────────────────────────────────
$currentBloque = null; $conceptos = []; $pdfsMap = []; $bloques = [];

try {
    if ($bloqueId > 0) {
        $s = $pdo->prepare('SELECT * FROM seac_bloques WHERE id=?'); $s->execute([$bloqueId]);
        $currentBloque = $s->fetch();
        if ($currentBloque) {
            $s = $pdo->prepare('SELECT * FROM seac_conceptos WHERE bloque_id=? ORDER BY orden ASC');
            $s->execute([$bloqueId]); $conceptos = $s->fetchAll();
            $s = $pdo->prepare('SELECT id,concepto_id,trimestre,pdf_path FROM seac_pdfs WHERE bloque_id=?');
            $s->execute([$bloqueId]);
            while ($r = $s->fetch()) { $pdfsMap[(int)$r['concepto_id']][(int)$r['trimestre']] = ['id'=>(int)$r['id'],'pdf_path'=>$r['pdf_path']]; }
        }
    } else {
        $s = $pdo->query('SELECT b.*,COUNT(p.id) AS num_pdfs FROM seac_bloques b LEFT JOIN seac_pdfs p ON p.bloque_id=b.id AND p.pdf_path IS NOT NULL AND p.pdf_path!="" GROUP BY b.id ORDER BY b.anio DESC');
        $bloques = $s->fetchAll();
    }
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) { error_log('SEAC query error: ' . $e->getMessage()); }
}

$flashMessage = $_SESSION['flash_message'] ?? ''; $flashType = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);
$token = csrf_token();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEAC — Panel de Administración DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css?v=7">
    <style>
        .seac-cell{min-width:160px;vertical-align:middle}
    </style>
</head>
<body>
<div class="d-flex">
    <?php require_once __DIR__ . '/sidebar_sections.php'; render_admin_sidebar($sidebar_groups, $current_admin_file); ?>

    <div class="main-content">
        <nav class="navbar navbar-light bg-white shadow-sm px-3">
            <button class="btn btn-outline-secondary me-2" id="toggleSidebar" aria-label="Menú"><i class="bi bi-list"></i></button>
            <span class="navbar-brand mb-0 h6">
                <?php if ($currentBloque): ?>
                    <a href="seac.php" class="text-decoration-none text-muted">SEAC</a>
                    <i class="bi bi-chevron-right mx-1 small"></i> Bloque <?= htmlspecialchars($currentBloque['anio']) ?>
                <?php else: ?>
                    SEAC — Bloques por Año
                <?php endif; ?>
            </span>
            <a href="logout.php" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-box-arrow-right"></i> Salir</a>
        </nav>

        <div class="container-fluid p-4">
            <?php if ($flashMessage): ?>
            <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($flashMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            <?php endif; ?>

<?php if ($bloqueId > 0 && $currentBloque): ?>
            <!-- ══════ DETALLE BLOQUE ══════ -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="bi bi-calendar-event me-1"></i> Bloque SEAC <?= htmlspecialchars($currentBloque['anio']) ?></h5>
                <button type="button" class="btn btn-sm btn-action-delete" data-bs-toggle="modal" data-bs-target="#deleteBlockModal"><i class="bi bi-trash3 me-1"></i> Eliminar bloque</button>
            </div>

            <!-- Formulario agregar concepto -->
            <div class="card mb-3">
                <div class="card-header bg-success text-white"><i class="bi bi-plus-circle me-1"></i> Agregar concepto</div>
                <div class="card-body">
                    <form method="POST" action="seac.php?bloque_id=<?= (int)$currentBloque['id'] ?>" class="row g-2 align-items-end">
                        <input type="hidden" name="action" value="add_concepto">
                        <input type="hidden" name="bloque_id" value="<?= (int)$currentBloque['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                        <div class="col-md-9">
                            <label for="nombre_concepto" class="form-label">Nombre del concepto</label>
                            <input type="text" class="form-control" id="nombre_concepto" name="nombre" required placeholder="Ej: Estado de Situación Financiera">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success w-100"><i class="bi bi-plus-lg me-1"></i> Agregar</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (empty($conceptos)): ?>
            <div class="alert alert-info"><i class="bi bi-info-circle me-1"></i> Este bloque no tiene conceptos aún. Use el formulario de arriba para agregar.</div>
            <?php else: ?>
            <div class="card">
                <div class="card-header"><i class="bi bi-table me-1"></i> Trimestres × Conceptos</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Concepto</th>
                                    <th class="seac-cell text-center">1er Trimestre</th>
                                    <th class="seac-cell text-center">2do Trimestre</th>
                                    <th class="seac-cell text-center">3er Trimestre</th>
                                    <th class="seac-cell text-center">4to Trimestre</th>
                                    <th style="width:140px" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
<?php foreach ($conceptos as $c): ?>
                                <tr>
                                    <td class="text-start"><?= htmlspecialchars($c['numero'].'.- '.$c['nombre']) ?></td>
<?php for ($t=1;$t<=4;$t++): $cell=$pdfsMap[(int)$c['id']][$t]??null; ?>
                                    <td class="seac-cell text-center">
<?php if ($cell && !empty($cell['pdf_path'])): ?>
                                        <a href="../<?= htmlspecialchars($cell['pdf_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mb-1"><i class="bi bi-file-earmark-pdf me-1"></i>Ver</a>
                                        <form method="POST" enctype="multipart/form-data" action="seac.php?bloque_id=<?= (int)$currentBloque['id'] ?>" class="mb-1">
                                            <input type="hidden" name="action" value="upload_pdf">
                                            <input type="hidden" name="bloque_id" value="<?= (int)$currentBloque['id'] ?>">
                                            <input type="hidden" name="concepto_id" value="<?= (int)$c['id'] ?>">
                                            <input type="hidden" name="trimestre" value="<?= $t ?>">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                            <div class="input-group input-group-sm"><input type="file" class="form-control form-control-sm" name="pdf" accept=".pdf" required><button type="submit" class="btn btn-sm btn-warning"><i class="bi bi-arrow-repeat"></i></button></div>
                                        </form>
                                        <form method="POST" action="seac.php?bloque_id=<?= (int)$currentBloque['id'] ?>" onsubmit="return confirm('¿Eliminar este PDF?');">
                                            <input type="hidden" name="action" value="delete_pdf">
                                            <input type="hidden" name="pdf_id" value="<?= (int)$cell['id'] ?>">
                                            <input type="hidden" name="bloque_id" value="<?= (int)$currentBloque['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                            <button type="submit" class="btn btn-sm btn-action-delete"><i class="bi bi-file-earmark-x"></i></button>
                                        </form>
<?php else: ?>
                                        <form method="POST" enctype="multipart/form-data" action="seac.php?bloque_id=<?= (int)$currentBloque['id'] ?>">
                                            <input type="hidden" name="action" value="upload_pdf">
                                            <input type="hidden" name="bloque_id" value="<?= (int)$currentBloque['id'] ?>">
                                            <input type="hidden" name="concepto_id" value="<?= (int)$c['id'] ?>">
                                            <input type="hidden" name="trimestre" value="<?= $t ?>">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                            <div class="input-group input-group-sm"><input type="file" class="form-control form-control-sm" name="pdf" accept=".pdf" required><button type="submit" class="btn btn-sm btn-success"><i class="bi bi-upload"></i></button></div>
                                        </form>
<?php endif; ?>
                                    </td>
<?php endfor; ?>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary mb-1" data-bs-toggle="modal" data-bs-target="#editC<?= (int)$c['id'] ?>"><i class="bi bi-pencil"></i></button>
                                        <form method="POST" action="seac.php?bloque_id=<?= (int)$currentBloque['id'] ?>" class="d-inline" onsubmit="return confirm('¿Eliminar concepto y sus PDFs?');">
                                            <input type="hidden" name="action" value="delete_concepto">
                                            <input type="hidden" name="bloque_id" value="<?= (int)$currentBloque['id'] ?>">
                                            <input type="hidden" name="concepto_id" value="<?= (int)$c['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                            <button type="submit" class="btn btn-sm btn-action-delete"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <!-- Modal editar concepto -->
                                <div class="modal fade" id="editC<?= (int)$c['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog"><div class="modal-content">
                                        <form method="POST" action="seac.php?bloque_id=<?= (int)$currentBloque['id'] ?>">
                                            <input type="hidden" name="action" value="edit_concepto">
                                            <input type="hidden" name="bloque_id" value="<?= (int)$currentBloque['id'] ?>">
                                            <input type="hidden" name="concepto_id" value="<?= (int)$c['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil me-1"></i> Editar concepto</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
                                            <div class="modal-body"><div class="mb-3"><label class="form-label">Nombre</label><input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($c['nombre']) ?>" required></div></div>
                                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Guardar</button></div>
                                        </form>
                                    </div></div>
                                </div>
<?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Modal eliminar bloque -->
            <div class="modal fade" id="deleteBlockModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog"><div class="modal-content">
                    <form method="POST" action="seac.php">
                        <input type="hidden" name="action" value="delete_block">
                        <input type="hidden" name="id" value="<?= (int)$currentBloque['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                        <div class="modal-header"><h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Eliminar bloque</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
                        <div class="modal-body">
                            <p>¿Eliminar bloque <strong>SEAC <?= htmlspecialchars($currentBloque['anio']) ?></strong>?</p>
                            <p class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i> Se eliminarán todos los conceptos y PDFs. No se puede deshacer.</p>
                        </div>
                        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger"><i class="bi bi-trash3 me-1"></i> Eliminar</button></div>
                    </form>
                </div></div>
            </div>

<?php elseif ($bloqueId > 0 && !$currentBloque): ?>
            <div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-1"></i> Bloque no encontrado. <a href="seac.php" class="alert-link">Volver</a>.</div>

<?php else: ?>
            <!-- ══════ LISTADO BLOQUES ══════ -->
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white"><i class="bi bi-plus-circle me-1"></i> Crear bloque SEAC</div>
                        <div class="card-body">
                            <form method="POST" action="seac.php">
                                <input type="hidden" name="action" value="create_block">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                <div class="mb-3"><label for="anio" class="form-label">Año</label><input type="number" class="form-control" id="anio" name="anio" min="2000" max="<?= date('Y') ?>" placeholder="Ej: <?= date('Y') ?>" required></div>
                                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-folder-plus me-1"></i> Crear bloque</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header"><i class="bi bi-file-earmark-pdf me-1"></i> Bloques SEAC <span class="badge bg-secondary ms-1"><?= count($bloques) ?></span></div>
                        <div class="card-body p-0">
<?php if (empty($bloques)): ?>
                            <div class="text-center text-muted py-4"><i class="bi bi-file-earmark-pdf" style="font-size:2rem"></i><p class="mt-2 mb-0">No hay bloques. Cree uno.</p></div>
<?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light"><tr><th>Año</th><th style="width:100px" class="text-center">PDFs</th><th style="width:200px">Acciones</th></tr></thead>
                                    <tbody>
<?php foreach ($bloques as $bl): ?>
                                        <tr>
                                            <td><span class="badge bg-info text-dark fs-6"><i class="bi bi-calendar-event me-1"></i><?= htmlspecialchars($bl['anio']) ?></span></td>
                                            <td class="text-center"><span class="badge bg-primary"><?= (int)$bl['num_pdfs'] ?></span></td>
                                            <td>
                                                <a href="seac.php?bloque_id=<?= (int)$bl['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Ver</a>
                                                <button type="button" class="btn btn-sm btn-action-delete" data-bs-toggle="modal" data-bs-target="#delBL<?= (int)$bl['id'] ?>"><i class="bi bi-trash3"></i> Eliminar</button>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="delBL<?= (int)$bl['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog"><div class="modal-content">
                                                <form method="POST" action="seac.php">
                                                    <input type="hidden" name="action" value="delete_block">
                                                    <input type="hidden" name="id" value="<?= (int)$bl['id'] ?>">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                                    <div class="modal-header"><h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Eliminar bloque</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
                                                    <div class="modal-body"><p>¿Eliminar <strong>SEAC <?= htmlspecialchars($bl['anio']) ?></strong>?</p><p class="text-danger small"><?= (int)$bl['num_pdfs'] ?> PDFs serán eliminados.</p></div>
                                                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger"><i class="bi bi-trash3 me-1"></i> Eliminar</button></div>
                                                </form>
                                            </div></div>
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
<?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/upload-progress.js?v=13"></script>
<script>
const sidebar=document.getElementById('sidebar');
if(window.innerWidth<=768) sidebar.classList.add('collapsed');
document.getElementById('toggleSidebar').addEventListener('click',()=>sidebar.classList.toggle('collapsed'));
const cb=document.getElementById('closeSidebar');
if(cb) cb.addEventListener('click',()=>sidebar.classList.add('collapsed'));
</script>
</body>
</html>




