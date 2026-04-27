<?php
/**
 * comunicacion-social/galeria.php — Galería Fotográfica del DIF San Mateo Atenco
 */
require_once __DIR__ . '/../includes/db.php';

$base_path   = '../';
$active_page = 'comunicacion';
$page_title  = 'Galería — DIF San Mateo Atenco';

// Verificar mantenimiento centralizado
$pagina_key = 'galeria';
require_once __DIR__ . '/../includes/mantenimiento_check.php';

$albumes = [];
try {
    $pdo = get_db();
    $stmt = $pdo->prepare('SELECT id, nombre, fecha_album, portada_path FROM galeria_albumes WHERE activo = 1 ORDER BY fecha_album DESC');
    $stmt->execute();
    $albumes = $stmt->fetchAll();

    if (!empty($albumes)) {
        $stmtImg = $pdo->prepare('SELECT imagen_path FROM galeria_imagenes WHERE album_id = ? ORDER BY orden ASC');
        foreach ($albumes as &$album) {
            $stmtImg->execute([$album['id']]);
            $album['imagenes'] = $stmtImg->fetchAll();
        }
        unset($album);
    }
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) error_log('galeria.php: ' . $e->getMessage());
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
                <h4 class="mb-1 d-inline-block" style="font-family:'Montserrat',sans-serif; font-weight:800; letter-spacing:2px; color:rgb(107,98,90);">
                    Galería de Fotos DIF</h4>
                <div style="height:16px; background:rgb(200,16,44); width:23%; margin: 4px auto 24px;"></div>
            </div>

            <?php if (!empty($albumes)): ?>
            <div class="row g-4 justify-content-center">
                <?php foreach ($albumes as $aIdx => $album):
                    $portada = !empty($album['portada_path'])
                        ? $base_path . htmlspecialchars($album['portada_path'], ENT_QUOTES, 'UTF-8')
                        : $base_path . 'img/placeholder.jpg';
                    $fechaFmt = date('d M Y', strtotime($album['fecha_album']));
                ?>
                <div class="col-10 col-sm-6 col-lg-3 wow fadeIn" data-wow-delay="0.1s">
                    <div style="cursor:pointer;" onclick="openGallery(<?= $aIdx ?>)">
                        <div style="overflow:hidden;">
                            <img src="<?= $portada ?>" class="img-fluid w-100" style="aspect-ratio:1/1;object-fit:cover;display:block;" alt="<?= htmlspecialchars($album['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="text-center py-3" style="background:rgb(107,98,90);color:#fff;">
                            <h6 style="font-family:'Montserrat',sans-serif;font-weight:700;margin-bottom:4px;color:#fff;"><?= htmlspecialchars($album['nombre'], ENT_QUOTES, 'UTF-8') ?></h6>
                            <small style="color:#fff !important;"><i class="fas fa-calendar me-1" style="color:#fff !important;"></i> <?= $fechaFmt ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-4">
                <p style="font-family:'Montserrat',sans-serif; font-weight:500; color:rgba(0,0,0,0.6); font-size:16px;">
                    No hay álbumes disponibles</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="pleca"><img src="<?= $base_path ?>img/pleca.png" alt="pleca"></div>

    <!-- Modal Galería -->
    <div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:860px;width:92vw;">
            <div class="modal-content gallery-modal-content">
                <!-- Header -->
                <div class="gallery-modal-header">
                    <div class="gallery-header-left">
                        <h5 id="galleryTitle" class="gallery-modal-title"></h5>
                        <span id="galleryCounter" class="gallery-counter-badge"></span>
                    </div>
                    <button type="button" class="gallery-close-btn" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <!-- Imagen principal -->
                <div class="gallery-main-area">
                    <button class="gallery-arrow gallery-prev" aria-label="Anterior">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="swiper gallery-swiper">
                        <div class="swiper-wrapper" id="gallerySwiperWrapper"></div>
                    </div>
                    <button class="gallery-arrow gallery-next" aria-label="Siguiente">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <!-- Thumbnails -->
                <div class="gallery-thumbs-area">
                    <div class="swiper gallery-thumbs">
                        <div class="swiper-wrapper" id="galleryThumbsWrapper"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

    <style>
    /* ========== Gallery Modal — Premium Thumbs Design ========== */
    .gallery-modal-content {
        background: rgb(107, 98, 90);
        border: none;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 24px 80px rgba(0,0,0,0.7);
    }

    /* Header */
    .gallery-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 22px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .gallery-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .gallery-modal-title {
        color: #fff;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 15px;
        margin: 0;
    }
    .gallery-counter-badge {
        background: rgb(200,16,44);
        color: #fff;
        font-family: 'Montserrat', sans-serif;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        letter-spacing: 0.5px;
    }
    .gallery-close-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: rgba(255,255,255,0.08);
        color: rgba(255,255,255,0.7);
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .gallery-close-btn:hover {
        background: rgb(200,16,44);
        color: #fff;
    }

    /* Main image area */
    .gallery-main-area {
        position: relative;
        display: flex;
        align-items: center;
        background: rgb(107, 98, 90);
    }
    .gallery-swiper {
        width: 100%;
        height: 65vh;
        padding: 0 !important;
    }
    .gallery-swiper .swiper-slide {
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 1 !important;
        transform: scale(1) !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    .gallery-swiper .swiper-slide img {
        max-width: 88%;
        max-height: 62vh;
        object-fit: contain;
        border-radius: 4px;
        transition: opacity 0.3s;
    }

    /* Arrows */
    .gallery-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 20;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: none;
        background: rgba(255,255,255,0.07);
        color: rgba(255,255,255,0.8);
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.25s ease;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .gallery-arrow:hover {
        background: rgb(200,16,44);
        color: #fff;
        transform: translateY(-50%) scale(1.12);
        box-shadow: 0 0 20px rgba(200,16,44,0.4);
    }
    .gallery-prev { left: 14px; }
    .gallery-next { right: 14px; }

    /* Thumbnails strip */
    .gallery-thumbs-area {
        background: rgb(107, 98, 90);
        padding: 12px 16px 14px;
        border-top: 1px solid rgba(255,255,255,0.06);
    }
    .gallery-thumbs {
        width: 100%;
        padding: 0 !important;
    }
    .gallery-thumbs .swiper-slide {
        width: 72px !important;
        height: 52px !important;
        max-width: 72px !important;
        border-radius: 6px;
        overflow: hidden;
        cursor: pointer;
        opacity: 0.4 !important;
        transform: scale(1) !important;
        border: 2px solid transparent;
        transition: opacity 0.3s, border-color 0.3s, transform 0.2s;
    }
    .gallery-thumbs .swiper-slide-thumb-active {
        opacity: 1 !important;
        border-color: rgb(200,16,44);
        transform: scale(1.05) !important;
    }
    .gallery-thumbs .swiper-slide:hover {
        opacity: 0.8 !important;
    }
    .gallery-thumbs .swiper-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .gallery-swiper { height: 50vh; }
        .gallery-swiper .swiper-slide img { max-height: 46vh; max-width: 92%; }
    }
    @media (max-width: 768px) {
        .gallery-swiper { height: 45vh; }
        .gallery-swiper .swiper-slide img { max-height: 40vh; max-width: 96%; }
        .gallery-arrow { width: 38px; height: 38px; font-size: 14px; }
        .gallery-prev { left: 6px; }
        .gallery-next { right: 6px; }
        .gallery-thumbs .swiper-slide { width: 56px !important; height: 42px !important; max-width: 56px !important; }
        .gallery-thumbs-area { padding: 10px 10px 12px; }
    }
    @media (max-width: 576px) {
        .gallery-swiper { height: 36vh; }
        .gallery-swiper .swiper-slide img { max-height: 32vh; max-width: 98%; }
        .gallery-arrow { width: 32px; height: 32px; font-size: 12px; }
        .gallery-prev { left: 4px; }
        .gallery-next { right: 4px; }
        .gallery-thumbs .swiper-slide { width: 44px !important; height: 34px !important; max-width: 44px !important; }
        .gallery-modal-title { font-size: 12px; }
        .gallery-counter-badge { font-size: 10px; padding: 2px 8px; }
        .gallery-modal-header { padding: 10px 14px; }
        .gallery-thumbs-area { padding: 8px 8px 10px; }
    }
    </style>

    <script>
    var albumesData = <?= json_encode(array_map(function($a) use ($base_path) {
        return [
            'nombre' => $a['nombre'],
            'imagenes' => array_map(function($img) use ($base_path) {
                return $base_path . $img['imagen_path'];
            }, $a['imagenes'] ?? [])
        ];
    }, $albumes), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    var gallerySwiper = null;
    var galleryThumbs = null;

    function openGallery(idx) {
        var album = albumesData[idx];
        if (!album || !album.imagenes.length) return;

        document.getElementById('galleryTitle').textContent = album.nombre;

        // Poblar slides principales
        var wrapper = document.getElementById('gallerySwiperWrapper');
        wrapper.innerHTML = '';
        album.imagenes.forEach(function(src) {
            var slide = document.createElement('div');
            slide.className = 'swiper-slide';
            slide.innerHTML = '<img src="' + src + '" alt="" loading="lazy">';
            wrapper.appendChild(slide);
        });

        // Poblar thumbnails
        var thumbsWrapper = document.getElementById('galleryThumbsWrapper');
        thumbsWrapper.innerHTML = '';
        album.imagenes.forEach(function(src) {
            var slide = document.createElement('div');
            slide.className = 'swiper-slide';
            slide.innerHTML = '<img src="' + src + '" alt="">';
            thumbsWrapper.appendChild(slide);
        });

        // Contador
        var counter = document.getElementById('galleryCounter');
        if (counter) counter.textContent = '1 / ' + album.imagenes.length;

        var modal = new bootstrap.Modal(document.getElementById('galleryModal'));
        modal.show();

        document.getElementById('galleryModal').addEventListener('shown.bs.modal', function initSwiper() {
            if (galleryThumbs) galleryThumbs.destroy(true, true);
            if (gallerySwiper) gallerySwiper.destroy(true, true);

            // Inicializar thumbnails primero
            galleryThumbs = new Swiper('.gallery-thumbs', {
                spaceBetween: 8,
                slidesPerView: 'auto',
                freeMode: true,
                watchSlidesProgress: true
            });

            // Inicializar slider principal con thumbs vinculados
            gallerySwiper = new Swiper('.gallery-swiper', {
                slidesPerView: 1,
                spaceBetween: 0,
                centeredSlides: true,
                loop: true,
                grabCursor: true,
                keyboard: { enabled: true },
                navigation: {
                    nextEl: '.gallery-next',
                    prevEl: '.gallery-prev'
                },
                thumbs: {
                    swiper: galleryThumbs
                },
                on: {
                    slideChange: function() {
                        if (counter) counter.textContent = (this.realIndex + 1) + ' / ' + album.imagenes.length;
                    }
                }
            });

            document.getElementById('galleryModal').removeEventListener('shown.bs.modal', initSwiper);
        }, { once: true });
    }

    // Limpiar al cerrar
    document.getElementById('galleryModal').addEventListener('hidden.bs.modal', function() {
        if (gallerySwiper) { gallerySwiper.destroy(true, true); gallerySwiper = null; }
        if (galleryThumbs) { galleryThumbs.destroy(true, true); galleryThumbs = null; }
        document.getElementById('gallerySwiperWrapper').innerHTML = '';
        document.getElementById('galleryThumbsWrapper').innerHTML = '';
    });
    </script>
