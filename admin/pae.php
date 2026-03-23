<?php
/**
 * admin/pae.php — CRUD para PAE (Programa Anual de Evaluación)
 * Títulos dinámicos × años con PDFs
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
        $_SESSION['flash_message'] = 'Token CSRF inválido.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: pae.php'); exit;
    }

    // — Crear título
    if ($action === 'add_titulo') {
        $nombre = trim($_POST['nombre'] ?? '');
        if ($nombre === '') {
            $_SESSION['flash_message'] = 'Ingrese un nombre para el título.'; $_SESSION['flash_type'] = 'warning';
            header('Location: pae.php'); exit;
        }
        try {
            $s = $pdo->prepare('SELECT COALESCE(MAX(orden),0)+1 FROM pae_titulos'); $s->execute();
            $ord = (int)$s->fetchColumn();
            $pdo->prepare('INSERT INTO pae_titulos (nombre,orden) VALUES (?,?)')->execute([$nombre,$ord]);
            $_SESSION['flash_message'] = "Título \"{$nombre}\" creado."; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error al guardar.'; $_SESSION['flash_type'] = 'danger';
        }
        header('Location: pae.php'); exit;
    }

    // — Editar título
    if ($action === 'edit_titulo') {
        $tId = (int)($_POST['titulo_id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        if ($tId <= 0 || $nombre === '') {
            $_SESSION['flash_message'] = 'Datos inválidos.'; $_SESSION['flash_type'] = 'warning';
            header('Location: pae.php'); exit;
        }
        try {
            $pdo->prepare('UPDATE pae_titulos SET nombre=? WHERE id=?')->execute([$nombre,$tId]);
            $_SESSION['flash_message'] = 'Título actualizado.'; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error.'; $_SESSION['flash_type'] = 'danger';
        }
        header('Location: pae.php'); exit;
    }

    // — Eliminar título (y sus PDFs en cascada)
    if ($action === 'delete_titulo') {
        $tId = (int)($_POST['titulo_id'] ?? 0);
        try {
            $sp = $pdo->prepare('SELECT pdf_path FROM pae_pdfs WHERE titulo_id=? AND pdf_path IS NOT NULL AND pdf_path!=""');
            $sp->execute([$tId]); $pdfs = $sp->fetchAll();
            $pdo->prepare('DELETE FROM pae_titulos WHERE id=?')->execute([$tId]);
            foreach ($pdfs as $p) { $f = BASE_PATH.'/'.$p['pdf_path']; if (file_exists($f)) unlink($f); }
            $_SESSION['flash_message'] = 'Título y PDFs eliminados.'; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error.'; $_SESSION['flash_type'] = 'danger';
        }
        header('Location: pae.php'); exit;
    }

    // — Agregar año con PDF a un título
    if ($action === 'add_anio') {
        $tId  = (int)($_POST['titulo_id'] ?? 0);
        $anio = trim($_POST['anio'] ?? '');
        if ($tId <= 0 || empty($anio) || !preg_match('/^\d{4}$/', $anio)) {
            $_SESSION['flash_message'] = 'Datos inválidos.'; $_SESSION['flash_type'] = 'warning';
            header('Location: pae.php'); exit;
        }
        if ((int)$anio > (int)date('Y')) {
            $_SESSION['flash_message'] = 'El año no puede ser mayor al año en curso (' . date('Y') . ').'; $_SESSION['flash_type'] = 'warning';
            header('Location: pae.php'); exit;
        }
        $s = $pdo->prepare('SELECT id FROM pae_pdfs WHERE titulo_id=? AND anio=?'); $s->execute([$tId,$anio]);
        if ($s->fetch()) {
            $_SESSION['flash_message'] = "Ya existe el año {$anio} en este título."; $_SESSION['flash_type'] = 'warning';
            header('Location: pae.php'); exit;
        }
        $pdfPath = null;
        if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = handle_upload($_FILES['pdf'], 'pdf');
            if (!$upload['success']) {
                $_SESSION['flash_message'] = $upload['error']; $_SESSION['flash_type'] = 'danger';
                header('Location: pae.php'); exit;
            }
            $pdfPath = $upload['path'];
        }
        try {
            $pdo->prepare('INSERT INTO pae_pdfs (titulo_id,anio,pdf_path) VALUES (?,?,?)')->execute([$tId,$anio,$pdfPath]);
            $_SESSION['flash_message'] = "Año {$anio} agregado."; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error.'; $_SESSION['flash_type'] = 'danger';
        }
        header('Location: pae.php'); exit;
    }

    // — Subir/reemplazar PDF
    if ($action === 'upload_pdf') {
        $pId = (int)($_POST['pdf_id'] ?? 0);
        if ($pId <= 0 || !isset($_FILES['pdf']) || $_FILES['pdf']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_message'] = 'Seleccione un PDF.'; $_SESSION['flash_type'] = 'warning';
            header('Location: pae.php'); exit;
        }
        $upload = handle_upload($_FILES['pdf'], 'pdf');
        if (!$upload['success']) {
            $_SESSION['flash_message'] = $upload['error']; $_SESSION['flash_type'] = 'danger';
            header('Location: pae.php'); exit;
        }
        try {
            $s = $pdo->prepare('SELECT pdf_path FROM pae_pdfs WHERE id=?'); $s->execute([$pId]); $old = $s->fetchColumn();
            if ($old && file_exists(BASE_PATH.'/'.$old)) unlink(BASE_PATH.'/'.$old);
            $pdo->prepare('UPDATE pae_pdfs SET pdf_path=? WHERE id=?')->execute([$upload['path'],$pId]);
            $_SESSION['flash_message'] = 'PDF subido.'; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error.'; $_SESSION['flash_type'] = 'danger';
        }
        header('Location: pae.php'); exit;
    }

    // — Eliminar año/PDF
    if ($action === 'delete_anio') {
        $pId = (int)($_POST['pdf_id'] ?? 0);
        try {
            $s = $pdo->prepare('SELECT pdf_path FROM pae_pdfs WHERE id=?'); $s->execute([$pId]); $old = $s->fetchColumn();
            if ($old && file_exists(BASE_PATH.'/'.$old)) unlink(BASE_PATH.'/'.$old);
            $pdo->prepare('DELETE FROM pae_pdfs WHERE id=?')->execute([$pId]);
            $_SESSION['flash_message'] = 'Registro eliminado.'; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG')&&APP_DEBUG) ? $e->getMessage() : 'Error.'; $_SESSION['flash_type'] = 'danger';
        }
        header('Location: pae.php'); exit;
    }
}

// — Consultar datos
$titulos = []; $pdfsMap = [];
try {
    $titulos = $pdo->query('SELECT * FROM pae_titulos ORDER BY orden ASC')->fetchAll();
    $tIds = array_column($titulos, 'id');
    if (!empty($tIds)) {
        $in = implode(',', array_fill(0, count($tIds), '?'));
        $s = $pdo->prepare("SELECT * FROM pae_pdfs WHERE titulo_id IN ({$in}) ORDER BY anio DESC");
        $s->execute($tIds);
        while ($r = $s->fetch()) { $pdfsMap[(int)$r['titulo_id']][] = $r; }
    }
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) error_log('PAE query error: ' . $e->getMessage());
}

$flashMessage = $_SESSION['flash_message'] ?? ''; $flashType = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);
$token = csrf_token();

$sections = [
    ['title'=>'Slider Principal','file'=>'slider_principal.php','icon'=>'bi-images'],
    ['title'=>'Slider DIF Comunica','file'=>'slider_comunica.php','icon'=>'bi-megaphone'],
    ['title'=>'Noticias','file'=>'noticias.php','icon'=>'bi-newspaper'],
    ['title'=>'Presidencia','file'=>'presidencia.php','icon'=>'bi-person-badge'],
    ['title'=>'Direcciones','file'=>'direcciones.php','icon'=>'bi-people'],
    ['title'=>'Organigrama','file'=>'organigrama.php','icon'=>'bi-diagram-3'],
    ['title'=>'Trámites','file'=>'tramites.php','icon'=>'bi-file-earmark-text'],
    ['title'=>'Galería','file'=>'galeria.php','icon'=>'bi-camera'],
    ['title'=>'SEAC','file'=>'seac.php','icon'=>'bi-file-earmark-pdf'],
    ['title'=>'Cuenta Pública','file'=>'cuenta_publica.php','icon'=>'bi-cash-stack'],
    ['title'=>'Presupuesto Anual','file'=>'presupuesto_anual.php','icon'=>'bi-wallet2'],
    ['title'=>'PAE','file'=>'pae.php','icon'=>'bi-clipboard-data'],
    ['title'=>'Matrices','file'=>'matrices_indicadores.php','icon'=>'bi-bar-chart-line'],
    ['title'=>'CONAC','file'=>'conac.php','icon'=>'bi-bank'],
    ['title'=>'Financiero','file'=>'financiero.php','icon'=>'bi-currency-dollar'],
    ['title'=>'Avisos Privacidad','file'=>'avisos_privacidad.php','icon'=>'bi-shield-exclamation'],
    ['title'=>'Programas','file'=>'programas.php','icon'=>'bi-grid-3x3-gap'],
    ['title'=>'Transparencia','file'=>'transparencia.php','icon'=>'bi-shield-check'],
    ['title'=>'Imagen Institucional','file'=>'institucion.php','icon'=>'bi-card-image'],
    ['title'=>'Footer','file'=>'footer.php','icon'=>'bi-layout-text-window-reverse'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAE — Panel de Administración DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="d-flex">
    <nav id="sidebar" class="sidebar d-flex flex-column">
        <div class="sidebar-header d-flex align-items-center justify-content-between">
            <a href="dashboard.php" class="text-white text-decoration-none"><img src="../img/escudo.png" alt="DIF" style="height:28px;margin-right:6px;vertical-align:middle;"> Admin DIF</a>
            <button class="btn btn-sm btn-outline-light d-md-none" id="closeSidebar" aria-label="Cerrar menú"><i class="bi bi-x-lg"></i></button>
        </div>
        <ul class="nav flex-column mt-2">
            <?php foreach ($sections as $sc): ?>
            <li class="nav-item"><a class="nav-link<?= $sc['file']==='pae.php'?' active':'' ?>" href="<?= htmlspecialchars($sc['file']) ?>"><i class="bi <?= htmlspecialchars($sc['icon']) ?>"></i> <?= htmlspecialchars($sc['title']) ?></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="mt-auto p-3 border-top border-secondary">
            <a href="logout.php" class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión</a>
        </div>
    </nav>
    <div class="main-content">
        <nav class="navbar navbar-light bg-white shadow-sm px-3">
            <button class="btn btn-outline-secondary me-2" id="toggleSidebar" aria-label="Menú"><i class="bi bi-list"></i></button>
            <span class="navbar-brand mb-0 h6">Programa Anual de Evaluación (PAE)</span>
            <a href="logout.php" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-box-arrow-right"></i> Salir</a>
        </nav>
        <div class="container-fluid p-4">
            <?php if ($flashMessage): ?>
            <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($flashMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            <?php endif; ?>

<!-- Crear título -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white"><i class="bi bi-plus-circle me-1"></i> Agregar Título</div>
    <div class="card-body">
        <form method="POST" class="row g-2 align-items-end">
            <input type="hidden" name="action" value="add_titulo">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
            <div class="col-md-9"><label class="form-label">Nombre del título</label><input type="text" name="nombre" class="form-control" placeholder="Ej: Programa Anual de Evaluaciones" required></div>
            <div class="col-md-3"><button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i> Agregar</button></div>
        </form>
    </div>
</div>

<?php if (empty($titulos)): ?>
<div class="text-center text-muted py-4"><i class="bi bi-folder2-open" style="font-size:2rem;"></i><p class="mt-2">No hay títulos creados aún.</p></div>
<?php else: ?>
<?php foreach ($titulos as $titulo): $tPdfs = $pdfsMap[(int)$titulo['id']] ?? []; ?>
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center" style="background-color:#333;color:#fff;">
        <span><i class="bi bi-bookmark-fill me-1"></i> <?= htmlspecialchars($titulo['nombre']) ?></span>
        <div>
            <button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#editT<?= (int)$titulo['id'] ?>"><i class="bi bi-pencil"></i></button>
            <button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#delT<?= (int)$titulo['id'] ?>"><i class="bi bi-trash"></i></button>
        </div>
    </div>
    <div class="card-body">
        <!-- Agregar año -->
        <form method="POST" enctype="multipart/form-data" class="row g-2 align-items-end mb-3">
            <input type="hidden" name="action" value="add_anio">
            <input type="hidden" name="titulo_id" value="<?= (int)$titulo['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
            <div class="col-md-3"><label class="form-label small">Año (máx <?= date('Y') ?>)</label><input type="number" name="anio" class="form-control form-control-sm" min="2000" max="<?= date('Y') ?>" required></div>
            <div class="col-md-5"><label class="form-label small">PDF (opcional)</label><input type="file" name="pdf" class="form-control form-control-sm" accept=".pdf"></div>
            <div class="col-md-4"><button type="submit" class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-plus-lg me-1"></i> Agregar año</button></div>
        </form>

        <?php if (empty($tPdfs)): ?>
        <p class="text-muted small mb-0">Sin años registrados.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Año</th><th class="text-center">PDF</th><th style="width:200px">Acciones</th></tr></thead>
                <tbody>
                <?php foreach ($tPdfs as $pdf): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($pdf['anio']) ?></strong></td>
                    <td class="text-center"><?php if (!empty($pdf['pdf_path'])): ?><span class="badge bg-success">Sí</span><?php else: ?><span class="badge bg-secondary">No</span><?php endif; ?></td>
                    <td>
                        <?php if (empty($pdf['pdf_path'])): ?>
                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#upPdf<?= (int)$pdf['id'] ?>"><i class="bi bi-upload"></i></button>
                        <?php else: ?>
                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#upPdf<?= (int)$pdf['id'] ?>"><i class="bi bi-arrow-repeat"></i></button>
                        <?php endif; ?>
                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este año y su PDF?')">
                            <input type="hidden" name="action" value="delete_anio">
                            <input type="hidden" name="pdf_id" value="<?= (int)$pdf['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <!-- Modal subir PDF -->
                <div class="modal fade" id="upPdf<?= (int)$pdf['id'] ?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="upload_pdf">
                        <input type="hidden" name="pdf_id" value="<?= (int)$pdf['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                        <div class="modal-header"><h5 class="modal-title">Subir PDF — <?= htmlspecialchars($titulo['nombre']) ?> <?= htmlspecialchars($pdf['anio']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
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

<!-- Modal editar título -->
<div class="modal fade" id="editT<?= (int)$titulo['id'] ?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
    <form method="POST">
        <input type="hidden" name="action" value="edit_titulo">
        <input type="hidden" name="titulo_id" value="<?= (int)$titulo['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
        <div class="modal-header"><h5 class="modal-title">Editar título</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><label class="form-label">Nombre</label><input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($titulo['nombre']) ?>" required></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-warning">Guardar</button></div>
    </form>
</div></div></div>

<!-- Modal eliminar título -->
<div class="modal fade" id="delT<?= (int)$titulo['id'] ?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
    <form method="POST">
        <input type="hidden" name="action" value="delete_titulo">
        <input type="hidden" name="titulo_id" value="<?= (int)$titulo['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
        <div class="modal-header"><h5 class="modal-title text-danger">Eliminar título</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><p>¿Eliminar <strong><?= htmlspecialchars($titulo['nombre']) ?></strong> y todos sus años/PDFs?</p></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger">Eliminar</button></div>
    </form>
</div></div></div>
<?php endforeach; endif; ?>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
var sidebar=document.getElementById('sidebar');
if(window.innerWidth<=768)sidebar.classList.add('collapsed');
document.getElementById('toggleSidebar').addEventListener('click',function(){sidebar.classList.toggle('collapsed');});
var cb=document.getElementById('closeSidebar');if(cb)cb.addEventListener('click',function(){sidebar.classList.add('collapsed');});
</script>
</body>
</html>
