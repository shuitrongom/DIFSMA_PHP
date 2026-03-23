<?php
/**
 * admin/institucion.php — Gestión de la Imagen Institucional
 *
 * Permite subir/reemplazar la imagen institucional que se muestra
 * en la página principal entre Programas y Noticias.
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
        header('Location: institucion.php');
        exit;
    }

    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
        $_SESSION['flash_message'] = 'Debe seleccionar una imagen.';
        $_SESSION['flash_type']    = 'warning';
        header('Location: institucion.php');
        exit;
    }

    $upload = handle_upload($_FILES['imagen'], 'image');

    if (!$upload['success']) {
        $_SESSION['flash_message'] = $upload['error'];
        $_SESSION['flash_type']    = 'danger';
        header('Location: institucion.php');
        exit;
    }

    try {
        // Obtener imagen anterior
        $old = $pdo->query('SELECT imagen_path FROM institucion_banner WHERE id = 1')->fetch();

        $stmt = $pdo->prepare(
            'INSERT INTO institucion_banner (id, imagen_path) VALUES (1, ?)
             ON DUPLICATE KEY UPDATE imagen_path = VALUES(imagen_path)'
        );
        $stmt->execute([$upload['path']]);

        // Borrar archivo anterior si era un upload
        if ($old && !empty($old['imagen_path']) && str_starts_with($old['imagen_path'], 'uploads/')) {
            $oldFile = BASE_PATH . '/' . $old['imagen_path'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        $_SESSION['flash_message'] = 'Imagen institucional actualizada correctamente.';
        $_SESSION['flash_type']    = 'success';
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = APP_DEBUG ? $e->getMessage() : 'Error al guardar en la base de datos.';
        $_SESSION['flash_type']    = 'danger';
    }

    header('Location: institucion.php');
    exit;
}

// ── Consultar registro actual ──────────────────────────────────────────────────
$banner = null;
$tableExists = true;
try {
    $banner = $pdo->query('SELECT imagen_path, updated_at FROM institucion_banner WHERE id = 1')->fetch();
} catch (PDOException $e) {
    $tableExists = false;
}

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
    <title>Imagen Institucional — Panel de Administración DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .preview-img { max-width: 100%; max-height: 350px; object-fit: contain; border-radius: 8px; }
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
                        <a class="nav-link<?= $s['file'] === 'institucion.php' ? ' active' : '' ?>" href="<?= htmlspecialchars($s['file']) ?>">
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
                <span class="navbar-brand mb-0 h6">Imagen Institucional</span>
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

                <?php if (!$tableExists): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Tabla no encontrada.</strong> Ejecute el siguiente SQL en phpMyAdmin para crear la tabla:
                        <pre class="mt-2 bg-light p-2 rounded" style="font-size: 13px;">CREATE TABLE IF NOT EXISTS `institucion_banner` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `imagen_path` VARCHAR(500) NOT NULL DEFAULT 'img/institucion.png',
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `institucion_banner` (`id`, `imagen_path`) VALUES (1, 'img/institucion.png')
ON DUPLICATE KEY UPDATE `id` = `id`;</pre>
                    </div>
                <?php else: ?>

                <div class="row g-4">
                    <!-- Vista previa actual -->
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-card-image me-1"></i> Imagen actual
                            </div>
                            <div class="card-body text-center">
                                <?php
                                    $imgPath = ($banner && !empty($banner['imagen_path']))
                                        ? $banner['imagen_path']
                                        : 'img/institucion.png';
                                ?>
                                <img src="../<?= htmlspecialchars($imgPath) ?>" class="preview-img mb-3"
                                     alt="Imagen institucional actual" id="previewImg">
                                <?php if ($banner && !empty($banner['updated_at'])): ?>
                                    <small class="text-muted d-block">
                                        <i class="bi bi-clock me-1"></i>
                                        Última actualización: <?= htmlspecialchars($banner['updated_at']) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de edición -->
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-upload me-1"></i> Subir / Reemplazar imagen institucional
                            </div>
                            <div class="card-body">
                                <p class="text-muted small">
                                    Esta imagen se muestra en la página principal entre las secciones de Programas y Noticias.
                                    Formatos aceptados: JPG, PNG, WebP (máx. <?= UPLOAD_MAX_IMAGE_MB ?> MB).
                                </p>
                                <form method="POST" enctype="multipart/form-data" action="institucion.php">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                    <div class="mb-3">
                                        <label for="imagen" class="form-label">Seleccionar nueva imagen</label>
                                        <input type="file" class="form-control" id="imagen" name="imagen"
                                               accept="image/jpeg,image/png,image/webp" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-save me-1"></i> Guardar imagen
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php endif; ?>
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
        // Vista previa al seleccionar archivo
        document.getElementById('imagen')?.addEventListener('change', function (e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function (ev) {
                    document.getElementById('previewImg').src = ev.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
