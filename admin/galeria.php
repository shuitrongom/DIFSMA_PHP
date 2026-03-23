<?php
/**
 * admin/galeria.php — CRUD para Galería Fotográfica (Álbumes e Imágenes)
 *
 * Requisitos: 9.1, 9.2, 9.3, 9.4, 9.5
 */

require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload_handler.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();

// ── Determinar vista: listado de álbumes o detalle de álbum ────────────────────
$albumId = isset($_GET['album_id']) ? (int) $_GET['album_id'] : 0;

// ── Procesamiento POST ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';

    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF inválido. Intente de nuevo.';
        $_SESSION['flash_type']    = 'danger';
        $redirect = $albumId > 0 ? "galeria.php?album_id={$albumId}" : 'galeria.php';
        header("Location: {$redirect}");
        exit;
    }

    // ── CREATE ALBUM ───────────────────────────────────────────────────────────
    if ($action === 'create_album') {
        $nombre = trim($_POST['nombre'] ?? '');
        $fecha  = $_POST['fecha_album'] ?? '';

        if (empty($nombre)) {
            $_SESSION['flash_message'] = 'El nombre del álbum es obligatorio.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: galeria.php');
            exit;
        }

        if (empty($fecha) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $_SESSION['flash_message'] = 'Formato de fecha inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: galeria.php');
            exit;
        }

        if ($fecha > date('Y-m-d')) {
            $_SESSION['flash_message'] = 'La fecha no puede ser mayor al día de hoy.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: galeria.php');
            exit;
        }

        if (!isset($_FILES['portada']) || $_FILES['portada']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_message'] = 'Debe seleccionar una imagen de portada.';
            $_SESSION['flash_type']    = 'warning';
            header('Location: galeria.php');
            exit;
        }

        $upload = handle_upload($_FILES['portada'], 'image');

        if (!$upload['success']) {
            $_SESSION['flash_message'] = $upload['error'];
            $_SESSION['flash_type']    = 'danger';
            header('Location: galeria.php');
            exit;
        }

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO galeria_albumes (nombre, fecha_album, portada_path, activo) VALUES (?, ?, ?, 1)'
            );
            $stmt->execute([$nombre, $fecha, $upload['path']]);

            $_SESSION['flash_message'] = 'Álbum creado correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al guardar en la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: galeria.php');
        exit;
    }

    // ── EDIT ALBUM ─────────────────────────────────────────────────────────────
    if ($action === 'edit_album') {
        $id     = (int) ($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $fecha  = $_POST['fecha_album'] ?? '';

        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID de álbum inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: galeria.php');
            exit;
        }

        if (empty($nombre)) {
            $_SESSION['flash_message'] = 'El nombre del álbum es obligatorio.';
            $_SESSION['flash_type']    = 'warning';
            header("Location: galeria.php?album_id={$id}");
            exit;
        }

        if (empty($fecha) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $_SESSION['flash_message'] = 'Formato de fecha inválido.';
            $_SESSION['flash_type']    = 'danger';
            header("Location: galeria.php?album_id={$id}");
            exit;
        }

        if ($fecha > date('Y-m-d')) {
            $_SESSION['flash_message'] = 'La fecha no puede ser mayor al día de hoy.';
            $_SESSION['flash_type']    = 'warning';
            header("Location: galeria.php?album_id={$id}");
            exit;
        }

        // Obtener álbum actual
        $stmt = $pdo->prepare('SELECT portada_path FROM galeria_albumes WHERE id = ?');
        $stmt->execute([$id]);
        $old = $stmt->fetch();

        if (!$old) {
            $_SESSION['flash_message'] = 'Álbum no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: galeria.php');
            exit;
        }

        $newPortada = $old['portada_path'];

        // Si se envió nueva portada, procesarla
        if (isset($_FILES['portada']) && $_FILES['portada']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = handle_upload($_FILES['portada'], 'image');

            if (!$upload['success']) {
                $_SESSION['flash_message'] = $upload['error'];
                $_SESSION['flash_type']    = 'danger';
                header("Location: galeria.php?album_id={$id}");
                exit;
            }

            $newPortada = $upload['path'];
        }

        try {
            $stmt = $pdo->prepare(
                'UPDATE galeria_albumes SET nombre = ?, fecha_album = ?, portada_path = ? WHERE id = ?'
            );
            $stmt->execute([$nombre, $fecha, $newPortada, $id]);

            // Si se reemplazó la portada, eliminar la anterior
            if ($newPortada !== $old['portada_path'] && !empty($old['portada_path'])) {
                $oldFile = BASE_PATH . '/' . $old['portada_path'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $_SESSION['flash_message'] = 'Álbum actualizado correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al actualizar en la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header("Location: galeria.php?album_id={$id}");
        exit;
    }

    // ── ADD IMAGE to album ─────────────────────────────────────────────────────
    if ($action === 'add_image') {
        $aId = (int) ($_POST['album_id'] ?? 0);

        if ($aId <= 0) {
            $_SESSION['flash_message'] = 'ID de álbum inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: galeria.php');
            exit;
        }

        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_message'] = 'Debe seleccionar una imagen.';
            $_SESSION['flash_type']    = 'warning';
            header("Location: galeria.php?album_id={$aId}");
            exit;
        }

        $upload = handle_upload($_FILES['imagen'], 'image');

        if (!$upload['success']) {
            $_SESSION['flash_message'] = $upload['error'];
            $_SESSION['flash_type']    = 'danger';
            header("Location: galeria.php?album_id={$aId}");
            exit;
        }

        try {
            $stmt = $pdo->prepare('SELECT COALESCE(MAX(orden), 0) + 1 FROM galeria_imagenes WHERE album_id = ?');
            $stmt->execute([$aId]);
            $nextOrden = (int) $stmt->fetchColumn();

            $stmt = $pdo->prepare(
                'INSERT INTO galeria_imagenes (album_id, imagen_path, orden) VALUES (?, ?, ?)'
            );
            $stmt->execute([$aId, $upload['path'], $nextOrden]);

            $_SESSION['flash_message'] = 'Imagen agregada al álbum correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al guardar en la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header("Location: galeria.php?album_id={$aId}");
        exit;
    }

    // ── DELETE IMAGE from album ────────────────────────────────────────────────
    if ($action === 'delete_image') {
        $imgId = (int) ($_POST['image_id'] ?? 0);
        $aId   = (int) ($_POST['album_id'] ?? 0);

        if ($imgId <= 0) {
            $_SESSION['flash_message'] = 'ID de imagen inválido.';
            $_SESSION['flash_type']    = 'danger';
            header("Location: galeria.php?album_id={$aId}");
            exit;
        }

        $stmt = $pdo->prepare('SELECT imagen_path FROM galeria_imagenes WHERE id = ?');
        $stmt->execute([$imgId]);
        $row = $stmt->fetch();

        if (!$row) {
            $_SESSION['flash_message'] = 'Imagen no encontrada.';
            $_SESSION['flash_type']    = 'danger';
            header("Location: galeria.php?album_id={$aId}");
            exit;
        }

        try {
            $stmt = $pdo->prepare('DELETE FROM galeria_imagenes WHERE id = ?');
            $stmt->execute([$imgId]);

            $filePath = BASE_PATH . '/' . $row['imagen_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $_SESSION['flash_message'] = 'Imagen eliminada correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al eliminar de la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header("Location: galeria.php?album_id={$aId}");
        exit;
    }

    // ── DELETE ALBUM (cascade) ─────────────────────────────────────────────────
    if ($action === 'delete_album') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID de álbum inválido.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: galeria.php');
            exit;
        }

        $stmt = $pdo->prepare('SELECT portada_path FROM galeria_albumes WHERE id = ?');
        $stmt->execute([$id]);
        $album = $stmt->fetch();

        if (!$album) {
            $_SESSION['flash_message'] = 'Álbum no encontrado.';
            $_SESSION['flash_type']    = 'danger';
            header('Location: galeria.php');
            exit;
        }

        try {
            // Obtener todas las imágenes del álbum para eliminar archivos
            $stmtImgs = $pdo->prepare('SELECT imagen_path FROM galeria_imagenes WHERE album_id = ?');
            $stmtImgs->execute([$id]);
            $imagenes = $stmtImgs->fetchAll();

            // Eliminar álbum (CASCADE eliminará galeria_imagenes)
            $stmt = $pdo->prepare('DELETE FROM galeria_albumes WHERE id = ?');
            $stmt->execute([$id]);

            // Eliminar archivos de imágenes del servidor
            foreach ($imagenes as $img) {
                $filePath = BASE_PATH . '/' . $img['imagen_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Eliminar portada del servidor
            if (!empty($album['portada_path'])) {
                $portadaPath = BASE_PATH . '/' . $album['portada_path'];
                if (file_exists($portadaPath)) {
                    unlink($portadaPath);
                }
            }

            $_SESSION['flash_message'] = 'Álbum y todas sus imágenes eliminados correctamente.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al eliminar de la base de datos.';
            $_SESSION['flash_type']    = 'danger';
        }

        header('Location: galeria.php');
        exit;
    }
}

// ── Consultar datos ────────────────────────────────────────────────────────────
$currentAlbum = null;
$albumImages  = [];
$albumes      = [];

if ($albumId > 0) {
    // Vista de detalle de álbum
    $stmt = $pdo->prepare('SELECT * FROM galeria_albumes WHERE id = ?');
    $stmt->execute([$albumId]);
    $currentAlbum = $stmt->fetch();

    if ($currentAlbum) {
        $stmt = $pdo->prepare('SELECT * FROM galeria_imagenes WHERE album_id = ? ORDER BY orden ASC');
        $stmt->execute([$albumId]);
        $albumImages = $stmt->fetchAll();
    }
} else {
    // Vista de listado de álbumes con conteo de imágenes
    $stmt = $pdo->query(
        'SELECT a.*, COUNT(i.id) AS num_imagenes
         FROM galeria_albumes a
         LEFT JOIN galeria_imagenes i ON i.album_id = a.id
         GROUP BY a.id
         ORDER BY a.fecha_album DESC, a.id DESC'
    );
    $albumes = $stmt->fetchAll();
}

// ── Flash messages ─────────────────────────────────────────────────────────────
$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType    = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);

$token = csrf_token();

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
    <title>Galería Fotográfica — Panel de Administración DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; }
        .gallery-item { position: relative; border-radius: 8px; overflow: hidden; }
        .gallery-item img { width: 100%; height: 150px; object-fit: cover; }
        .gallery-item .overlay {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: rgba(0,0,0,0.6); padding: 0.3rem; text-align: center;
        }
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
                        <a class="nav-link<?= $s['file'] === 'galeria.php' ? ' active' : '' ?>" href="<?= htmlspecialchars($s['file']) ?>">
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
                <span class="navbar-brand mb-0 h6">
                    <?php if ($currentAlbum): ?>
                        <a href="galeria.php" class="text-decoration-none text-muted">Galería</a>
                        <i class="bi bi-chevron-right mx-1 small"></i>
                        <?= htmlspecialchars($currentAlbum['nombre']) ?>
                    <?php else: ?>
                        Galería Fotográfica
                    <?php endif; ?>
                </span>
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

                <?php if ($albumId > 0 && $currentAlbum): ?>
                <!-- ═══════════════════════════════════════════════════════════ -->
                <!-- VISTA DE DETALLE DE ÁLBUM                                  -->
                <!-- ═══════════════════════════════════════════════════════════ -->
                <div class="row g-4">
                    <!-- Info del álbum + edición -->
                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-pencil-square me-1"></i> Datos del álbum
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data" action="galeria.php?album_id=<?= (int) $currentAlbum['id'] ?>">
                                    <input type="hidden" name="action" value="edit_album">
                                    <input type="hidden" name="id" value="<?= (int) $currentAlbum['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                    <?php if (!empty($currentAlbum['portada_path'])): ?>
                                        <div class="text-center mb-3">
                                            <img src="../<?= htmlspecialchars($currentAlbum['portada_path']) ?>"
                                                 alt="Portada actual" class="img-fluid rounded" style="max-height: 180px;">
                                        </div>
                                    <?php endif; ?>
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre del álbum</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre"
                                               value="<?= htmlspecialchars($currentAlbum['nombre']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="fecha_album" class="form-label">Fecha</label>
                                        <input type="date" class="form-control" id="fecha_album" name="fecha_album"
                                               value="<?= htmlspecialchars($currentAlbum['fecha_album']) ?>" max="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="portada" class="form-label">Nueva portada (opcional)</label>
                                        <input type="file" class="form-control" id="portada" name="portada" accept=".jpg,.jpeg,.png,.webp">
                                    </div>
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="bi bi-pencil me-1"></i> Guardar cambios
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Formulario agregar imagen -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <i class="bi bi-plus-circle me-1"></i> Agregar imagen al álbum
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data" action="galeria.php?album_id=<?= (int) $currentAlbum['id'] ?>">
                                    <input type="hidden" name="action" value="add_image">
                                    <input type="hidden" name="album_id" value="<?= (int) $currentAlbum['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                    <div class="mb-3">
                                        <label for="imagen" class="form-label">Imagen (JPG, PNG, WEBP — máx. 20 MB)</label>
                                        <input type="file" class="form-control" id="imagen" name="imagen" accept=".jpg,.jpeg,.png,.webp" required>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-upload me-1"></i> Subir imagen
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Botón eliminar álbum -->
                        <button type="button" class="btn btn-outline-danger w-100"
                                data-bs-toggle="modal" data-bs-target="#deleteAlbumModal">
                            <i class="bi bi-trash me-1"></i> Eliminar álbum completo
                        </button>
                    </div>

                    <!-- Grid de imágenes del álbum -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-images me-1"></i> Imágenes del álbum
                                <span class="badge bg-secondary ms-1"><?= count($albumImages) ?></span>
                            </div>
                            <div class="card-body">
                                <?php if (empty($albumImages)): ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-image" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">No hay imágenes en este álbum. Use el formulario para agregar una.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="gallery-grid">
                                        <?php foreach ($albumImages as $img): ?>
                                            <div class="gallery-item border">
                                                <img src="../<?= htmlspecialchars($img['imagen_path']) ?>"
                                                     alt="Imagen #<?= (int) $img['id'] ?>">
                                                <div class="overlay">
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteImgModal<?= (int) $img['id'] ?>"
                                                            title="Eliminar imagen">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Modal eliminar imagen -->
                                            <div class="modal fade" id="deleteImgModal<?= (int) $img['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" action="galeria.php?album_id=<?= (int) $currentAlbum['id'] ?>">
                                                            <input type="hidden" name="action" value="delete_image">
                                                            <input type="hidden" name="image_id" value="<?= (int) $img['id'] ?>">
                                                            <input type="hidden" name="album_id" value="<?= (int) $currentAlbum['id'] ?>">
                                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title text-danger">
                                                                    <i class="bi bi-exclamation-triangle me-1"></i> Eliminar imagen
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>¿Está seguro de eliminar esta imagen?</p>
                                                                <img src="../<?= htmlspecialchars($img['imagen_path']) ?>"
                                                                     alt="Imagen a eliminar" class="img-fluid rounded" style="max-height: 200px;">
                                                                <p class="text-muted small mt-2">Esta acción no se puede deshacer.</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-danger">
                                                                    <i class="bi bi-trash me-1"></i> Eliminar
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal eliminar álbum completo -->
                <div class="modal fade" id="deleteAlbumModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="galeria.php">
                                <input type="hidden" name="action" value="delete_album">
                                <input type="hidden" name="id" value="<?= (int) $currentAlbum['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title text-danger">
                                        <i class="bi bi-exclamation-triangle me-1"></i> Eliminar álbum completo
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <p>¿Está seguro de eliminar el álbum <strong><?= htmlspecialchars($currentAlbum['nombre']) ?></strong>?</p>
                                    <p class="text-danger small">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        Se eliminarán todas las imágenes del álbum (<?= count($albumImages) ?>) del servidor. Esta acción no se puede deshacer.
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash me-1"></i> Eliminar álbum
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php elseif ($albumId > 0 && !$currentAlbum): ?>
                <!-- Álbum no encontrado -->
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-1"></i> Álbum no encontrado.
                    <a href="galeria.php" class="alert-link">Volver al listado</a>.
                </div>

                <?php else: ?>
                <!-- ═══════════════════════════════════════════════════════════ -->
                <!-- VISTA DE LISTADO DE ÁLBUMES                                -->
                <!-- ═══════════════════════════════════════════════════════════ -->
                <div class="row g-4">
                    <!-- Formulario crear álbum -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-plus-circle me-1"></i> Crear álbum
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data" action="galeria.php">
                                    <input type="hidden" name="action" value="create_album">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre del álbum</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required
                                               placeholder="Ej: Evento de Navidad 2024">
                                    </div>
                                    <div class="mb-3">
                                        <label for="fecha_album" class="form-label">Fecha del álbum</label>
                                        <input type="date" class="form-control" id="fecha_album" name="fecha_album"
                                               value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="portada" class="form-label">Imagen de portada (JPG, PNG, WEBP — máx. 20 MB)</label>
                                        <input type="file" class="form-control" id="portada" name="portada" accept=".jpg,.jpeg,.png,.webp" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-folder-plus me-1"></i> Crear álbum
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Listado de álbumes -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-camera me-1"></i> Álbumes de la Galería
                                <span class="badge bg-secondary ms-1"><?= count($albumes) ?></span>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($albumes)): ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-camera" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">No hay álbumes registrados. Use el formulario para crear uno.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 80px;">Portada</th>
                                                    <th>Nombre</th>
                                                    <th style="width: 120px;">Fecha</th>
                                                    <th style="width: 80px;">Imágenes</th>
                                                    <th style="width: 60px;">Activo</th>
                                                    <th style="width: 200px;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($albumes as $album): ?>
                                                    <tr>
                                                        <td>
                                                            <?php if (!empty($album['portada_path'])): ?>
                                                                <img src="../<?= htmlspecialchars($album['portada_path']) ?>"
                                                                     alt="<?= htmlspecialchars($album['nombre']) ?>"
                                                                     class="thumb-preview">
                                                            <?php else: ?>
                                                                <span class="text-muted"><i class="bi bi-image"></i></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= htmlspecialchars($album['nombre']) ?></td>
                                                        <td>
                                                            <span class="badge bg-info text-dark">
                                                                <i class="bi bi-calendar-event me-1"></i>
                                                                <?= htmlspecialchars($album['fecha_album']) ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-primary"><?= (int) $album['num_imagenes'] ?></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if ($album['activo']): ?>
                                                                <span class="badge bg-success">Sí</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">No</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <a href="galeria.php?album_id=<?= (int) $album['id'] ?>"
                                                               class="btn btn-sm btn-outline-primary" title="Ver álbum">
                                                                <i class="bi bi-eye"></i> Ver
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#deleteAlbumListModal<?= (int) $album['id'] ?>"
                                                                    title="Eliminar">
                                                                <i class="bi bi-trash"></i> Eliminar
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <!-- Modal eliminar álbum desde listado -->
                                                    <div class="modal fade" id="deleteAlbumListModal<?= (int) $album['id'] ?>" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form method="POST" action="galeria.php">
                                                                    <input type="hidden" name="action" value="delete_album">
                                                                    <input type="hidden" name="id" value="<?= (int) $album['id'] ?>">
                                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title text-danger">
                                                                            <i class="bi bi-exclamation-triangle me-1"></i> Eliminar álbum
                                                                        </h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>¿Está seguro de eliminar el álbum <strong><?= htmlspecialchars($album['nombre']) ?></strong>?</p>
                                                                        <p class="text-danger small">
                                                                            <i class="bi bi-exclamation-circle me-1"></i>
                                                                            Se eliminarán todas las imágenes (<?= (int) $album['num_imagenes'] ?>) del servidor. Esta acción no se puede deshacer.
                                                                        </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                        <button type="submit" class="btn btn-danger">
                                                                            <i class="bi bi-trash me-1"></i> Eliminar
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
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
