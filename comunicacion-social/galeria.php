<?php
/**
 * comunicacion-social/galeria.php — Galería Fotográfica del DIF San Mateo Atenco
 *
 * Consulta galeria_albumes (activo=1) con sus galeria_imagenes (ORDER BY orden ASC)
 * y renderiza tarjetas de álbumes con portada, nombre y fecha.
 * Soporte Lightbox2 para visualizar las imágenes del álbum al hacer clic.
 *
 * Requisitos: 9.6, 9.7
 */

require_once __DIR__ . '/../includes/db.php';

$base_path   = '../';
$active_page = 'comunicacion';
$page_title  = 'Galería — DIF San Mateo Atenco';

// ── Consultar álbumes activos con sus imágenes ───────────────────────────────
$albumes = [];
try {
    $pdo = get_db();

    // Obtener álbumes activos ordenados por fecha descendente
    $stmt = $pdo->prepare(
        'SELECT id, nombre, fecha_album, portada_path
         FROM galeria_albumes
         WHERE activo = 1
         ORDER BY fecha_album DESC'
    );
    $stmt->execute();
    $albumes = $stmt->fetchAll();

    // Para cada álbum, obtener sus imágenes ordenadas
    if (!empty($albumes)) {
        $stmtImg = $pdo->prepare(
            'SELECT imagen_path FROM galeria_imagenes WHERE album_id = ? ORDER BY orden ASC'
        );
        foreach ($albumes as &$album) {
            $stmtImg->execute([$album['id']]);
            $album['imagenes'] = $stmtImg->fetchAll();
        }
        unset($album);
    }
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log('galeria.php PDOException: ' . $e->getMessage());
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

    <!-- Gallery Start -->
    <div class="container-fluid events py-5 bg-light">
        <div class="container py-5">
            <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
                <h4 class="mb-1 d-inline-block" style="font-family:'Montserrat',sans-serif; font-weight:800; letter-spacing:2px; color:rgb(107,98,90);">
                    Galería de Fotos DIF</h4>
                <div style="height:16px; background:rgb(200,16,44); width:23%; margin: 4px auto 24px;"></div>
            </div>

            <?php if (!empty($albumes)): ?>
            <div class="row g-5 justify-content-center">
                <?php foreach ($albumes as $album):
                    $portada = !empty($album['portada_path'])
                        ? $base_path . htmlspecialchars($album['portada_path'], ENT_QUOTES, 'UTF-8')
                        : $base_path . 'img/placeholder.jpg';
                    $albumSlug = 'album-' . (int)$album['id'];
                    $fechaFmt  = date('d M Y', strtotime($album['fecha_album']));
                ?>
                <div class="col-md-6 col-lg-6 col-xl-4 wow fadeIn" data-wow-delay="0.1s">
                    <div class="events-item rounded">
                        <div class="events-inner position-relative">
                            <div class="events-img overflow-hidden img-border-radius position-relative">
                                <img src="<?= $portada ?>" class="img-fluid w-100" alt="<?= htmlspecialchars($album['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                                <div class="event-overlay">
                                    <?php if (!empty($album['imagenes'])): ?>
                                        <?php foreach ($album['imagenes'] as $idx => $img): ?>
                                    <a href="<?= $base_path . htmlspecialchars($img['imagen_path'], ENT_QUOTES, 'UTF-8') ?>" data-lightbox="<?= $albumSlug ?>" data-title="<?= htmlspecialchars($album['nombre'], ENT_QUOTES, 'UTF-8') ?>"><?php if ($idx === 0): ?><i class="fas fa-search-plus text-white fa-2x"></i><?php endif; ?></a>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                    <a href="<?= $portada ?>" data-lightbox="<?= $albumSlug ?>" data-title="<?= htmlspecialchars($album['nombre'], ENT_QUOTES, 'UTF-8') ?>"><i class="fas fa-search-plus text-white fa-2x"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between px-4 py-3 bg-light border-bottom border-primary blog-date-comments">
                                <small class="text-dark"><i class="fas fa-calendar me-1 text-dark"></i> <?= htmlspecialchars($fechaFmt, ENT_QUOTES, 'UTF-8') ?></small>
                            </div>
                        </div>
                        <div class="px-4 pb-4 bg-light rounded-bottom">
                            <div class="blog-text-inner">
                                <a class="h4"><?= htmlspecialchars($album['nombre'], ENT_QUOTES, 'UTF-8') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-4">
                <p style="font-family:'Montserrat',sans-serif; font-weight:500; color:rgba(0,0,0,0.6); font-size:16px;">
                    No hay álbumes disponibles en la galería</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Gallery End -->

    <!-- Pleca Start -->
    <div class="pleca">
        <img src="<?= $base_path ?>img/pleca.png" alt="pleca">
    </div>
    <!-- Pleca End -->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
