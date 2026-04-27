<?php
/**
 * comunicacion-social/noticias.php — Página de Noticias del DIF San Mateo Atenco
 *
 * Reutiliza el mismo componente Slider_Noticias del index.php (Requisito 4/10):
 *   - Consulta noticias_imagenes WHERE activo=1 AND fecha_noticia = CURDATE()
 *   - Renderiza carrusel Swiper de 3 columnas con flechas de navegación
 *   - Muestra mensaje si no hay noticias del día
 */

require_once __DIR__ . '/../includes/db.php';

$base_path   = '../';
$active_page = 'comunicacion';
$page_title  = 'Noticias — DIF San Mateo Atenco';

// ── Consultar noticias_imagenes del día actual ───────────────────────────────
$noticias_images = [];
try {
    $pdo  = get_db();
    $stmt = $pdo->prepare(
        'SELECT imagen_path FROM noticias_imagenes WHERE activo = 1 AND fecha_noticia = CURDATE()'
    );
    $stmt->execute();
    $noticias_images = $stmt->fetchAll();
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log('noticias.php PDOException: ' . $e->getMessage());
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

    <!-- Notice Start -->
    <div class="container-fluid program py-5">
        <div class="container py-5">
            <div class="mx-auto text-center wow fadeIn" style="max-width: 900px;">
                <h4 class="mb-1 d-inline-block" style="font-family:'Montserrat',sans-serif; font-weight:700; letter-spacing:2px; color:rgb(107,98,90);">
                    ÚLTIMAS NOTICIAS INSTITUCIONALES</h4>
                <div style="height:16px; background:rgb(200,16,44); width:23%; margin: 4px auto 24px;"></div>
            </div>

            <?php if (!empty($noticias_images)): ?>
            <div class="swiper notice-swiper" id="swiperNoticias">
                <div class="swiper-wrapper">
                    <?php foreach ($noticias_images as $i => $img): ?>
                    <div class="swiper-slide">
                        <a href="https://www.facebook.com/DifSanMateoAtenco/" target="_blank" rel="noopener noreferrer">
                            <img src="<?= htmlspecialchars($base_path . $img['imagen_path']) ?>" class="notice-img" alt="Noticia <?= $i + 1 ?>" loading="lazy" style="max-height:750px;object-fit:contain;cursor:pointer;">
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="d-flex justify-content-center gap-3 mt-3">
                <button class="notice-btn notice-swiper-prev" aria-label="Anterior">&#10094;</button>
                <button class="notice-btn notice-swiper-next" aria-label="Siguiente">&#10095;</button>
            </div>
            <?php else: ?>
            <div class="text-center py-4">
                <p style="font-family:'Montserrat',sans-serif; font-weight:500; color:rgba(0,0,0,0.6); font-size:16px;">
                    No hay noticias disponibles para el día de hoy</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Notice End -->

    <!-- Pleca Start -->
    <div class="pleca">
        <img src="<?= $base_path ?>img/pleca.png" alt="pleca">
    </div>
    <!-- Pleca End -->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php if (!empty($noticias_images)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var totalSlides = <?= count($noticias_images) ?>;
        var useLoop = totalSlides > 3;
        var useAutoplay = totalSlides > 3 ? { delay: 10000, disableOnInteraction: false } : false;

        new Swiper('#swiperNoticias', {
            loop: useLoop,
            rewind: !useLoop,
            slidesPerView: 1,
            spaceBetween: 4,
            autoplay: useAutoplay,
            navigation: {
                nextEl: '.notice-swiper-next',
                prevEl: '.notice-swiper-prev'
            },
            breakpoints: {
                576: { slidesPerView: Math.min(2, totalSlides) },
                992: { slidesPerView: Math.min(3, totalSlides) }
            }
        });
    });
</script>
<?php endif; ?>
