<?php
/**
 * index.php — Página principal del sitio DIF San Mateo Atenco
 *
 * Incluye header, navbar y footer desde includes/.
 * Consulta slider_principal (activo=1) ordenado por `orden` ASC
 * y renderiza el carrusel con el patrón viewport/dots.
 * Si no hay imágenes en DB, muestra un placeholder.
 */

require_once 'includes/db.php';

$base_path   = '';
$active_page = 'inicio';
$page_title  = 'DIF San Mateo Atenco';

// ── Consultar slider_principal ────────────────────────────────────────────────
$slider_images = [];
try {
    $pdo  = get_db();
    $stmt = $pdo->prepare(
        'SELECT imagen_path FROM slider_principal WHERE activo = 1 ORDER BY orden ASC'
    );
    $stmt->execute();
    $slider_images = $stmt->fetchAll();
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log('index.php slider PDOException: ' . $e->getMessage());
    }
}

// Construir array de rutas para el JS
if (!empty($slider_images)) {
    $images_js = array_map(fn($row) => $row['imagen_path'], $slider_images);
} else {
    $images_js = ['img/carousel-1.jpg'];
}

// ── Consultar slider_comunica del mes actual (DIF Comunica — Swiper 3D) ──────
$comunica_images = [];
try {
    $pdo  = $pdo ?? get_db();
    $stmt = $pdo->prepare(
        'SELECT imagen_path FROM slider_comunica WHERE activo = 1 AND mes = MONTH(CURDATE()) AND anio = YEAR(CURDATE()) ORDER BY orden ASC'
    );
    $stmt->execute();
    $comunica_images = $stmt->fetchAll();

    // Fallback: si no hay imágenes para el mes actual, mostrar todas las activas
    if (empty($comunica_images)) {
        $stmt = $pdo->prepare(
            'SELECT imagen_path FROM slider_comunica WHERE activo = 1 ORDER BY orden ASC'
        );
        $stmt->execute();
        $comunica_images = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log('index.php slider_comunica PDOException: ' . $e->getMessage());
    }
}

// ── Consultar noticias_imagenes del día actual ───────────────────────────────
$noticias_images = [];
try {
    $pdo  = $pdo ?? get_db();
    $stmt = $pdo->prepare(
        'SELECT imagen_path FROM noticias_imagenes WHERE activo = 1 AND fecha_noticia = CURDATE()'
    );
    $stmt->execute();
    $noticias_images = $stmt->fetchAll();
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log('index.php noticias_imagenes PDOException: ' . $e->getMessage());
    }
}

// ── Consultar transparencia_items activos ─────────────────────────────────────
$transparencia_items = [];
try {
    $pdo  = $pdo ?? get_db();
    $stmt = $pdo->prepare(
        'SELECT titulo, url, imagen_path FROM transparencia_items WHERE activo = 1 ORDER BY orden ASC'
    );
    $stmt->execute();
    $transparencia_items = $stmt->fetchAll();
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log('index.php transparencia_items PDOException: ' . $e->getMessage());
    }
}

// ── Consultar programas activos con sus secciones ────────────────────────────
$programas = [];
try {
    $pdo  = $pdo ?? get_db();
    $stmt = $pdo->prepare(
        'SELECT id, nombre, imagen_path FROM programas WHERE activo = 1 ORDER BY orden ASC'
    );
    $stmt->execute();
    $programas = $stmt->fetchAll();

    // Cargar secciones para cada programa
    if (!empty($programas)) {
        $stmtSec = $pdo->prepare(
            'SELECT titulo, contenido FROM programas_secciones WHERE programa_id = ? ORDER BY orden ASC'
        );
        foreach ($programas as &$prog) {
            $stmtSec->execute([$prog['id']]);
            $prog['secciones'] = $stmtSec->fetchAll();
        }
        unset($prog);
    }
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log('index.php programas PDOException: ' . $e->getMessage());
    }
}

// ── Consultar banner institucional ────────────────────────────────────────────
$banner_path = 'img/institucion.png';
try {
    $pdo  = $pdo ?? get_db();
    $stmt = $pdo->prepare('SELECT imagen_path FROM institucion_banner WHERE id = 1');
    $stmt->execute();
    $row = $stmt->fetch();
    if ($row && !empty($row['imagen_path'])) {
        $banner_path = $row['imagen_path'];
    }
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log('index.php institucion_banner PDOException: ' . $e->getMessage());
    }
}

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

    <!-- Carousel Slaider 1 Start -->
    <div class="container-fluid border-bottom bg-white px-0 py-0">
        <div class="container px-0">
            <section class="slider" aria-label="Galería de imágenes">
                <div class="viewport" id="viewport"></div>
                <div class="dots" id="dots" aria-hidden="false" style="margin-top: 12px;"></div>
            </section>
        </div>
    </div>
    <!-- Carousel Slaider 1 End -->

    <script>
    (function () {
        const images = <?= json_encode($images_js, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

        const viewport = document.getElementById('viewport');
        const dotsEl   = document.getElementById('dots');

        // ── Construir slides ──────────────────────────────────────────────────
        function buildSlides(imgs) {
            viewport.innerHTML = '';
            dotsEl.innerHTML   = '';

            imgs.forEach(function (src, i) {
                const slide = document.createElement('div');
                slide.className = 'slide';
                const img = document.createElement('img');
                img.src = src;
                img.alt = 'Imagen del slider ' + (i + 1);
                img.loading = i === 0 ? 'eager' : 'lazy';
                img.style.width = '100%';
                img.style.height = 'auto';
                img.style.display = 'block';
                slide.appendChild(img);
                viewport.appendChild(slide);

                const dot = document.createElement('button');
                dot.className = 'dot' + (i === 0 ? ' active' : '');
                dot.setAttribute('aria-label', 'Ir a imagen ' + (i + 1));
                dot.addEventListener('click', function () {
                    goTo(i);
                    restartAuto();
                });
                dotsEl.appendChild(dot);
            });
        }

        // ── Navegación ────────────────────────────────────────────────────────
        let current = 0;

        function goTo(index) {
            const slides = viewport.children;
            const n = slides.length;
            if (!n) return;
            current = ((index % n) + n) % n;
            viewport.style.transform = 'translateX(-' + (current * 100) + '%)';
            Array.from(dotsEl.children).forEach(function (d, i) {
                d.classList.toggle('active', i === current);
            });
        }

        function next() { goTo(current + 1); }

        // ── Auto-avance ───────────────────────────────────────────────────────
        let timer = null;
        function startAuto()   { stopAuto(); timer = setInterval(next, 7000); }
        function stopAuto()    { if (timer) { clearInterval(timer); timer = null; } }
        function restartAuto() { stopAuto(); startAuto(); }

        // Pausar al pasar el cursor
        viewport.addEventListener('mouseenter', stopAuto);
        viewport.addEventListener('mouseleave', startAuto);

        // ── Inicializar ───────────────────────────────────────────────────────
        buildSlides(images);
        startAuto();
    })();
    </script>

    <!-- Espacio White Start -->
    <div class="container-fluid py-3">
        <div class="container">
        </div>
    </div>
    <!-- Espacio White End -->

<?php if (!empty($comunica_images)): ?>
    <!-- Swiper Carrousel Start -->
    <div class="container-fluid service py-4">
        <div class="container">
            <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
                <h4 class="mb-1 d-inline-block" style="font-family:'Montserrat',sans-serif; font-weight:800; letter-spacing:2px; color:rgb(107,98,90);">
                    DIF COMUNICA</h4>
                <div style="height:16px; background:rgb(200, 16, 44); width:23%; margin: 4px auto 24px;"></div>
            </div>
            <div class="wrap">
                <div class="swiper">
                    <div class="swiper-wrapper">
                        <?php
                        // Swiper coverflow+loop necesita mínimo 5 slides; duplicar si hay menos
                        $slides = $comunica_images;
                        while (count($slides) < 5) {
                            $slides = array_merge($slides, $comunica_images);
                        }
                        foreach ($slides as $i => $img): ?>
                        <div class="swiper-slide"><img src="<?= htmlspecialchars($img['imagen_path']) ?>" alt="DIF Comunica <?= ($i % count($comunica_images)) + 1 ?>"></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Swiper Carrousel End -->
<?php endif; ?>

    <!-- Programs DIF Start -->
    <div class="container-fluid program py-4">
        <div class="container py-4">
            <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
                <h4 class="mb-1 d-inline-block" style="font-family:'Montserrat',sans-serif; font-weight:800; letter-spacing:2px; color:rgb(107,98,90);">
                    TODOS NUESTROS PROGRAMAS</h4>
                <div style="height:16px; background:rgb(200,16,44); width:23%; margin: 4px auto 24px;"></div>
            </div>

            <?php if (!empty($programas)): ?>
            <div class="row g-3 g-md-4 justify-content-center" style="max-width:960px;margin:0 auto;">
                <?php foreach ($programas as $pIdx => $programa): ?>
                <div class="col-10 col-sm-6 col-lg-4 wow fadeIn" data-wow-delay="<?= number_format(0.1 + ($pIdx % 3) * 0.2, 1) ?>s">
                    <div class="events-item">
                        <div class="events-inner position-relative">
                            <div class="events-img overflow-hidden position-relative">
                                <img src="<?= htmlspecialchars($programa['imagen_path'] ?? 'img/placeholder.jpg') ?>" class="img-fluid w-100" alt="<?= htmlspecialchars($programa['nombre']) ?>">
                            </div>
                        </div>
                        <div class="events-text p-0 border-0 rounded-bottom" style="background:transparent;">
                            <?php if (!empty($programa['secciones'])): ?>
                            <div class="dropdown">
                                <button class="btn p-0 w-100 dropdown-toggle btn-ver-programas" type="button"
                                    id="dropdownPrograma<?= $programa['id'] ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="img/btn_ver_programas.png" alt="Ver Programas" class="img-fluid w-100" style="display:block;">
                                </button>
                                <ul class="dropdown-menu p-3" aria-labelledby="dropdownPrograma<?= $programa['id'] ?>" style="width: 300px;">
                                    <div class="accordion" id="accordionPrograma<?= $programa['id'] ?>">
                                        <?php foreach ($programa['secciones'] as $sIdx => $seccion): ?>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingP<?= $programa['id'] ?>S<?= $sIdx ?>">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapseP<?= $programa['id'] ?>S<?= $sIdx ?>"
                                                    aria-expanded="false"
                                                    aria-controls="collapseP<?= $programa['id'] ?>S<?= $sIdx ?>">
                                                    <?= htmlspecialchars($seccion['titulo']) ?>
                                                </button>
                                            </h2>
                                            <div id="collapseP<?= $programa['id'] ?>S<?= $sIdx ?>"
                                                class="accordion-collapse collapse"
                                                aria-labelledby="headingP<?= $programa['id'] ?>S<?= $sIdx ?>"
                                                data-bs-parent="#accordionPrograma<?= $programa['id'] ?>">
                                                <div class="accordion-body">
                                                    <?= $seccion['contenido'] ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </ul>
                            </div>
                            <?php else: ?>
                            <div>
                                <img src="img/btn_ver_programas.png" alt="Ver Programas" class="img-fluid w-100" style="display:block;cursor:default;">
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-4">
                <p style="font-family:'Montserrat',sans-serif; font-weight:500; color:rgba(0,0,0,0.6); font-size:16px;">
                    No hay programas disponibles</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Programs DIF End -->

    <!-- Team Start -->
    <div class="container-fluid program py-5">
        <div class="container py-5">
            <div class="row g-3 g-md-4 justify-content-center">
                <div class="col-md-6 col-lg-4 col-xl-12 wow fadeIn" data-wow-delay="0.3s">
                    <div>
                        <a href="#">
                            <img src="<?= htmlspecialchars($banner_path) ?>" class="img-fluid" alt="Institución DIF">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Team End -->

    <!-- Notice Start -->
    <div class="container-fluid service py-4">
        <div class="container py-4">
            <div class="mx-auto text-center wow fadeIn" style="max-width: 900px;">
                <h4 class="mb-1 d-inline-block" style="font-family:'Montserrat',sans-serif; font-weight:700; letter-spacing:2px; color:rgb(107,98,90);">
                    ÚLTIMAS NOTICIAS INSTITUCIONALES</h4>
                <div style="height:16px; background:rgb(200,16,44); width:23%; margin: 4px auto 24px;"></div>
            </div>

            <?php if (!empty($noticias_images)): ?>
            <?php
            // Swiper loop con 3 slidesPerView necesita mínimo 7 slides (2*slidesPerView+1)
            $notice_slides = $noticias_images;
            while (count($notice_slides) < 7) {
                $notice_slides = array_merge($notice_slides, $noticias_images);
            }
            ?>
            <div class="position-relative">
                <div class="swiper notice-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($notice_slides as $i => $img): ?>
                        <div class="swiper-slide"><img src="<?= htmlspecialchars($img['imagen_path']) ?>" class="notice-img" alt="Noticia <?= ($i % count($noticias_images)) + 1 ?>"></div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="d-flex justify-content-center gap-3 mt-3">
                    <button class="notice-btn" id="noticePrev" aria-label="Anterior">&#10094;</button>
                    <button class="notice-btn" id="noticeNext" aria-label="Siguiente">&#10095;</button>
                </div>
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

    <!-- Trasparencia Start -->
    <div class="container-fluid program py-4">
        <div class="container py-4">
            <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
                <h4 class="mb-1 d-inline-block" style="font-family:'Montserrat',sans-serif; font-weight:700; letter-spacing:2px; color:rgb(107,98,90);">
                    TRANSPARENCIA</h4>
                <div style="height:16px; background:rgb(200,16,44); width:23%; margin: 4px auto 24px;"></div>
            </div>

            <?php if (!empty($transparencia_items)): ?>
            <div class="row g-4 justify-content-center" style="max-width:960px;margin:0 auto;">
                <?php foreach ($transparencia_items as $tIdx => $tItem): ?>
                <div class="col-10 col-sm-6 col-lg-4 wow fadeIn" data-wow-delay="<?= number_format(0.1 + ($tIdx % 3) * 0.2, 1) ?>s">
                    <div style="text-align:center;">
                        <div>
                                <?php
                                $is_external = (strpos($tItem['url'], 'http') === 0);
                                $href = $is_external ? htmlspecialchars($tItem['url']) : htmlspecialchars($tItem['url']);
                                $target = $is_external ? ' target="_blank" rel="noopener noreferrer"' : '';
                                ?>
                                <a href="<?= $href ?>"<?= $target ?>>
                                    <img src="<?= htmlspecialchars($tItem['imagen_path'] ?? 'img/placeholder.jpg') ?>" class="img-fluid" style="max-width:180px;" alt="<?= htmlspecialchars($tItem['titulo']) ?>">
                                </a>
                            <div class="text-center py-3">
                                <h6 style="color:rgb(107,98,90);"><?= htmlspecialchars($tItem['titulo']) ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-4">
                <p style="font-family:'Montserrat',sans-serif; font-weight:500; color:rgba(0,0,0,0.6); font-size:16px;">
                    No hay contenido de transparencia disponible</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Trasparencia End -->

    <!-- Pleca Start -->
    <div class="pleca">
        <img src="img/pleca.png" alt="pleca">
    </div>
    <!-- Pleca End -->

<?php require_once 'includes/footer.php'; ?>

<?php if (!empty($comunica_images)): ?>
<script>
    var swiper = new Swiper('.swiper:not(.notice-swiper)', {
        effect: 'coverflow',
        grabCursor: true,
        centeredSlides: true,
        loop: true,
        speed: 700,
        slidesPerView: 'auto',
        coverflowEffect: {
            rotate: 0,
            stretch: 0,
            depth: 200,
            modifier: 2.5,
            slideShadows: false
        },
        autoplay: {
            delay: 3200,
            disableOnInteraction: false
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true
        },
        keyboard: {
            enabled: true,
            onlyInViewport: true
        }
    });
</script>
<?php endif; ?>

<?php if (!empty($noticias_images)): ?>
<script>
    var noticeSwiper = new Swiper('.notice-swiper', {
        loop: true,
        slidesPerView: 1,
        spaceBetween: 16,
        autoplay: {
            delay: 12000,
            disableOnInteraction: false
        },
        navigation: {
            nextEl: '#noticeNext',
            prevEl: '#noticePrev'
        },
        breakpoints: {
            576: { slidesPerView: 2 },
            992: { slidesPerView: 3 }
        }
    });
</script>
<?php endif; ?>
