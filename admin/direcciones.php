<?php
/**
 * admin/direcciones.php — Gestión de Direcciones por departamento
 *
 * Requisitos: 6.1, 6.2, 6.3, 6.5
 */

require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload_handler.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();

// ── Imágenes predeterminadas por departamento ────────────────────────────────
$default_images = [
    'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes'       => 'img/team-3.jpg',
    'Dirección de Atención a Adultos Mayores'                                    => 'img/team-3.jpg',
    'Dirección de Alimentación y Nutrición Familiar'                             => 'img/team-4.jpg',
    'Dirección de Atención a la Discapacidad'                                    => 'img/team-1.jpg',
    'Dirección de Prevención y Bienestar Familiar'                               => 'img/team-1.jpg',
    'Dirección de Servicios Jurídicos – Asistenciales e Igualdad de Género'      => 'img/team-3.jpg',
];
$fallback_image = 'img/team-3.jpg';

// ── Procesamiento POST ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';

    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF inválido. Intente de nuevo.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: direcciones.php');
        exit;
    }

    // ── EDIT: actualizar nombre, cargo e imagen de un departamento ─────────────
    if ($action === 'edit') {
        $id     = (int) ($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $cargo  = trim($_POST['cargo'] ?? '');

        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID de departamento inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: direcciones.php');
            exit;
        }

        if (empty($nombre) || empty($cargo)) {
            $_SESSION['flash_message'] = 'El nombre y el cargo son obligatorios.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: direcciones.php');
            exit;
        }

        // Obtener registro actual
        $stmt = $pdo->prepare('SELECT * FROM direcciones WHERE id = ?');
        $stmt->execute([$id]);
        $current = $stmt->fetch();

        if (!$current) {
            $_SESSION['flash_message'] = 'Departamento no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: direcciones.php');
            exit;
        }

        $imagenPath = $current['imagen_path'];

        // Si se envió un nuevo archivo, procesarlo
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = handle_upload($_FILES['imagen'], 'image');

            if (!$upload['success']) {
                $_SESSION['flash_message'] = $upload['error'];
                $_SESSION['flash_type']    = 'danger';
                header('Location: direcciones.php');
                exit;
            }

            // Eliminar imagen anterior si existe (solo uploads, no las predeterminadas)
            if (!empty($current['imagen_path'])) {
                $oldFile = BASE_PATH . '/' . $current['imagen_path'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $imagenPath = $upload['path'];
        }

        try {
            $stmt = $pdo->prepare(
                'UPDATE direcciones SET nombre = ?, cargo = ?, imagen_path = ? WHERE id = ?'
            );
            $stmt->execute([$nombre, $cargo, $imagenPath, $id]);

            $_SESSION['flash_message'] = 'Departamento actualizado correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al guardar en la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: direcciones.php');
        exit;
    }

    // ── DELETE_IMAGE: eliminar imagen y restaurar predeterminada ────────────────
    if ($action === 'delete_image') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID de departamento inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: direcciones.php');
            exit;
        }

        $stmt = $pdo->prepare('SELECT * FROM direcciones WHERE id = ?');
        $stmt->execute([$id]);
        $current = $stmt->fetch();

        if (!$current) {
            $_SESSION['flash_message'] = 'Departamento no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: direcciones.php');
            exit;
        }

        // Eliminar archivo del servidor si existe
        if (!empty($current['imagen_path'])) {
            $filePath = BASE_PATH . '/' . $current['imagen_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        try {
            // Restaurar imagen predeterminada (NULL en DB, frontend usa fallback)
            $stmt = $pdo->prepare('UPDATE direcciones SET imagen_path = NULL WHERE id = ?');
            $stmt->execute([$id]);

            $_SESSION['flash_message'] = 'Imagen eliminada. Se restauró la imagen predeterminada del departamento.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al actualizar la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: direcciones.php');
        exit;
    }
}

// ── Consultar direcciones actuales ─────────────────────────────────────────────
$stmt = $pdo->query('SELECT * FROM direcciones ORDER BY orden ASC, id ASC');
$direcciones = $stmt->fetchAll();

// ── Flash messages ─────────────────────────────────────────────────────────────
$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType    = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);

// Generar token CSRF para los formularios
$token = csrf_token();

// Secciones del sidebar
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
    <title>Direcciones — Panel de Administración DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css">
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
                        <a class="nav-link<?= $s['file'] === 'direcciones.php' ? ' active' : '' ?>" href="<?= htmlspecialchars($s['file']) ?>">
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
                <span class="navbar-brand mb-0 h6">Direcciones</span>
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

                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-people me-1"></i> Departamentos
                        <span class="badge bg-secondary ms-1"><?= count($direcciones) ?></span>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($direcciones)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-people" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">No hay departamentos registrados.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 90px;">Imagen</th>
                                            <th>Departamento</th>
                                            <th>Nombre</th>
                                            <th>Cargo</th>
                                            <th style="width: 60px;">Orden</th>
                                            <th style="width: 220px;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($direcciones as $dir): ?>
                                            <?php
                                                // Determine display image
                                                if (!empty($dir['imagen_path'])) {
                                                    $imgSrc = '../' . htmlspecialchars($dir['imagen_path'], ENT_QUOTES, 'UTF-8');
                                                    $hasCustomImage = true;
                                                } else {
                                                    $dept = $dir['departamento'];
                                                    $imgSrc = '../' . ($default_images[$dept] ?? $fallback_image);
                                                    $hasCustomImage = false;
                                                }
                                            ?>
                                            <tr>
                                                <td>
                                                    <img src="<?= $imgSrc ?>"
                                                         alt="<?= htmlspecialchars($dir['departamento'], ENT_QUOTES, 'UTF-8') ?>"
                                                         class="thumb-preview">
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($dir['departamento'], ENT_QUOTES, 'UTF-8') ?></strong>
                                                    <?php if (!$hasCustomImage): ?>
                                                        <br><small class="text-muted"><i class="bi bi-info-circle"></i> Imagen predeterminada</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($dir['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($dir['cargo'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td class="text-center"><?= (int) $dir['orden'] ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-warning"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editModal<?= (int) $dir['id'] ?>"
                                                            title="Editar">
                                                        <i class="bi bi-pencil"></i> Editar
                                                    </button>
                                                    <?php if ($hasCustomImage): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deleteImgModal<?= (int) $dir['id'] ?>"
                                                                title="Eliminar imagen">
                                                            <i class="bi bi-trash"></i> Quitar imagen
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>

                                            <!-- Modal Editar -->
                                            <div class="modal fade" id="editModal<?= (int) $dir['id'] ?>" tabindex="-1" aria-labelledby="editLabel<?= (int) $dir['id'] ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" enctype="multipart/form-data" action="direcciones.php">
                                                            <input type="hidden" name="action" value="edit">
                                                            <input type="hidden" name="id" value="<?= (int) $dir['id'] ?>">
                                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editLabel<?= (int) $dir['id'] ?>">
                                                                    Editar: <?= htmlspecialchars($dir['departamento'], ENT_QUOTES, 'UTF-8') ?>
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p class="text-muted small">Imagen actual:</p>
                                                                <div class="text-center mb-3">
                                                                    <img src="<?= $imgSrc ?>"
                                                                         alt="Imagen actual"
                                                                         class="img-fluid rounded" style="max-height: 200px;">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="nombre<?= (int) $dir['id'] ?>" class="form-label">Nombre</label>
                                                                    <input type="text" class="form-control" id="nombre<?= (int) $dir['id'] ?>" name="nombre"
                                                                           value="<?= htmlspecialchars($dir['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                                                           required maxlength="200"
                                                                           placeholder="Nombre completo">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="cargo<?= (int) $dir['id'] ?>" class="form-label">Cargo</label>
                                                                    <input type="text" class="form-control" id="cargo<?= (int) $dir['id'] ?>" name="cargo"
                                                                           value="<?= htmlspecialchars($dir['cargo'], ENT_QUOTES, 'UTF-8') ?>"
                                                                           required maxlength="300"
                                                                           placeholder="Cargo o título">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="imagen<?= (int) $dir['id'] ?>" class="form-label">
                                                                        Nueva imagen (JPG, PNG, WEBP — máx. 20 MB)
                                                                        <small class="text-muted">— dejar vacío para conservar la actual</small>
                                                                    </label>
                                                                    <input type="file" class="form-control" id="imagen<?= (int) $dir['id'] ?>" name="imagen"
                                                                           accept=".jpg,.jpeg,.png,.webp">
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

                                            <?php if ($hasCustomImage): ?>
                                            <!-- Modal Eliminar Imagen -->
                                            <div class="modal fade" id="deleteImgModal<?= (int) $dir['id'] ?>" tabindex="-1" aria-labelledby="deleteImgLabel<?= (int) $dir['id'] ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" action="direcciones.php">
                                                            <input type="hidden" name="action" value="delete_image">
                                                            <input type="hidden" name="id" value="<?= (int) $dir['id'] ?>">
                                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title text-danger" id="deleteImgLabel<?= (int) $dir['id'] ?>">
                                                                    <i class="bi bi-exclamation-triangle me-1"></i> Confirmar eliminación de imagen
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>¿Está seguro de eliminar la imagen personalizada de este departamento?</p>
                                                                <div class="text-center mb-3">
                                                                    <img src="<?= $imgSrc ?>"
                                                                         alt="Imagen a eliminar"
                                                                         class="img-fluid rounded" style="max-height: 150px;">
                                                                </div>
                                                                <p class="text-muted small">
                                                                    <strong><?= htmlspecialchars($dir['departamento'], ENT_QUOTES, 'UTF-8') ?></strong><br>
                                                                    Se restaurará la imagen predeterminada del departamento. El archivo será eliminado del servidor.
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-danger">
                                                                    <i class="bi bi-trash me-1"></i> Eliminar imagen
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
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
