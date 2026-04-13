<?php
/**
 * acerca-del-dif/presidencia.php — Página de Presidencia del DIF San Mateo Atenco
 *
 * Consulta la tabla `presidencia` para obtener imagen, nombre, cargo y descripción.
 * Fallback a ../img/Presidente.png si no hay imagen en DB.
 */

require_once __DIR__ . '/../includes/db.php';

$base_path   = '../';
$active_page = 'acerca';
$page_title  = 'Presidente DIF — DIF San Mateo Atenco';

// ── Consultar registro de presidencia ────────────────────────────────────────
$presidente = null;
try {
    $pdo  = get_db();
    $stmt = $pdo->prepare('SELECT imagen_path, nombre, apellidos, cargo, descripcion FROM presidencia LIMIT 1');
    $stmt->execute();
    $presidente = $stmt->fetch();
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log('presidencia.php PDOException: ' . $e->getMessage());
    }
}

// Determinar imagen, nombre, cargo y descripción con fallbacks
$imagen      = $base_path . 'img/Presidente.png';
$nombre      = 'Oscar';
$apellidos   = 'Muñiz Maynez';
$cargo       = 'PRESIDENTE HONORARIO';
$descripcion = '';

if ($presidente) {
    if (!empty($presidente['imagen_path'])) {
        $imagen = htmlspecialchars($base_path . $presidente['imagen_path'], ENT_QUOTES, 'UTF-8');
    }
    if (!empty($presidente['nombre'])) {
        $nombre = $presidente['nombre'];
    }
    if (!empty($presidente['apellidos'])) {
        $apellidos = $presidente['apellidos'];
    }
    if (!empty($presidente['cargo'])) {
        $cargo = $presidente['cargo'];
    }
    if (!empty($presidente['descripcion'])) {
        $descripcion = $presidente['descripcion'];
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

    <!-- Presidencia Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
                <h4 class="mb-1 d-inline-block" style="font-family:'Montserrat',sans-serif; font-weight:800; letter-spacing:2px; color:rgba(255, 255, 255, 1);">
                    PRESIDENTE HONORARIO DEL DIF</h4>
                <div style="height:16px; background:rgb(200,16,44); width:23%; margin: 4px auto 24px;"></div>
            </div>
            <div class="row g-0 justify-content-center wow fadeIn" data-wow-delay="0.1s" style="max-width:900px;margin:0 auto;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.15);">
                <!-- Foto -->
                <div class="col-md-6">
                    <img src="<?= $imagen ?>" class="img-fluid w-100 h-100" style="object-fit:cover;" alt="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <!-- Panel rojo -->
                <div class="col-md-6 d-flex flex-column justify-content-center align-items-center text-center text-white" style="background:rgb(200,16,44);padding:2.5rem 2rem;overflow:hidden;">
                    <h3 style="font-family:'Montserrat',sans-serif;font-weight:700;margin-bottom:0;color:#fff;"><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></h3>
                    <h3 style="font-family:'Montserrat',sans-serif;font-weight:700;margin-bottom:1rem;color:#fff;"><?= htmlspecialchars($apellidos, ENT_QUOTES, 'UTF-8') ?></h3>
                    <p style="font-family:'Montserrat',sans-serif;font-weight:600;font-size:15px;letter-spacing:1px;margin-bottom:1.5rem;"><?= htmlspecialchars($cargo, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php if (!empty($descripcion)): ?>
                    <div style="font-family:'Montserrat',sans-serif;font-size:14px;line-height:1.7;opacity:0.95;margin-bottom:1.5rem;text-align:justify;word-wrap:break-word;overflow-wrap:break-word;max-width:100%;color:#fff !important;">
                        <style>.pres-desc, .pres-desc * { color: #fff !important; }</style>
                        <div class="pres-desc"><?= $descripcion ?></div>
                    </div>
                    <?php endif; ?>
                    <div>
                        <a href="#" class="text-white" style="font-size:1.5rem;" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Presidencia End -->

    <!-- Pleca Start -->
    <div class="pleca">
        <img src="<?= $base_path ?>img/pleca.png" alt="pleca">
    </div>
    <!-- Pleca End -->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
