<?php
/**
 * admin/matrices_indicadores.php — CRUD Matrices de Indicadores
 * Años con PDFs
 */
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload_handler.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';
    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF inválido.'; $_SESSION['flash_type'] = 'danger';
        header('Location: matrices_indicadores.php'); exit;
    }

    // — Agregar año
    if ($action === 'add_anio') {
        $anio = trim($_POST['anio'] ?? '');
        if (empty($anio) || !preg_match('/^\d{4}$/', $anio)) {
            $_SESSION['flash_message'] = 'Año inválido (4 dígitos).'; $_SESSION['flash_type'] = 'warning';
            header('Location: matrices_indicadores.php'); exit;
        }
        if ((int)$anio > (int)date('Y')) {
            $_SESSION['flash_message'] = 'El año no puede ser mayor al año en curso (' . date('Y') . ').'; $_SESSION['flash_type'] = 'warning';
            header('Location: matrices_indicadores.php'); exit;
        }
        $s = $pdo->prepare('SELECT id FROM mi_pdfs WHERE anio=?'); $s->execute([$anio]);
        if ($s->fetch()) {
            $_SESSION['flash_message'] = "Ya existe el año {$anio}."; $_SESSION['flash_type'] = 'warning';
            header('Location: matrices_indicadores.php'); exit;
        }
        $pdfPath = null;
        if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = handle_upload($_FILES['pdf'], 'pdf');
            if (!$upload['success']) { $_SESSION['flash_message'] = $upload['error']; $_SESSION['flash_type'] = 'danger'; header('Location: matrices_indicadores.php'); exit; }
            $pdfPath = $upload['path'];
        }
        try {
            $pdo->prepare('INSERT INTO mi_pdfs (anio,pdf_path,orden) VALUES (?,?,?)')->execute([$anio,$pdfPath,(int)$anio]);
            $_SESSION['flash_message'] = "Año {$anio} agregado."; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error.'; $_SESSION['flash_type'] = 'danger';
        }
        header('Location: matrices_indicadores.php'); exit;
    }

    // — Editar año (y opcionalmente reemplazar PDF)
    if ($action === 'edit_anio') {
        $pId = (int)($_POST['pdf_id'] ?? 0);
        $anio = trim($_POST['anio'] ?? '');
        if ($pId <= 0 || empty($anio) || !preg_match('/^\d{4}$/', $anio)) {
            $_SESSION['flash_message'] = 'Datos inválidos.'; $_SESSION['flash_type'] = 'warning';
            header('Location: matrices_indicadores.php'); exit;
        }
        // Verificar que no exista otro registro con el mismo año
        $s = $pdo->prepare('SELECT id FROM mi_pdfs WHERE anio=? AND id!=?'); $s->execute([$anio, $pId]);
        if ($s->fetch()) {
            $_SESSION['flash_message'] = "Ya existe otro registro con el año {$anio}."; $_SESSION['flash_type'] = 'warning';
            header('Location: matrices_indicadores.php'); exit;
        }
        $pdfPath = null;
        if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = handle_upload($_FILES['pdf'], 'pdf');
            if (!$upload['success']) { $_SESSION['flash_message'] = $upload['error']; $_SESSION['flash_type'] = 'danger'; header('Location: matrices_indicadores.php'); exit; }
            $s = $pdo->prepare('SELECT pdf_path FROM mi_pdfs WHERE id=?'); $s->execute([$pId]); $old = $s->fetchColumn();
            if ($old && file_exists(BASE_PATH.'/'.$old)) unlink(BASE_PATH.'/'.$old);
            $pdfPath = $upload['path'];
        }
        try {
            if ($pdfPath) {
                $pdo->prepare('UPDATE mi_pdfs SET anio=?, pdf_path=?, orden=? WHERE id=?')->execute([$anio, $pdfPath, (int)$anio, $pId]);
            } else {
                $pdo->prepare('UPDATE mi_pdfs SET anio=?, orden=? WHERE id=?')->execute([$anio, (int)$anio, $pId]);
            }
            $_SESSION['flash_message'] = 'Registro actualizado.'; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error.'; $_SESSION['flash_type'] = 'danger';
        }
        header('Location: matrices_indicadores.php'); exit;
    }

    // — Subir/reemplazar PDF
    if ($action === 'upload_pdf') {
        $pId = (int)($_POST['pdf_id'] ?? 0);
        if ($pId <= 0 || !isset($_FILES['pdf']) || $_FILES['pdf']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_message'] = 'Seleccione un PDF.'; $_SESSION['flash_type'] = 'warning';
            header('Location: matrices_indicadores.php'); exit;
        }
        $upload = handle_upload($_FILES['pdf'], 'pdf');
        if (!$upload['success']) { $_SESSION['flash_message'] = $upload['error']; $_SESSION['flash_type'] = 'danger'; header('Location: matrices_indicadores.php'); exit; }
        try {
            $s = $pdo->prepare('SELECT pdf_path FROM mi_pdfs WHERE id=?'); $s->execute([$pId]); $old = $s->fetchColumn();
            if ($old && file_exists(BASE_PATH.'/'.$old)) unlink(BASE_PATH.'/'.$old);
            $pdo->prepare('UPDATE mi_pdfs SET pdf_path=? WHERE id=?')->execute([$upload['path'],$pId]);
            $_SESSION['flash_message'] = 'PDF subido.'; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error.'; $_SESSION['flash_type'] = 'danger';
        }
        header('Location: matrices_indicadores.php'); exit;
    }

    // — Eliminar año
    if ($action === 'delete_anio') {
        $pId = (int)($_POST['pdf_id'] ?? 0);
        try {
            $s = $pdo->prepare('SELECT pdf_path FROM mi_pdfs WHERE id=?'); $s->execute([$pId]); $old = $s->fetchColumn();
            if ($old && file_exists(BASE_PATH.'/'.$old)) unlink(BASE_PATH.'/'.$old);
            $pdo->prepare('DELETE FROM mi_pdfs WHERE id=?')->execute([$pId]);
            $_SESSION['flash_message'] = 'Año eliminado.'; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error.'; $_SESSION['flash_type'] = 'danger';
        }
        header('Location: matrices_indicadores.php'); exit;
    }
}

// — Consultar datos
$registros = [];
try {
    $registros = $pdo->query('SELECT * FROM mi_pdfs ORDER BY anio DESC')->fetchAll();
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) error_log('MI query error: ' . $e->getMessage());
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
    <title>Matrices de Indicadores — Panel de Administración DIF</title>
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
            <button class="btn btn-outline-secondary me-2" id="toggleSidebar" aria-label="Menú"><i class="bi bi-list"></i></button>
            <span class="navbar-brand mb-0 h6">Matrices de Indicadores</span>
            <a href="logout.php" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-box-arrow-right"></i> Salir</a>
        </nav>
        <div class="container-fluid p-4">
            <?php if ($flashMessage): ?>
            <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($flashMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            <?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-primary text-white"><i class="bi bi-plus-circle me-1"></i> Agregar Año</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_anio">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                    <div class="mb-3"><label class="form-label">Año (máx <?= date('Y') ?>)</label><input type="number" name="anio" class="form-control" min="2000" max="<?= date('Y') ?>" placeholder="Ej: <?= date('Y') ?>" required></div>
                    <div class="mb-3"><label class="form-label">PDF (opcional)</label><input type="file" name="pdf" class="form-control" accept=".pdf"></div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i> Agregar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <?php if (empty($registros)): ?>
        <div class="text-center text-muted py-5"><i class="bi bi-inbox" style="font-size:3rem;"></i><p class="mt-2">No hay años creados.</p></div>
        <?php else: ?>
        <div class="card">
            <div class="card-header bg-primary text-white"><i class="bi bi-list-ul me-1"></i> Años registrados</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Año</th><th class="text-center">PDF</th><th style="width:200px">Acciones</th></tr></thead>
                        <tbody>
                        <?php foreach ($registros as $r): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($r['anio']) ?></strong></td>
                            <td class="text-center"><?php if (!empty($r['pdf_path'])): ?><span class="badge bg-success">Sí</span><?php else: ?><span class="badge bg-secondary">No</span><?php endif; ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editAnio<?= (int)$r['id'] ?>"><i class="bi bi-pencil"></i></button>
                                <?php if (empty($r['pdf_path'])): ?>
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#upPdf<?= (int)$r['id'] ?>"><i class="bi bi-upload"></i> Subir PDF</button>
                                <?php else: ?>
                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#upPdf<?= (int)$r['id'] ?>"><i class="bi bi-arrow-repeat"></i> Cambiar PDF</button>
                                <?php endif; ?>
                                <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este año y su PDF?')">
                                    <input type="hidden" name="action" value="delete_anio">
                                    <input type="hidden" name="pdf_id" value="<?= (int)$r['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <div class="modal fade" id="upPdf<?= (int)$r['id'] ?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="upload_pdf">
                                <input type="hidden" name="pdf_id" value="<?= (int)$r['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                <div class="modal-header"><h5 class="modal-title">Subir PDF — <?= htmlspecialchars($r['anio']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body"><input type="file" name="pdf" class="form-control" accept=".pdf" required></div>
                                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-success">Subir</button></div>
                            </form>
                        </div></div></div>
                        <div class="modal fade" id="editAnio<?= (int)$r['id'] ?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="edit_anio">
                                <input type="hidden" name="pdf_id" value="<?= (int)$r['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                <div class="modal-header"><h5 class="modal-title">Editar — <?= htmlspecialchars($r['anio']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <div class="mb-3"><label class="form-label">Año</label><input type="number" name="anio" class="form-control" min="2000" max="<?= date('Y') ?>" value="<?= htmlspecialchars($r['anio']) ?>" required></div>
                                    <div class="mb-3"><label class="form-label">Reemplazar PDF (opcional)</label><input type="file" name="pdf" class="form-control" accept=".pdf"></div>
                                </div>
                                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-warning"><i class="bi bi-pencil me-1"></i> Guardar</button></div>
                            </form>
                        </div></div></div>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

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
