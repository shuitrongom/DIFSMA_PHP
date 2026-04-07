<?php
/**
 * admin/presupuesto_anual.php — CRUD para Presupuesto Anual
 * Estructura: Bloques (año) → Conceptos → Sub-años con PDF
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
        header('Location: presupuesto_anual.php' . ($bloqueId > 0 ? "?bloque_id={$bloqueId}" : ''));
        exit;
    }

    // — Crear bloque (año)
    if ($action === 'create_block') {
        $anio = trim($_POST['anio'] ?? '');
        if (empty($anio) || !preg_match('/^\d{4}$/', $anio)) {
            $_SESSION['flash_message'] = 'Año inválido (4 dígitos).'; $_SESSION['flash_type'] = 'warning';
            header('Location: presupuesto_anual.php'); exit;
        }
        if ((int)$anio > (int)date('Y')) {
            $_SESSION['flash_message'] = 'El año no puede ser mayor al año en curso (' . date('Y') . ').'; $_SESSION['flash_type'] = 'warning';
            header('Location: presupuesto_anual.php'); exit;
        }
        try {
            $s = $pdo->prepare('SELECT id FROM pa_bloques WHERE anio = ?'); $s->execute([$anio]);
            if ($s->fetch()) { $_SESSION['flash_message'] = "Ya existe bloque {$anio}."; $_SESSION['flash_type'] = 'warning'; header('Location: presupuesto_anual.php'); exit; }
            $s = $pdo->prepare('SELECT COALESCE(MAX(orden),0)+1 FROM pa_bloques'); $s->execute();
            $ord = (int)$s->fetchColumn();
            $pdo->prepare('INSERT INTO pa_bloques (anio,orden) VALUES (?,?)')->execute([$anio,$ord]);
            $_SESSION['flash_message'] = "Bloque {$anio} creado."; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error al guardar.'; $_SESSION['flash_type'] = 'danger';
        }
        header('Location: presupuesto_anual.php'); exit;
    }

    // — Eliminar bloque
    if ($action === 'delete_block') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) { $_SESSION['flash_message'] = 'ID inválido.'; $_SESSION['flash_type'] = 'danger'; header('Location: presupuesto_anual.php'); exit; }
        try {
            $sp = $pdo->prepare('SELECT p.pdf_path FROM pa_pdfs p INNER JOIN pa_conceptos c ON p.concepto_id=c.id WHERE c.bloque_id=? AND p.pdf_path IS NOT NULL AND p.pdf_path!=""');
            $sp->execute([$id]); $pdfs = $sp->fetchAll();
            $pdo->prepare('DELETE FROM pa_bloques WHERE id=?')->execute([$id]);
            foreach ($pdfs as $p) { $f = BASE_PATH.'/'.$p['pdf_path']; if (file_exists($f)) unlink($f); }
            $_SESSION['flash_message'] = 'Bloque eliminado.'; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error.'; $_SESSION['flash_type'] = 'danger';
        }
        header('Location: presupuesto_anual.php'); exit;
    }

    // — Agregar concepto
    if ($action === 'add_concepto') {
        $bId = (int)($_POST['bloque_id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        if ($bId <= 0 || $nombre === '') {
            $_SESSION['flash_message'] = 'Ingrese un nombre para el concepto.'; $_SESSION['flash_type'] = 'warning';
            header("Location: presupuesto_anual.php?bloque_id={$bId}"); exit;
        }
        try {
            $s = $pdo->prepare('SELECT COALESCE(MAX(orden),0)+1 FROM pa_conceptos WHERE bloque_id=?'); $s->execute([$bId]);
            $ord = (int)$s->fetchColumn();
            $pdo->prepare('INSERT INTO pa_conceptos (bloque_id,nombre,orden) VALUES (?,?,?)')->execute([$bId,$nombre,$ord]);
            $_SESSION['flash_message'] = "Concepto \"{$nombre}\" agregado."; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error.'; $_SESSION['flash_type'] = 'danger';
        }
        header("Location: presupuesto_anual.php?bloque_id={$bId}"); exit;
    }

    // — Editar concepto
    if ($action === 'edit_concepto') {
        $bId = (int)($_POST['bloque_id'] ?? 0);
        $cId = (int)($_POST['concepto_id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        if ($cId <= 0 || $nombre === '') {
            $_SESSION['flash_message'] = 'Datos inválidos.'; $_SESSION['flash_type'] = 'warning';
            header("Location: presupuesto_anual.php?bloque_id={$bId}"); exit;
        }
        try {
            $pdo->prepare('UPDATE pa_conceptos SET nombre=? WHERE id=?')->execute([$nombre,$cId]);
            $_SESSION['flash_message'] = 'Concepto actualizado.'; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error.'; $_SESSION['flash_type'] = 'danger';
        }
        header("Location: presupuesto_anual.php?bloque_id={$bId}"); exit;
    }

    // — Eliminar concepto
    if ($action === 'delete_concepto') {
        $bId = (int)($_POST['bloque_id'] ?? 0);
        $cId = (int)($_POST['concepto_id'] ?? 0);
        try {
            $sp = $pdo->prepare('SELECT pdf_path FROM pa_pdfs WHERE concepto_id=? AND pdf_path IS NOT NULL AND pdf_path!=""');
            $sp->execute([$cId]); $pdfs = $sp->fetchAll();
            $pdo->prepare('DELETE FROM pa_conceptos WHERE id=?')->execute([$cId]);
            foreach ($pdfs as $p) { $f = BASE_PATH.'/'.$p['pdf_path']; if (file_exists($f)) unlink($f); }
            $_SESSION['flash_message'] = 'Concepto eliminado.'; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error.'; $_SESSION['flash_type'] = 'danger';
        }
        header("Location: presupuesto_anual.php?bloque_id={$bId}"); exit;
    }

    // — Agregar sub-año con PDF
    if ($action === 'add_subanio') {
        $bId = (int)($_POST['bloque_id'] ?? 0);
        $cId = (int)($_POST['concepto_id'] ?? 0);
        $subAnio = trim($_POST['sub_anio'] ?? '');
        if ($cId <= 0 || empty($subAnio) || !preg_match('/^\d{4}$/', $subAnio)) {
            $_SESSION['flash_message'] = 'Año inválido.'; $_SESSION['flash_type'] = 'warning';
            header("Location: presupuesto_anual.php?bloque_id={$bId}"); exit;
        }
        // Validar que sub_anio <= año del bloque
        $s = $pdo->prepare('SELECT b.anio FROM pa_bloques b INNER JOIN pa_conceptos c ON c.bloque_id=b.id WHERE c.id=?');
        $s->execute([$cId]); $bloqueAnio = (int)$s->fetchColumn();
        if ((int)$subAnio > $bloqueAnio) {
            $_SESSION['flash_message'] = "El año no puede ser mayor a {$bloqueAnio}."; $_SESSION['flash_type'] = 'warning';
            header("Location: presupuesto_anual.php?bloque_id={$bId}"); exit;
        }
        $pdfPath = null;
        if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = handle_upload($_FILES['pdf'], 'pdf');
            if (!$upload['success']) {
                $_SESSION['flash_message'] = $upload['error']; $_SESSION['flash_type'] = 'danger';
                header("Location: presupuesto_anual.php?bloque_id={$bId}"); exit;
            }
            $pdfPath = $upload['path'];
        }
        try {
            $s = $pdo->prepare('SELECT id FROM pa_pdfs WHERE concepto_id=? AND sub_anio=?'); $s->execute([$cId,$subAnio]);
            if ($s->fetch()) { $_SESSION['flash_message'] = "Ya existe el año {$subAnio} en este concepto."; $_SESSION['flash_type'] = 'warning'; header("Location: presupuesto_anual.php?bloque_id={$bId}"); exit; }
            $pdo->prepare('INSERT INTO pa_pdfs (concepto_id,sub_anio,pdf_path,orden) VALUES (?,?,?,?)')->execute([$cId,$subAnio,$pdfPath,(int)$subAnio]);
            $_SESSION['flash_message'] = "Año {$subAnio} agregado."; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error.'; $_SESSION['flash_type'] = 'danger';
        }
        header("Location: presupuesto_anual.php?bloque_id={$bId}"); exit;
    }

    // — Subir/reemplazar PDF de sub-año
    if ($action === 'upload_pdf') {
        $bId = (int)($_POST['bloque_id'] ?? 0);
        $pId = (int)($_POST['pdf_id'] ?? 0);
        if ($pId <= 0 || !isset($_FILES['pdf']) || $_FILES['pdf']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_message'] = 'Seleccione un PDF.'; $_SESSION['flash_type'] = 'warning';
            header("Location: presupuesto_anual.php?bloque_id={$bId}"); exit;
        }
        $upload = handle_upload($_FILES['pdf'], 'pdf');
        if (!$upload['success']) {
            $_SESSION['flash_message'] = $upload['error']; $_SESSION['flash_type'] = 'danger';
            header("Location: presupuesto_anual.php?bloque_id={$bId}"); exit;
        }
        try {
            $s = $pdo->prepare('SELECT pdf_path FROM pa_pdfs WHERE id=?'); $s->execute([$pId]); $old = $s->fetchColumn();
            if ($old && file_exists(BASE_PATH.'/'.$old)) unlink(BASE_PATH.'/'.$old);
            $pdo->prepare('UPDATE pa_pdfs SET pdf_path=? WHERE id=?')->execute([$upload['path'],$pId]);
            $_SESSION['flash_message'] = 'PDF subido.'; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error.'; $_SESSION['flash_type'] = 'danger';
        }
        header("Location: presupuesto_anual.php?bloque_id={$bId}"); exit;
    }

    // — Eliminar sub-año
    if ($action === 'delete_subanio') {
        $bId = (int)($_POST['bloque_id'] ?? 0);
        $pId = (int)($_POST['pdf_id'] ?? 0);
        try {
            $s = $pdo->prepare('SELECT pdf_path FROM pa_pdfs WHERE id=?'); $s->execute([$pId]); $old = $s->fetchColumn();
            if ($old && file_exists(BASE_PATH.'/'.$old)) unlink(BASE_PATH.'/'.$old);
            $pdo->prepare('DELETE FROM pa_pdfs WHERE id=?')->execute([$pId]);
            $_SESSION['flash_message'] = 'Año eliminado.'; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error.'; $_SESSION['flash_type'] = 'danger';
        }
        header("Location: presupuesto_anual.php?bloque_id={$bId}"); exit;
    }
}

// — Consultar datos
$currentBloque = null; $conceptos = []; $pdfsMap = []; $bloques = [];
try {
    if ($bloqueId > 0) {
        $s = $pdo->prepare('SELECT * FROM pa_bloques WHERE id=?'); $s->execute([$bloqueId]);
        $currentBloque = $s->fetch();
        if ($currentBloque) {
            $s = $pdo->prepare('SELECT * FROM pa_conceptos WHERE bloque_id=? ORDER BY orden ASC');
            $s->execute([$bloqueId]); $conceptos = $s->fetchAll();
            $cIds = array_column($conceptos, 'id');
            if (!empty($cIds)) {
                $in = implode(',', array_fill(0, count($cIds), '?'));
                $s = $pdo->prepare("SELECT * FROM pa_pdfs WHERE concepto_id IN ({$in}) ORDER BY sub_anio DESC");
                $s->execute($cIds);
                while ($r = $s->fetch()) { $pdfsMap[(int)$r['concepto_id']][] = $r; }
            }
        }
    } else {
        $s = $pdo->query('SELECT b.*, (SELECT COUNT(*) FROM pa_pdfs p INNER JOIN pa_conceptos c ON p.concepto_id=c.id WHERE c.bloque_id=b.id AND p.pdf_path IS NOT NULL AND p.pdf_path!="") AS num_pdfs FROM pa_bloques b ORDER BY b.anio DESC');
        $bloques = $s->fetchAll();
    }
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) error_log('Presupuesto Anual query error: ' . $e->getMessage());
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
    <title>Presupuesto Anual — Panel de Administración DIF</title>
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
            <button class="btn btn-outline-secondary me-2" id="toggleSidebar" aria-label="Menú"><i class="bi bi-list"></i></button>
            <span class="navbar-brand mb-0 h6">
                <?php if ($currentBloque): ?>
                    <a href="presupuesto_anual.php" class="text-decoration-none text-muted">Presupuesto Anual</a>
                    <i class="bi bi-chevron-right mx-1 small"></i> <?= htmlspecialchars($currentBloque['anio']) ?>
                <?php else: ?>
                    Presupuesto Anual — Bloques por Año
                <?php endif; ?>
            </span>
            <a href="logout.php" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-box-arrow-right"></i> Salir</a>
        </nav>
        <div class="container-fluid p-4">
                <?php page_help('presupuesto_anual'); ?>
            <?php if ($flashMessage): ?>
            <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($flashMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            <?php endif; ?>

<?php if($bloqueId > 0 && $currentBloque): ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0"><i class="bi bi-calendar-event me-1"></i> Presupuesto Anual <?= htmlspecialchars($currentBloque['anio']) ?></h5>
    <button type="button" class="btn btn-sm btn-action-delete" data-bs-toggle="modal" data-bs-target="#deleteBlockModal"><i class="bi bi-trash3 me-1"></i> Eliminar bloque</button>
</div>

<!-- Agregar concepto -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white"><i class="bi bi-plus-circle me-1"></i> Agregar Concepto</div>
    <div class="card-body">
        <form method="POST" class="row g-2 align-items-end">
            <input type="hidden" name="action" value="add_concepto">
            <input type="hidden" name="bloque_id" value="<?= $bloqueId ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
            <div class="col-md-9"><label class="form-label">Nombre del concepto</label><input type="text" name="nombre" class="form-control" placeholder="Ej: Presupuesto de Egresos" required></div>
            <div class="col-md-3"><button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i> Agregar</button></div>
        </form>
    </div>
</div>

<?php if (empty($conceptos)): ?>
<div class="text-center text-muted py-4"><i class="bi bi-folder2-open" style="font-size:2rem;"></i><p class="mt-2">No hay conceptos aún.</p></div>
<?php else: ?>
<?php foreach ($conceptos as $concepto): $cPdfs = $pdfsMap[(int)$concepto['id']] ?? []; ?>
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center" style="background-color:rgb(107,98,90);color:#fff;cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#cBody<?= (int)$concepto['id'] ?>">
        <span><i class="bi bi-bookmark-fill me-1"></i> <?= htmlspecialchars($concepto['nombre']) ?> <i class="bi bi-chevron-down ms-2 small"></i></span>
        <div onclick="event.stopPropagation()">
            <button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#editC<?= (int)$concepto['id'] ?>"><i class="bi bi-pencil"></i></button>
            <button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#delC<?= (int)$concepto['id'] ?>"><i class="bi bi-trash3"></i></button>
        </div>
    </div>
    <div class="collapse" id="cBody<?= (int)$concepto['id'] ?>">
    <div class="card-body">
        <!-- Agregar sub-año -->
        <form method="POST" enctype="multipart/form-data" class="row g-2 align-items-end mb-3">
            <input type="hidden" name="action" value="add_subanio">
            <input type="hidden" name="bloque_id" value="<?= $bloqueId ?>">
            <input type="hidden" name="concepto_id" value="<?= (int)$concepto['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
            <div class="col-md-3"><label class="form-label small">Año (máx <?= htmlspecialchars($currentBloque['anio']) ?>)</label><input type="number" name="sub_anio" class="form-control form-control-sm" min="2000" max="<?= htmlspecialchars($currentBloque['anio']) ?>" required></div>
            <div class="col-md-5"><label class="form-label small">PDF (opcional)</label><input type="file" name="pdf" class="form-control form-control-sm" accept=".pdf"></div>
            <div class="col-md-4"><button type="submit" class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-plus-lg me-1"></i> Agregar año</button></div>
        </form>

        <?php if (empty($cPdfs)): ?>
        <p class="text-muted small mb-0">Sin años registrados.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Año</th><th class="text-end pe-4">PDF</th><th style="width:200px">Acciones</th></tr></thead>
                <tbody>
                <?php foreach ($cPdfs as $pdf): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($pdf['sub_anio']) ?></strong></td>
                    <td class="text-end pe-4"><?php if (!empty($pdf['pdf_path'])): ?><span class="badge bg-success">Sí</span><?php else: ?><span class="badge bg-secondary">No</span><?php endif; ?></td>
                    <td>
                        <?php if (empty($pdf['pdf_path'])): ?>
                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#upPdf<?= (int)$pdf['id'] ?>"><i class="bi bi-upload"></i></button>
                        <?php else: ?>
                        <button class="btn btn-sm btn-action-key" data-bs-toggle="modal" data-bs-target="#upPdf<?= (int)$pdf['id'] ?>"><i class="bi bi-arrow-repeat"></i></button>
                        <?php endif; ?>
                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este año y su PDF?')">
                            <input type="hidden" name="action" value="delete_subanio">
                            <input type="hidden" name="bloque_id" value="<?= $bloqueId ?>">
                            <input type="hidden" name="pdf_id" value="<?= (int)$pdf['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                            <button class="btn btn-sm btn-action-delete"><i class="bi bi-trash3"></i></button>
                        </form>
                    </td>
                </tr>
                <!-- Modal subir PDF -->
                <div class="modal fade" id="upPdf<?= (int)$pdf['id'] ?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="upload_pdf">
                        <input type="hidden" name="bloque_id" value="<?= $bloqueId ?>">
                        <input type="hidden" name="pdf_id" value="<?= (int)$pdf['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                        <div class="modal-header"><h5 class="modal-title">Subir PDF — Año <?= htmlspecialchars($pdf['sub_anio']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body"><input type="file" name="pdf" class="form-control" accept=".pdf" required></div>
                        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-success">Subir</button></div>
                    </form>
                </div></div></div>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    </div>
</div>

<!-- Modal editar concepto -->
<div class="modal fade" id="editC<?= (int)$concepto['id'] ?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
    <form method="POST">
        <input type="hidden" name="action" value="edit_concepto">
        <input type="hidden" name="bloque_id" value="<?= $bloqueId ?>">
        <input type="hidden" name="concepto_id" value="<?= (int)$concepto['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
        <div class="modal-header"><h5 class="modal-title">Editar concepto</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><label class="form-label">Nombre</label><input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($concepto['nombre']) ?>" required></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-warning">Guardar</button></div>
    </form>
</div></div></div>

<!-- Modal eliminar concepto -->
<div class="modal fade" id="delC<?= (int)$concepto['id'] ?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
    <form method="POST">
        <input type="hidden" name="action" value="delete_concepto">
        <input type="hidden" name="bloque_id" value="<?= $bloqueId ?>">
        <input type="hidden" name="concepto_id" value="<?= (int)$concepto['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
        <div class="modal-header"><h5 class="modal-title text-danger">Eliminar concepto</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><p>¿Eliminar <strong><?= htmlspecialchars($concepto['nombre']) ?></strong> y todos sus años/PDFs?</p></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger">Eliminar</button></div>
    </form>
</div></div></div>
<?php endforeach; endif; ?>

<!-- Modal eliminar bloque -->
<div class="modal fade" id="deleteBlockModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
    <form method="POST">
        <input type="hidden" name="action" value="delete_block">
        <input type="hidden" name="id" value="<?= $bloqueId ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
        <div class="modal-header"><h5 class="modal-title text-danger">Eliminar bloque</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><p>¿Eliminar bloque <strong><?= htmlspecialchars($currentBloque['anio']) ?></strong> y todo su contenido?</p></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger">Eliminar</button></div>
    </form>
</div></div></div>

<?php else: ?>
<!-- Vista de bloques -->
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-primary text-white"><i class="bi bi-plus-circle me-1"></i> Crear bloque por año</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="create_block">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                    <div class="mb-3"><label class="form-label">Año</label><input type="number" name="anio" class="form-control" min="2000" max="<?= date('Y') ?>" placeholder="Ej: <?= date('Y') ?>" required></div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i> Crear bloque</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <?php if (empty($bloques)): ?>
        <div class="text-center text-muted py-5"><i class="bi bi-inbox" style="font-size:3rem;"></i><p class="mt-2">No hay bloques creados.</p></div>
        <?php else: ?>
        <div class="card">
            <div class="card-header bg-primary text-white"><i class="bi bi-list-ul me-1"></i> Bloques existentes</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Año</th><th class="text-center">PDFs</th><th>Acciones</th></tr></thead>
                        <tbody>
                        <?php foreach ($bloques as $b): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($b['anio']) ?></strong></td>
                            <td class="text-center"><span class="badge bg-primary"><?= (int)$b['num_pdfs'] ?></span></td>
                            <td><a href="presupuesto_anual.php?bloque_id=<?= (int)$b['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i> Ver</a></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/upload-progress.js?v=13"></script>
<script>
var sidebar=document.getElementById('sidebar');
if(window.innerWidth<=768)sidebar.classList.add('collapsed');
document.getElementById('toggleSidebar').addEventListener('click',function(){sidebar.classList.toggle('collapsed');});
var cb=document.getElementById('closeSidebar');if(cb)cb.addEventListener('click',function(){sidebar.classList.add('collapsed');});
</script>
</body>
</html>





