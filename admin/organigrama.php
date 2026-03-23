<?php
/**
 * admin/organigrama.php — Gestión del PDF de Organigrama
 *
 * Requisitos: 7.1, 7.2
 */

require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload_handler.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();

// ── Procesamiento POST ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF inválido. Intente de nuevo.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: organigrama.php');
        exit;
    }

    $titulo = trim($_POST['titulo'] ?? '');

    if (empty($titulo)) {
        $_SESSION['flash_message'] = 'El título es obligatorio.';
        $_SESSION['flash_type']    = 'warning';
        header('Location: organigrama.php');
        exit;
    }

    // Obtener registro actual
    $stmt = $pdo->query('SELECT * FROM organigrama ORDER BY id ASC LIMIT 1');
    $current = $stmt->fetch();

    $pdfPath = $current ? $current['pdf_path'] : null;

    // Si se envió un nuevo archivo, procesarlo
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = handle_upload($_FILES['pdf'], 'pdf');

        if (!$upload['success']) {
            $_SESSION['flash_message'] = $upload['error'];
            $_SESSION['flash_type']    = 'danger';
            header('Location: organigrama.php');
            exit;
        }

        // Eliminar PDF anterior si existe
        if ($current && !empty($current['pdf_path'])) {
            $oldFile = BASE_PATH . '/' . $current['pdf_path'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        $pdfPath = $upload['path'];
    }

    try {
        if ($current) {
            $stmt = $pdo->prepare(
                'UPDATE organigrama SET titulo = ?, pdf_path = ? WHERE id = ?'
            );
            $stmt->execute([$titulo, $pdfPath, $current['id']]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO organigrama (titulo, pdf_path) VALUES (?, ?)'
            );
            $stmt->execute([$titulo, $pdfPath]);
        }

        $_SESSION['flash_message'] = 'Organigrama actualizado correctamente.';
        $_SESSION['flash_type']    = 'success';
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al guardar en la base de datos.';
        $_SESSION['flash_type']    = 'danger';
    }

    header('Location: organigrama.php');
    exit;
}

// ── Consultar registro actual ──────────────────────────────────────────────────
$stmt = $pdo->query('SELECT * FROM organigrama ORDER BY id ASC LIMIT 1');
$organigrama = $stmt->fetch();

// ── Flash messages ─────────────────────────────────────────────────────────────
$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType    = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);

// Generar token CSRF para el formulario
$token = csrf_token();

// Secciones del sidebar (misma lista que dashboard)
$sections = [
    ['title' => 'Slider Principal',   'file' => 'slider_principal.php', 'icon' => 'bi-images'],
    ['title' => 'Slider DIF Comunica','file' => 'slider_comunica.php',  'icon' => 'bi-megaphone'],
    ['title' => 'Noticias',           'file' => 'noticias.php',         'icon' => 'bi-newspaper'],
    ['title' => 'Presidencia',        'file' => 'presidencia.php',      'icon' => 'bi-person-badge'],
    ['title' => 'Direcciones',        'file' => 'direcciones.php',      'icon' => 'bi-people'],
    ['title' => 'Organigrama',        'file' => 'organigrama.php',      'icon' => 'bi-diagram-3'],
    ['title' => 'Trámites',           'file' => 'tramites.php',         'icon' => 'bi-file-earmark-text'],
    ['title' => 'Galería',            'file' => 'galeria.php',          'icon' => 'bi-camera'],
    ['title' => 'SEAC',               'file' => 'seac.php',             'icon' => 'bi-file-earmark-pdf'],
    ['title' => 'Cuenta Pública',     'file' => 'cuenta_publica.php',   'icon' => 'bi-cash-stack'],
    ['title' => 'Presupuesto Anual',  'file' => 'presupuesto_anual.php', 'icon' => 'bi-wallet2'],
    ['title' => 'PAE',               'file' => 'pae.php',              'icon' => 'bi-clipboard-data'],
    ['title' => 'Matrices',          'file' => 'matrices_indicadores.php', 'icon' => 'bi-bar-chart-line'],
    ['title' => 'CONAC',             'file' => 'conac.php',             'icon' => 'bi-bank'],
    ['title' => 'Financiero',        'file' => 'financiero.php',       'icon' => 'bi-currency-dollar'],
    ['title' => 'Avisos Privacidad', 'file' => 'avisos_privacidad.php','icon' => 'bi-shield-exclamation'],
    ['title' => 'Programas',          'file' => 'programas.php',        'icon' => 'bi-grid-3x3-gap'],
    ['title' => 'Transparencia',      'file' => 'transparencia.php',    'icon' => 'bi-shield-check'],
    ['title' => 'Imagen Institucional','file' => 'institucion.php',     'icon' => 'bi-card-image'],
    ['title' => 'Footer',             'file' => 'footer.php',           'icon' => 'bi-layout-text-window-reverse'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organigrama — Panel de Administración DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .pdf-preview { max-height: 300px; border-radius: 8px; }
    </style>
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar d-flex flex-column">
            <div class="sidebar-header d-flex align-items-center justify-content-between">
                <a href="dashboard.php" class="text-white text-decoration-none">
                    <img src="../img/escudo.png" alt="DIF" style="height:28px;margin-right:6px;vertical-align:middle;"> Admin DIF
                </a>
                <button class="btn btn-sm btn-outline-light d-md-none" id="closeSidebar" aria-label="Cerrar menú">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <ul class="nav flex-column mt-2">
                <?php foreach ($sections as $s): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $s['file'] === 'organigrama.php' ? ' active' : '' ?>" href="<?= htmlspecialchars($s['file']) ?>">
                            <i class="bi <?= htmlspecialchars($s['icon']) ?>"></i>
                            <?= htmlspecialchars($s['title']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="mt-auto p-3 border-top border-secondary">
                <a href="logout.php" class="btn btn-outline-danger btn-sm w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
                </a>
            </div>
        </nav>

        <!-- Main content -->
        <div class="main-content">
            <!-- Top bar -->
            <nav class="navbar navbar-light bg-white shadow-sm px-3">
                <button class="btn btn-outline-secondary me-2" id="toggleSidebar" aria-label="Abrir/cerrar menú">
                    <i class="bi bi-list"></i>
                </button>
                <span class="navbar-brand mb-0 h6">Organigrama</span>
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
                    <!-- Vista previa actual -->
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-diagram-3 me-1"></i> PDF actual
                            </div>
                            <div class="card-body text-center">
                                <?php if ($organigrama && !empty($organigrama['pdf_path'])): ?>
                                    <div class="mb-3">
                                        <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 3rem;"></i>
                                    </div>
                                    <p class="mb-1">
                                        <strong><?= htmlspecialchars($organigrama['titulo']) ?></strong>
                                    </p>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-file-earmark me-1"></i>
                                        <?= htmlspecialchars(basename($organigrama['pdf_path'])) ?>
                                    </p>
                                    <?php if (!empty($organigrama['updated_at'])): ?>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            Última actualización: <?= htmlspecialchars($organigrama['updated_at']) ?>
                                        </small>
                                    <?php endif; ?>
                                    <div class="mt-3">
                                        <a href="<?= '../' . htmlspecialchars($organigrama['pdf_path']) ?>"
                                           class="btn btn-sm btn-outline-primary"
                                           target="_blank" rel="noopener noreferrer">
                                            <i class="bi bi-eye me-1"></i> Ver PDF
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted py-4">
                                        <i class="bi bi-file-earmark-pdf" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">No hay PDF de organigrama cargado. Use el formulario para subir uno.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de edición -->
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-upload me-1"></i>
                                <?= ($organigrama && !empty($organigrama['pdf_path'])) ? 'Reemplazar PDF del organigrama' : 'Subir PDF del organigrama' ?>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data" action="organigrama.php">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                    <div class="mb-3">
                                        <label for="titulo" class="form-label">Título</label>
                                        <input type="text" class="form-control" id="titulo" name="titulo"
                                               value="<?= $organigrama ? htmlspecialchars($organigrama['titulo']) : 'Organigrama 2025-2027' ?>"
                                               required maxlength="200"
                                               placeholder="Título del organigrama">
                                    </div>
                                    <div class="mb-3">
                                        <label for="pdf" class="form-label">
                                            Archivo PDF (máx. <?= UPLOAD_MAX_PDF_MB ?> MB)
                                            <?php if ($organigrama && !empty($organigrama['pdf_path'])): ?>
                                                <small class="text-muted">— dejar vacío para conservar el actual</small>
                                            <?php endif; ?>
                                        </label>
                                        <input type="file" class="form-control" id="pdf" name="pdf"
                                               accept=".pdf"
                                               <?= (!$organigrama || empty($organigrama['pdf_path'])) ? 'required' : '' ?>>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-save me-1"></i>
                                        <?= ($organigrama && !empty($organigrama['pdf_path'])) ? 'Guardar cambios' : 'Subir organigrama' ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    </script>
</body>
</html>
