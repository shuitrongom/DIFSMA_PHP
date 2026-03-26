<?php
/**
 * tramites/_tramite_template.php — Plantilla compartida para páginas de trámites
 *
 * Variables requeridas antes de incluir este archivo:
 *   $tramite_slug     — string: slug del trámite (e.g. 'PMPNNA')
 *   $default_image    — string: ruta relativa a la imagen fallback (e.g. 'img/tram_serv_procuraduria.png')
 *
 * Requisitos: 8.4, 8.5
 */

require_once __DIR__ . '/../includes/db.php';

$base_path   = '../';
$active_page = 'tramites';

// ── Consultar registro del trámite ──────────────────────────────────────────
$tramite = null;
try {
    $pdo  = get_db();
    $stmt = $pdo->prepare('SELECT titulo, imagen_path, contenido FROM tramites WHERE slug = ?');
    $stmt->execute([$tramite_slug]);
    $tramite = $stmt->fetch();
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log("tramite {$tramite_slug} PDOException: " . $e->getMessage());
    }
}

// Determinar título, imagen y contenido con fallbacks
$titulo    = $tramite_slug;
$imagen    = $base_path . $default_image;
$contenido = '';

if ($tramite) {
    if (!empty($tramite['titulo'])) {
        $titulo = $tramite['titulo'];
    }
    if (!empty($tramite['imagen_path'])) {
        $imagen = htmlspecialchars($base_path . $tramite['imagen_path'], ENT_QUOTES, 'UTF-8');
    }
    if (!empty($tramite['contenido'])) {
        $contenido = $tramite['contenido'];
    }
}

$page_title = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') . ' — DIF San Mateo Atenco';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

    <!-- Blog Start-->
    <div class="container-fluid blog py-5">
        <div class="container py-5">
            <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 600px;">
                <h4 class="mb-1 d-inline-block" style="font-family:'Montserrat',sans-serif; font-weight:800; letter-spacing:2px; color:rgb(107,98,90);">
                    <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
                <div style="height:16px; background:rgb(200,16,44); width:23%; margin: 4px auto 24px;"></div>
            </div>
            <div class="row g-5 justify-content-center">
                <div class="col-md-6 col-lg-6 col-xl-10 wow fadeIn" data-wow-delay="0.5s">
                    <div class="blog-item rounded-bottom">
                        <div class="overflow-hidden position-relative img-border-radius" style="background:#fff;">
                            <img src="<?= $imagen ?>" class="img-fluid w-100" alt="<?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="d-flex justify-content-between px-4 py-3" style="background:#fff;border-bottom:3px solid rgba(0,0,0,0.7);">
                            <small style="color:rgba(0,0,0,0.7);"><i class="fas fa-calendar me-1" style="color:rgba(0,0,0,0.7);"></i> <?= date('Y') ?></small>
                        </div>
                        <div class="px-4 pb-4 rounded-bottom" style="background:rgb(107,98,90);">
                            <div class="blog-text-inner" style="padding-top:1rem;">
                                <style>.blog-text-inner,.blog-text-inner *{color:#fff !important;}</style>
                                <?= $contenido ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Blog End-->

    <!-- Pleca Start -->
    <div class="pleca">
        <img src="<?= $base_path ?>img/pleca.png" alt="pleca">
    </div>
    <!-- Pleca End -->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
